<?php
/**
 * Atualização Cadastral e Alteração Segura de Senha
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';

try {
    if (!isset($_SESSION['usuario']['id'])) {
        http_response_code(401);
        throw new RuntimeException("Você precisa estar autenticado para realizar esta ação.");
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException("Método HTTP não permitido.");
    }

    $usuarioId  = (int)$_SESSION['usuario']['id'];
    $nome       = trim($_POST['nome'] ?? '');
    $email      = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $senhaAtual = trim($_POST['senha_atual'] ?? '');
    $novaSenha  = trim($_POST['nova_senha'] ?? '');
    $confSenha  = trim($_POST['conf_senha'] ?? '');

    if (empty($nome) || mb_strlen($nome) < 3) {
        throw new InvalidArgumentException("O nome informado é inválido ou muito curto.");
    }

    if (!$email) {
        throw new InvalidArgumentException("E-mail inválido.");
    }

    $pdo = getDBConnection();

    // Busca usuário atual para verificação de credenciais
    $stmtUser = $pdo->prepare("SELECT id, senha FROM usuarios WHERE id = :id LIMIT 1");
    $stmtUser->execute([':id' => $usuarioId]);
    $currentUser = $stmtUser->fetch();

    if (!$currentUser) {
        throw new RuntimeException("Usuário não encontrado.");
    }

    // Se informou alteração de senha
    $atualizarSenha = false;
    $novoHash = null;

    if (!empty($novaSenha)) {
        if (empty($senhaAtual)) {
            throw new InvalidArgumentException("Para alterar sua senha, digite a senha atual.");
        }
        if (!password_verify($senhaAtual, $currentUser['senha'])) {
            registrarAuditoria('perfil_senha_erro', "Tentativa incorreta de alteração de senha", $usuarioId);
            throw new RuntimeException("A senha atual informada está incorreta.");
        }
        if (mb_strlen($novaSenha) < 6) {
            throw new InvalidArgumentException("A nova senha deve possuir ao menos 6 caracteres.");
        }
        if ($novaSenha !== $confSenha) {
            throw new InvalidArgumentException("A confirmação da nova senha não confere.");
        }
        $novoHash = password_hash($novaSenha, PASSWORD_BCRYPT);
        $atualizarSenha = true;
    }

    // Atualiza dados
    if ($atualizarSenha) {
        $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email, senha = :senha WHERE id = :id");
        $stmtUpdate->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => $novoHash,
            ':id'    => $usuarioId
        ]);
        registrarAuditoria('perfil_update_senha', "Nome e senha alterados com Bcrypt", $usuarioId);
    } else {
        $stmtUpdate = $pdo->prepare("UPDATE usuarios SET nome = :nome, email = :email WHERE id = :id");
        $stmtUpdate->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':id'    => $usuarioId
        ]);
        registrarAuditoria('perfil_update', "Dados cadastrais atualizados", $usuarioId);
    }

    // Atualiza sessão
    $_SESSION['usuario']['nome']  = $nome;
    $_SESSION['usuario']['email'] = $email;

    echo json_encode([
        'success' => true,
        'message' => '🍌 Perfil atualizado com sucesso!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
