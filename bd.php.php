<?php
/**
 * Conversor Bananal 🍌
 * Arquivo de Conexão com o Banco de Dados (bd.php)
 * Utiliza PDO para segurança total contra SQL Injections.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ==============================================================================
// CONFIGURAÇÕES DO BANCO DE DADOS (MySQL) - INFINITYFREE
// ==============================================================================
$db_host = 'sql103.infinityfree.com';[cite: 5]
$db_port = '3306';[cite: 5]
$db_user = 'if0_43364187';[cite: 5]
$db_pass = 'vnwWn2wW35M';[cite: 6]
$db_name = 'if0_43364187_conversor';[cite: 6]

$pdo = null;
$usando_sqlite_fallback = false;

try {
    // Tenta conectar diretamente ao MySQL do InfinityFree
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::ATTR_TIMEOUT => 4
    ]);

    inicializar_tabelas_mysql($pdo);

} catch (Exception $e_direct) {
    try {
        $dsn_server = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
        $pdo_server = new PDO($dsn_server, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
            PDO::ATTR_TIMEOUT => 2
        ]);

        $pdo_server->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

        $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
        $pdo = new PDO($dsn, $db_user, $db_pass, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        inicializar_tabelas_mysql($pdo);

    } catch (Exception $e_mysql) {
        try {
            $sqlite_file = __DIR__ . '/conversor_bananal.sqlite';
            $pdo = new PDO("sqlite:" . $sqlite_file, null, null, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]);
            $usando_sqlite_fallback = true;
            inicializar_tabelas_sqlite($pdo);
        } catch (Exception $e_sqlite) {
            die("Erro crítico na conexão com o banco de dados: " . htmlspecialchars($e_direct->getMessage()));
        }
    }
}

/**
 * Cria as tabelas necessárias no MySQL
 */
function inicializar_tabelas_mysql(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `senha` VARCHAR(255) NOT NULL,
        `avatar` VARCHAR(255) DEFAULT 'banana',
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `conversoes` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NULL,
        `nome_original` VARCHAR(255) NOT NULL,
        `formato_origem` VARCHAR(30) NOT NULL,
        `formato_destino` VARCHAR(30) NOT NULL,
        `tamanho_original` INT NOT NULL DEFAULT 0,
        `tamanho_convertido` INT NOT NULL DEFAULT 0,
        `caminho_arquivo` VARCHAR(255) NULL,
        `status` ENUM('sucesso', 'falha') DEFAULT 'sucesso',
        `ip_address` VARCHAR(45) NULL,
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_usuario_id (`usuario_id`),
        INDEX idx_criado_em (`criado_em`),
        CONSTRAINT `fk_conversoes_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `logs_auditoria` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `usuario_id` INT NULL,
        `usuario_nome` VARCHAR(100) NULL,
        `acao` VARCHAR(60) NOT NULL,
        `detalhes` TEXT NULL,
        `ip_address` VARCHAR(45) NULL,
        `user_agent` VARCHAR(255) NULL,
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_log_usuario (`usuario_id`),
        INDEX idx_log_acao (`acao`),
        INDEX idx_log_criado_em (`criado_em`),
        CONSTRAINT `fk_logs_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios`(`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

    criar_usuario_padrao_se_necessario($pdo);
}

function inicializar_tabelas_sqlite(PDO $pdo): void {
    $pdo->exec("CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `nome` TEXT NOT NULL,
        `email` TEXT NOT NULL UNIQUE,
        `senha` TEXT NOT NULL,
        `avatar` TEXT DEFAULT 'banana',
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `conversoes` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `usuario_id` INTEGER NULL,
        `nome_original` TEXT NOT NULL,
        `formato_origem` TEXT NOT NULL,
        `formato_destino` TEXT NOT NULL,
        `tamanho_original` INTEGER NOT NULL DEFAULT 0,
        `tamanho_convertido` INTEGER NOT NULL DEFAULT 0,
        `caminho_arquivo` TEXT NULL,
        `status` TEXT DEFAULT 'sucesso',
        `ip_address` TEXT NULL,
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    $pdo->exec("CREATE TABLE IF NOT EXISTS `logs_auditoria` (
        `id` INTEGER PRIMARY KEY AUTOINCREMENT,
        `usuario_id` INTEGER NULL,
        `usuario_nome` TEXT NULL,
        `acao` TEXT NOT NULL,
        `detalhes` TEXT NULL,
        `ip_address` TEXT NULL,
        `user_agent` TEXT NULL,
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP
    );");

    criar_usuario_padrao_se_necessario($pdo);
}

function criar_usuario_padrao_se_necessario(PDO $pdo): void {
    $stmt = $pdo->query("SELECT COUNT(*) FROM `usuarios`");
    $total = $stmt->fetchColumn();
    if ($total == 0) {
        $senha_hash = password_hash('bananal123', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO `usuarios` (nome, email, senha) VALUES (:nome, :email, :senha)");
        $insert->execute([
            ':nome' => 'Administrador Bananal',
            ':email' => 'admin@bananal.com',
            ':senha' => $senha_hash
        ]);
        registrar_log($pdo, 'SISTEMA_INIT', 'Banco de dados e usuário administrador padrão inicializados com sucesso.', 1, 'Administrador Bananal');
    }
}

function obter_ip_cliente(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

function registrar_log(PDO $pdo, string $acao, string $detalhes, ?int $usuario_id = null, ?string $usuario_nome = null): bool {
    try {
        if ($usuario_id === null && isset($_SESSION['usuario']['id'])) {
            $usuario_id = (int)$_SESSION['usuario']['id'];
        }
        if ($usuario_nome === null && isset($_SESSION['usuario']['nome'])) {
            $usuario_nome = $_SESSION['usuario']['nome'];
        } elseif ($usuario_id === null) {
            $usuario_nome = 'Visitante Convidado';
        }

        $ip = obter_ip_cliente();
        $agent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Desconhecido', 0, 250);

        $sql = "INSERT INTO `logs_auditoria` (usuario_id, usuario_nome, acao, detalhes, ip_address, user_agent, criado_em) 
                VALUES (:usuario_id, :usuario_nome, :acao, :detalhes, :ip_address, :user_agent, datetime('now'))";

        if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') {
            $sql = "INSERT INTO `logs_auditoria` (usuario_id, usuario_nome, acao, detalhes, ip_address, user_agent, criado_em) 
                    VALUES (:usuario_id, :usuario_nome, :acao, :detalhes, :ip_address, :user_agent, NOW())";
        }
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':usuario_nome' => $usuario_nome,
            ':acao' => strtoupper($acao),
            ':detalhes' => $detalhes,
            ':ip_address' => $ip,
            ':user_agent' => $agent
        ]);
    } catch (Exception $e) {
        return false;
    }
}

function usuario_esta_logado(): bool {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['id']);
}

function usuario_autenticado(): ?array {
    return $_SESSION['usuario'] ?? null;
}

function definir_toast(string $tipo, string $mensagem): void {
    $_SESSION['toast'] = [
        'tipo' => $tipo,
        'mensagem' => $mensagem
    ];
}