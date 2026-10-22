<?php
/**
 * Conversor Bananal 🍌 - Encerramento de Sessão (Logout)
 */
require_once __DIR__ . '/../config/bd.php';

if (usuario_esta_logado()) {
    $u = usuario_autenticado();
    // Registra log de auditoria antes de destruir a sessão
    registrar_log($pdo, 'LOGOUT', "Usuário efetuou saída do sistema: {$u['email']}", $u['id'], $u['nome']);
}

// Destroi a sessão
$_SESSION = [];
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
session_destroy();

// Inicia nova sessão para poder enviar o Toast de feedback
session_start();
definir_toast('info', 'Você saiu da sua conta com segurança. Até breve! 🍌');
header('Location: ../login.php');
exit;
