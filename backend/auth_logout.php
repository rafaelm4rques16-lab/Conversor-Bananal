<?php
/**
 * Encerramento Seguro de Sessão
 * Conversor Bananal
 */

declare(strict_types=1);

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';

if (isset($_SESSION['usuario']['id'])) {
    registrarAuditoria('logout', 'Usuário encerrou a sessão', (int)$_SESSION['usuario']['id']);
}

// Limpa dados de sessão
$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header('Location: ../login.php?msg=logout');
exit;
