<?php
/**
 * Cadastro de Usuários com Criptografia Bcrypt
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException("Método HTTP não permitido.");
    }

    $nome   = trim($_POST['nome'] ?? '');
    $email  = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha  = trim($_POST['senha'] ?? '');
    $conf   = trim($_POST['senha_confirm'] ?? '');

    if (empty($nome) || mb_strlen($nome) < 3) {
        throw new InvalidArgumentException("O nome deve conter pelo menos 3 caracteres.");
    }

    if (!$email) {
        throw new InvalidArgumentException("Por favor, informe um endereço de e-mail válido.");
    }

    if (mb_strlen($senha) < 6) {
        throw new InvalidArgumentException("A senha deve possuir pelo menos 6 caracteres.");
    }

    if ($senha !== $conf) {
        throw new InvalidArgumentException("A confirmação de senha não confere com a senha digitada.");
    }

    $pdo = getDBConnection();

    // Verifica se e-mail já existe
    $stmtCheck = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmtCheck->execute([':email' => $email]);
    if ($stmtCheck->fetch()) {
        throw new RuntimeException("Este e-mail já está cadastrado no sistema.");
    }

    // Criptografia estrita com Bcrypt nativo do PHP
    $senhaHash = password_hash($senha, PASSWORD_BCRYPT);

    $stmtInsert = $pdo->prepare("
        INSERT INTO usuarios (nome, email, senha, cargo, created_at)
        VALUES (:nome, :email, :senha, 'usuario', NOW())
    ");
    
    // Tratamento de compatibilidade caso SQLite esteja ativo
    try {
        $stmtInsert->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $senhaHash
        ]);
    } catch (PDOException $e) {
        // Fallback SQLite caso NOW() falhe
        $stmtInsertSqlite = $pdo->prepare("
            INSERT INTO usuarios (nome, email, senha, cargo, created_at)
            VALUES (:nome, :email, :senha, 'usuario', datetime('now'))
        ");
        $stmtInsertSqlite->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $senhaHash
        ]);
    }

    $novoId = (int)$pdo->lastInsertId();

    // Inicia sessão automaticamente
    $_SESSION['usuario'] = [
        'id'     => $novoId,
        'nome'   => $nome,
        'email'  => $email,
        'cargo'  => 'usuario',
        'avatar' => null
    ];

    registrarAuditoria('cadastro', "Novo usuário registrado: {$email}", $novoId);

    echo json_encode([
        'success'  => true,
        'message'  => "🍌 Conta criada com sucesso! Seja bem-vindo ao Bananal.",
        'redirect' => 'index.php'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
