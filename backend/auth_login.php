<?php
/**
 * Conversor Bananal 🍌 - Processamento de Login
 */
require_once __DIR__ . '/../config/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || empty($senha)) {
    definir_toast('error', 'Por favor, informe seu e-mail e sua senha.');
    header('Location: ../login.php');
    exit;
}

try {
    // Busca usuário pelo e-mail com PDO prepared statement
    $stmt = $pdo->prepare("SELECT id, nome, email, senha FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $usuario = $stmt->fetch();

    // Verificação de senha com bcrypt
    if ($usuario && password_verify($senha, $usuario['senha'])) {
        // Sucesso: armazena na sessão nativa
        $_SESSION['usuario'] = [
            'id' => (int)$usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email']
        ];

        // Registra log de auditoria
        registrar_log($pdo, 'LOGIN', "Usuário autenticado com sucesso: {$usuario['email']}", $usuario['id'], $usuario['nome']);

        definir_toast('success', "Olá, {$usuario['nome']}! Você entrou com sucesso no Conversor Bananal. 🍌");
        header('Location: ../index.php');
        exit;
    } else {
        // Falha no login: registra log de tentativa para auditoria
        registrar_log($pdo, 'LOGIN_FALHA', "Tentativa de login frustrada com email: {$email}", null, 'Desconhecido');

        definir_toast('error', 'E-mail ou senha inválidos. Por favor, verifique suas informações.');
        header('Location: ../login.php');
        exit;
    }

} catch (PDOException $e) {
    definir_toast('error', 'Erro ao processar autenticação: ' . $e->getMessage());
    header('Location: ../login.php');
    exit;
}
