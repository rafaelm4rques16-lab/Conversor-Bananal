<?php
/**
 * Conversor Bananal 🍌 - Processamento de Cadastro
 */
require_once __DIR__ . '/../config/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../registro.php');
    exit;
}

$nome = trim($_POST['nome'] ?? '');
$email = filter_var(trim($_POST['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validações básicas
if (empty($nome) || !$email || empty($senha)) {
    definir_toast('error', 'Preencha todos os campos corretamente para continuar.');
    header('Location: ../registro.php');
    exit;
}

if (strlen($senha) < 6) {
    definir_toast('error', 'A senha deve conter no mínimo 6 caracteres.');
    header('Location: ../registro.php');
    exit;
}

if ($senha !== $confirmar_senha) {
    definir_toast('error', 'As senhas informadas não coincidem.');
    header('Location: ../registro.php');
    exit;
}

try {
    // 1. Verifica se e-mail já existe (com PDO prepared statement)
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        definir_toast('error', 'Este endereço de e-mail já está cadastrado no sistema.');
        header('Location: ../registro.php');
        exit;
    }

    // 2. Hash seguro da senha com BCRYPT (Regra do CORE: Tratar senhas de usuários com hash bcript)
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    // 3. Insere o novo usuário no MySQL
    $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, criado_em) VALUES (:nome, :email, :senha, NOW())");
    $insert->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha_hash
    ]);

    $novo_id = (int)$pdo->lastInsertId();

    // 4. Registra log de auditoria obrigatório
    registrar_log($pdo, 'CADASTRO', "Novo usuário cadastrado: {$nome} ({$email})", $novo_id, $nome);

    // 5. Inicia a sessão nativa do usuário
    $_SESSION['usuario'] = [
        'id' => $novo_id,
        'nome' => $nome,
        'email' => $email
    ];

    definir_toast('success', "Conta criada com sucesso! Bem-vindo(a) ao Conversor Bananal, {$nome}! 🍌");
    header('Location: ../index.php');
    exit;

} catch (PDOException $e) {
    definir_toast('error', 'Erro interno ao processar cadastro: ' . $e->getMessage());
    header('Location: ../registro.php');
    exit;
}
