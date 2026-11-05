<?php
/**
 * Configuração de Conexão com Banco de Dados via PDO
 * Suporta MySQL com Fallback Inteligente para SQLite
 * Conversor Bananal
 */

declare(strict_types=1);

if (!defined('BANANAL_ACCESS')) {
    define('BANANAL_ACCESS', true);
}

// Configurações do MySQL
const DB_HOST    = '127.0.0.1';
const DB_PORT    = '3306';
const DB_NAME    = 'conversor_bananal';
const DB_USER    = 'root';
const DB_PASS    = '';
const DB_CHARSET = 'utf8mb4';

/**
 * Retorna uma instância ativa do PDO (MySQL ou Fallback SQLite)
 */
function getDBConnection(): PDO
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

    // Tentativa 1: MySQL via PDO
    try {
        $dsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', DB_HOST, DB_PORT, DB_NAME, DB_CHARSET);
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        $pdo->exec("SET NAMES " . DB_CHARSET . " COLLATE utf8mb4_unicode_ci");
        initDatabaseTables($pdo, 'mysql');
        return $pdo;
    } catch (PDOException $mysqlEx) {
        // Se o banco de dados não existir no MySQL, tenta criá-lo
        if ($mysqlEx->getCode() === 1049) {
            try {
                $rootDsn = sprintf('mysql:host=%s;port=%s;charset=%s', DB_HOST, DB_PORT, DB_CHARSET);
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS, $options);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                initDatabaseTables($pdo, 'mysql');
                return $pdo;
            } catch (Throwable $e) {
                // Cai para fallback SQLite abaixo
            }
        }
    }

    // Tentativa 2: Fallback transparente para PDO SQLite (para rodar imediatamente sem depender do MySQL ativo)
    try {
        $dbDir = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'database';
        if (!is_dir($dbDir)) {
            mkdir($dbDir, 0755, true);
        }
        $sqlitePath = $dbDir . DIRECTORY_SEPARATOR . 'bananal.sqlite';
        $pdo = new PDO('sqlite:' . $sqlitePath, null, null, $options);
        $pdo->exec("PRAGMA foreign_keys = ON; PRAGMA journal_mode = WAL;");
        initDatabaseTables($pdo, 'sqlite');
        return $pdo;
    } catch (PDOException $sqliteEx) {
        error_log("[Conversor Bananal] Falha crítica de conexão PDO: " . $sqliteEx->getMessage());
        throw new PDOException("Não foi possível estabelecer conexão com MySQL nem SQLite.");
    }
}

/**
 * Cria tabelas e dados iniciais se não existirem
 */
function initDatabaseTables(PDO $pdo, string $driver): void
{
    static $initialized = false;
    if ($initialized) return;

    if ($driver === 'mysql') {
        // Tabelas MySQL
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS `usuarios` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `nome` VARCHAR(100) NOT NULL,
              `email` VARCHAR(150) NOT NULL UNIQUE,
              `senha` VARCHAR(255) NOT NULL,
              `avatar` VARCHAR(255) NULL,
              `cargo` ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `updated_at` DATETIME NULL ON UPDATE CURRENT_TIMESTAMP,
              INDEX `idx_email` (`email`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `conversoes` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `usuario_id` INT UNSIGNED NULL,
              `session_id` VARCHAR(128) NOT NULL,
              `nome_original` VARCHAR(255) NOT NULL,
              `formato_origem` VARCHAR(32) NOT NULL,
              `tamanho_origem` BIGINT UNSIGNED NOT NULL,
              `formato_destino` VARCHAR(32) NOT NULL,
              `arquivo_convertido` VARCHAR(255) NULL,
              `tamanho_convertido` BIGINT UNSIGNED NULL,
              `status` ENUM('pendente', 'processando', 'concluido', 'erro') NOT NULL DEFAULT 'pendente',
              `mensagem_erro` TEXT NULL,
              `token_download` VARCHAR(64) NOT NULL UNIQUE,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              `concluido_em` DATETIME NULL,
              INDEX `idx_session` (`session_id`),
              INDEX `idx_token` (`token_download`),
              INDEX `idx_usuario` (`usuario_id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

            CREATE TABLE IF NOT EXISTS `auditoria` (
              `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
              `usuario_id` INT UNSIGNED NULL,
              `acao` VARCHAR(100) NOT NULL,
              `detalhes` TEXT NULL,
              `ip` VARCHAR(45) NOT NULL,
              `user_agent` TEXT NULL,
              `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
              INDEX `idx_auditoria_usuario` (`usuario_id`),
              INDEX `idx_auditoria_acao` (`acao`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ");
    } else {
        // Tabelas SQLite
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usuarios (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              nome TEXT NOT NULL,
              email TEXT NOT NULL UNIQUE,
              senha TEXT NOT NULL,
              avatar TEXT,
              cargo TEXT NOT NULL DEFAULT 'usuario',
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              updated_at DATETIME
            );

            CREATE TABLE IF NOT EXISTS conversoes (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              usuario_id INTEGER,
              session_id TEXT NOT NULL,
              nome_original TEXT NOT NULL,
              formato_origem TEXT NOT NULL,
              tamanho_origem INTEGER NOT NULL,
              formato_destino TEXT NOT NULL,
              arquivo_convertido TEXT,
              tamanho_convertido INTEGER,
              status TEXT NOT NULL DEFAULT 'pendente',
              mensagem_erro TEXT,
              token_download TEXT NOT NULL UNIQUE,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
              concluido_em DATETIME
            );

            CREATE TABLE IF NOT EXISTS auditoria (
              id INTEGER PRIMARY KEY AUTOINCREMENT,
              usuario_id INTEGER,
              acao TEXT NOT NULL,
              detalhes TEXT,
              ip TEXT NOT NULL,
              user_agent TEXT,
              created_at DATETIME DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    // Insere usuário administrador de testes caso não exista (senha: bananal123 com Bcrypt)
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = 'admin@bananal.com' LIMIT 1");
    $stmtCheck->execute();
    if (!$stmtCheck->fetch()) {
        $hashBcrypt = password_hash('bananal123', PASSWORD_BCRYPT);
        $stmtAdmin = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, cargo, created_at)
            VALUES ('Administrador Bananal', 'admin@bananal.com', :senha, 'admin', datetime('now'))
        ");
        if ($driver === 'mysql') {
            $stmtAdmin = $pdo->prepare("
                INSERT INTO usuarios (nome, email, senha, cargo, created_at)
                VALUES ('Administrador Bananal', 'admin@bananal.com', :senha, 'admin', NOW())
            ");
        }
        $stmtAdmin->execute([':senha' => $hashBcrypt]);
    }

    $initialized = true;
}

/**
 * Registra eventos na trilha de auditoria
 */
function registrarAuditoria(string $acao, string $detalhes = '', ?int $usuarioId = null): void
{
    try {
        $pdo = getDBConnection();
        $ip = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
        $agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido', 0, 255);

        if ($usuarioId === null && isset($_SESSION['usuario']['id'])) {
            $usuarioId = (int)$_SESSION['usuario']['id'];
        }

        $stmt = $pdo->prepare("
            INSERT INTO auditoria (usuario_id, acao, detalhes, ip, user_agent)
            VALUES (:uid, :acao, :detalhes, :ip, :agent)
        ");
        $stmt->execute([
            ':uid'      => $usuarioId,
            ':acao'     => $acao,
            ':detalhes' => $detalhes,
            ':ip'       => $ip,
            ':agent'    => $agent
        ]);
    } catch (Throwable $e) {
        error_log("[Conversor Bananal] Erro ao registrar auditoria: " . $e->getMessage());
    }
}
