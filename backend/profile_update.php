<?php
/**
 * Conversor Bananal 🍌 - Atualização de Perfil e Senha
 */
require_once __DIR__ . '/../config/bd.php';

if (!usuario_esta_logado() || $_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

$u = usuario_autenticado();
$acao = $_POST['acao'] ?? '';

try {
    if ($acao === 'atualizar_nome') {
        $novo_nome = trim($_POST['nome'] ?? '');
        if (empty($novo_nome)) {
            definir_toast('error', 'O nome não pode estar em branco.');
            header('Location: ../perfil.php');
            exit;
        }

        $nome_antigo = $u['nome'];
        $stmt = $pdo->prepare("UPDATE usuarios SET nome = :nome WHERE id = :id");
        $stmt->execute([':nome' => $novo_nome, ':id' => $u['id']]);

        // Atualiza a sessão
        $_SESSION['usuario']['nome'] = $novo_nome;

        // Registra log de auditoria
        registrar_log($pdo, 'ALTERACAO_PERFIL', "Usuário alterou o nome de '{$nome_antigo}' para '{$novo_nome}'.", $u['id'], $novo_nome);

        definir_toast('success', 'Seu nome foi atualizado com sucesso!');
        header('Location: ../perfil.php');
        exit;
    }

    if ($acao === 'alterar_senha') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirmar_nova_senha = $_POST['confirmar_nova_senha'] ?? '';

        if (empty($senha_atual) || empty($nova_senha) || empty($confirmar_nova_senha)) {
            definir_toast('error', 'Preencha todos os campos para alterar a senha.');
            header('Location: ../perfil.php');
            exit;
        }

        if (strlen($nova_senha) < 6) {
            definir_toast('error', 'A nova senha deve possuir no mínimo 6 caracteres.');
            header('Location: ../perfil.php');
            exit;
        }

        if ($nova_senha !== $confirmar_nova_senha) {
            definir_toast('error', 'A confirmação não coincide com a nova senha digitada.');
            header('Location: ../perfil.php');
            exit;
        }

        // Verifica a senha atual cadastrada
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $u['id']]);
        $hash_atual = $stmt->fetchColumn();

        if (!$hash_atual || !password_verify($senha_atual, $hash_atual)) {
            definir_toast('error', 'A senha atual informada está incorreta.');
            header('Location: ../perfil.php');
            exit;
        }

        // Gera novo hash com BCRYPT (Regra do CORE)
        $novo_hash = password_hash($nova_senha, PASSWORD_BCRYPT);

        $update = $pdo->prepare("UPDATE usuarios SET senha = :senha WHERE id = :id");
        $update->execute([':senha' => $novo_hash, ':id' => $u['id']]);

        // Registra log de auditoria da alteração de segurança
        registrar_log($pdo, 'ALTERACAO_SENHA', "Usuário alterou sua senha com sucesso utilizando criptografia bcrypt.", $u['id'], $u['nome']);

        definir_toast('success', 'Sua senha foi redefinida com segurança!');
        header('Location: ../perfil.php');
        exit;
    }

    // Ação não reconhecida
    header('Location: ../perfil.php');
    exit;

} catch (PDOException $e) {
    definir_toast('error', 'Erro ao salvar alterações: ' . $e->getMessage());
    header('Location: ../perfil.php');
    exit;
}
