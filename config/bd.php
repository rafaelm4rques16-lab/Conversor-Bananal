<?php
/**
 * Conversor Bananal 🍌
 * Arquivo de Conexão com o Banco de Dados (bd.php)
 * Utiliza PDO para máxima segurança contra SQL Injections.
 */

// Inicia a sessão nativa PHP se ainda não tiver sido iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do Banco de Dados MySQL
$db_host = '127.0.0.1';
$db_port = '3306';
$db_user = 'root';
$db_pass = '';
$db_name = 'conversor_bananal';

try {
    // 1. Conexão inicial com o servidor MySQL (sem selecionar banco) para garantir existência
    $dsn_server = "mysql:host={$db_host};port={$db_port};charset=utf8mb4";
    $pdo_server = new PDO($dsn_server, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // Cria o banco se não existir
    $pdo_server->exec("CREATE DATABASE IF NOT EXISTS `{$db_name}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");

    // 2. Conexão definitiva vinculada ao banco `conversor_bananal`
    $dsn = "mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4";
    $pdo = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);

    // 3. Inicialização e garantia das tabelas necessárias para o sistema
    inicializar_tabelas($pdo);

} catch (PDOException $e) {
    die("Erro crítico na conexão com o banco de dados: " . htmlspecialchars($e->getMessage()));
}

/**
 * Cria as tabelas do sistema se ainda não existirem
 */
function inicializar_tabelas(PDO $pdo): void {
    // Tabela de Usuários (senhas tratadas estritamente com bcrypt)
    $sql_usuarios = "CREATE TABLE IF NOT EXISTS `usuarios` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nome` VARCHAR(100) NOT NULL,
        `email` VARCHAR(150) NOT NULL UNIQUE,
        `senha` VARCHAR(255) NOT NULL,
        `avatar` VARCHAR(255) DEFAULT 'banana',
        `criado_em` DATETIME DEFAULT CURRENT_TIMESTAMP,
        `atualizado_em` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql_usuarios);

    // Tabela de Histórico de Conversões
    $sql_conversoes = "CREATE TABLE IF NOT EXISTS `conversoes` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql_conversoes);

    // Tabela de Logs de Auditoria para rastrear TODAS as alterações feitas
    $sql_logs = "CREATE TABLE IF NOT EXISTS `logs_auditoria` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    $pdo->exec($sql_logs);

    // Cria um usuário padrão para demonstração/testes se a tabela estiver vazia
    $stmt = $pdo->query("SELECT COUNT(*) as total FROM `usuarios`");
    $total = $stmt->fetchColumn();
    if ($total == 0) {
        // Senha com BCRYPT
        $senha_hash = password_hash('bananal123', PASSWORD_BCRYPT);
        $insert = $pdo->prepare("INSERT INTO `usuarios` (nome, email, senha) VALUES (:nome, :email, :senha)");
        $insert->execute([
            ':nome' => 'Administrador Bananal',
            ':email' => 'admin@bananal.com',
            ':senha' => $senha_hash
        ]);

        // Log de inicialização do sistema
        registrar_log($pdo, 'SISTEMA_INIT', 'Banco de dados e usuário inicial padrão criados com sucesso.', 1, 'Administrador Bananal');
    }
}

/**
 * Obtém o endereço IP real do cliente
 */
function obter_ip_cliente(): string {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

/**
 * Registra eventos e alterações em logs de auditoria
 * Atende à regra do CORE: "manter sempre os logs de qualquer alteração feita por qualquer usuário"
 */
function registrar_log(PDO $pdo, string $acao, string $detalhes, ?int $usuario_id = null, ?string $usuario_nome = null): bool {
    try {
        // Se não foi passado id/nome, tenta pegar da sessão atual
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
                VALUES (:usuario_id, :usuario_nome, :acao, :detalhes, :ip_address, :user_agent, NOW())";
        
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
        error_log("Falha ao gravar log de auditoria: " . $e->getMessage());
        return false;
    }
}

/**
 * Retorna se o usuário está logado na sessão atual
 */
function usuario_esta_logado(): bool {
    return isset($_SESSION['usuario']) && !empty($_SESSION['usuario']['id']);
}

/**
 * Retorna os dados do usuário autenticado
 */
function usuario_autenticado(): ?array {
    return $_SESSION['usuario'] ?? null;
}

/**
 * Define mensagem flash para exibição em TOAST
 */
function definir_toast(string $tipo, string $mensagem): void {
    $_SESSION['toast'] = [
        'tipo' => $tipo, // 'success', 'error', 'warning', 'info'
        'mensagem' => $mensagem
    ];
}
