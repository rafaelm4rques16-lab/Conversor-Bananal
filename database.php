<?php
/**
 * Configuração de Banco de Dados via PDO
 * Suporta MySQL com Fallback Automático para SQLite
 * Conversor Bananal Pro
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.use_strict_mode', '1');
    ini_set('session.cookie_httponly', '1');
    session_start();
}

const DB_HOST    = '127.0.0.1';
const DB_PORT    = '3306';
const DB_NAME    = 'bananal_pro';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8mb4';

function getDB(): PDO
{
    static $pdo = null;
    if ($pdo !== null) {
        return $pdo;
    }

    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    // 1. Tenta MySQL
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci");
        initTables($pdo, 'mysql');
        return $pdo;
    } catch (PDOException $ex) {
        // Se banco não existir, tenta criar
        if ($ex->getCode() === 1049) {
            try {
                $rootDsn = sprintf('mysql:host=%s;port=%s;charset=%s', DB_HOST, DB_PORT, DB_CHARSET);
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                initTables($pdo, 'mysql');
                return $pdo;
            } catch (Throwable $t) {}
        }
    }

    // 2. Fallback Transparente para SQLite (Zero configuração prévia necessária)
    $dbDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database';
    if (!is_dir($dbDir)) {
        mkdir($dbDir, 0755, true);
    }
    $sqliteFile = $dbDir . DIRECTORY_SEPARATOR . 'bananal_pro.sqlite';
    $pdo = new PDO('sqlite:' . $sqliteFile, null, null, $options);
    $pdo->exec("PRAGMA journal_mode = WAL;");
    initTables($pdo, 'sqlite');
    return $pdo;
}

function initTables(PDO $pdo, string $driver): void
{
    static $done = false;
    if ($done) return;

    if ($driver === 'mysql') {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                nome VARCHAR(100) NOT NULL,
                email VARCHAR(150) NOT NULL UNIQUE,
                senha VARCHAR(255) NOT NULL,
                cargo VARCHAR(20) NOT NULL DEFAULT 'usuario',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS conversoes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT UNSIGNED NULL,
                session_id VARCHAR(128) NOT NULL,
                nome_original VARCHAR(255) NOT NULL,
                formato_origem VARCHAR(32) NOT NULL,
                tamanho_origem BIGINT UNSIGNED NOT NULL,
                formato_destino VARCHAR(32) NOT NULL,
                arquivo_convertido VARCHAR(255) NULL,
                tamanho_convertido BIGINT UNSIGNED NULL,
                categoria VARCHAR(50) NOT NULL DEFAULT 'geral',
                status VARCHAR(32) NOT NULL DEFAULT 'concluido',
                token_download VARCHAR(64) NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS transcricoes (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT UNSIGNED NULL,
                titulo VARCHAR(255) NOT NULL,
                fonte_tipo VARCHAR(50) NOT NULL, -- 'audio', 'video', 'youtube', 'ao_vivo'
                fonte_url VARCHAR(500) NULL,
                texto_transcricao LONGTEXT NOT NULL,
                legendas_srt LONGTEXT NULL,
                sumario LONGTEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

            CREATE TABLE IF NOT EXISTS auditoria (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT UNSIGNED NULL,
                acao VARCHAR(100) NOT NULL,
                detalhes TEXT NULL,
                ip VARCHAR(45) NOT NULL,
                user_agent TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
        ");
    } else {
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                nome TEXT NOT NULL,
                email TEXT NOT NULL UNIQUE,
                senha TEXT NOT NULL,
                cargo TEXT NOT NULL DEFAULT 'usuario',
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS conversoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NULL,
                session_id TEXT NOT NULL,
                nome_original TEXT NOT NULL,
                formato_origem TEXT NOT NULL,
                tamanho_origem INTEGER NOT NULL,
                formato_destino TEXT NOT NULL,
                arquivo_convertido TEXT NULL,
                tamanho_convertido INTEGER NULL,
                categoria TEXT NOT NULL DEFAULT 'geral',
                status TEXT NOT NULL DEFAULT 'concluido',
                token_download TEXT NOT NULL UNIQUE,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS transcricoes (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NULL,
                titulo TEXT NOT NULL,
                fonte_tipo TEXT NOT NULL,
                fonte_url TEXT NULL,
                texto_transcricao TEXT NOT NULL,
                legendas_srt TEXT NULL,
                sumario TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );

            CREATE TABLE IF NOT EXISTS auditoria (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                usuario_id INTEGER NULL,
                acao TEXT NOT NULL,
                detalhes TEXT NULL,
                ip TEXT NOT NULL,
                user_agent TEXT NULL,
                created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Usuário administrador inicial com Bcrypt
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = 'admin@bananal.com' LIMIT 1");
    $stmt->execute();
    if (!$stmt->fetch()) {
        $hash = password_hash('bananal123', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, cargo) VALUES ('Administrador Bananal', 'admin@bananal.com', :s, 'admin')");
        $insert->execute([':s' => $hash]);
    }

    $done = true;
}

function logAudit(string $acao, string $detalhes = ''): void
{
    try {
        $pdo = getDB();
        $uid = $_SESSION['usuario']['id'] ?? null;
        $ip  = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $ua  = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido', 0, 255);

        $stmt = $pdo->prepare("INSERT INTO auditoria (usuario_id, acao, detalhes, ip, user_agent) VALUES (:u, :a, :d, :i, :ua)");
        $stmt->execute([
            ':u'  => $uid,
            ':a'  => $acao,
            ':d'  => $detalhes,
            ':i'  => $ip,
            ':ua' => $ua
        ]);
    } catch (Throwable $e) {}
}

function getSessionId(): string
{
    if (!isset($_SESSION['bananal_session_token'])) {
        $_SESSION['bananal_session_token'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['bananal_session_token'];
}
