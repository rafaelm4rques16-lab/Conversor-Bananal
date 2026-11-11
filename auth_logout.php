<?php
/**
 * Conversor Bananal 🍌 - Encerramento Seguro de Sessão (Logout)
 */
require_once __DIR__ . '/../config/bd.php';

if (usuario_esta_logado()) {
    $u = usuario_autenticado();
    registrar_log($pdo, 'LOGOUT', "Usuário efetuou logout da conta.", $u['id'], $u['nome']);
}

// Limpa dados de sessão
unset($_SESSION['usuario']);
session_regenerate_id(true);

definir_toast('info', 'Você foi desconectado com sucesso. Até logo! 🍌');
header('Location: ../index.php');
exit;
