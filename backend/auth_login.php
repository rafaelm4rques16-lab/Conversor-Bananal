<?php
/**
 * Autenticação de Usuários com Bcrypt
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

    $email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senha = trim($_POST['senha'] ?? '');

    if (!$email || empty($senha)) {
        throw new InvalidArgumentException("Preencha um e-mail válido e a sua senha.");
    }

    $pdo = getDBConnection();
    $stmt = $pdo->prepare("SELECT id, nome, email, senha, cargo, avatar FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    // Verificação estrita de senha com Bcrypt
    if (!$user || !password_verify($senha, $user['senha'])) {
        registrarAuditoria('login_falha', "Tentativa de login inválida para o e-mail: {$email}");
        throw new RuntimeException("E-mail ou senha incorretos.");
    }

    // Login com sucesso
    $_SESSION['usuario'] = [
        'id'     => (int)$user['id'],
        'nome'   => $user['nome'],
        'email'  => $user['email'],
        'cargo'  => $user['cargo'],
        'avatar' => $user['avatar'] ?? null
    ];

    registrarAuditoria('login_sucesso', "Usuário autenticado no sistema", (int)$user['id']);

    echo json_encode([
        'success'  => true,
        'message'  => "🍌 Bem-vindo de volta, " . htmlspecialchars($user['nome']) . "!",
        'redirect' => 'index.php'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
