<?php
/**
 * Gerenciamento de Sessão Nativa do PHP
 * Conversor Bananal
 */

declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    // Configurações de cookies de sessão seguros
    ini_set('session.use_strict_mode', '1');
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    
    // Cookie SameSite
    session_set_cookie_params([
        'lifetime' => 86400, // 24 horas
        'path'     => '/',
        'domain'   => '',
        'secure'   => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    session_start();
}

// Garante um identificador de sessão estável para associar os arquivos e histórico
if (!isset($_SESSION['bananal_user_id'])) {
    $_SESSION['bananal_user_id'] = bin2hex(random_bytes(16));
}

function getSessionUserId(): string
{
    return $_SESSION['bananal_user_id'] ?? session_id();
}
