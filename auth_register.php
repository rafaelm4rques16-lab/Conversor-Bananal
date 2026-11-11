<?php
/**
 * Conversor Bananal 🍌 - Processamento de Cadastro
 * Senhas tratadas com hash Bcrypt e inserção com PDO
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

// Validações
if (empty($nome) || !$email || empty($senha)) {
    definir_toast('error', 'Preencha todos os campos obrigatórios corretamente.');
    header('Location: ../registro.php');
    exit;
}

if (strlen($senha) < 6) {
    definir_toast('error', 'A senha deve conter no mínimo 6 caracteres.');
    header('Location: ../registro.php');
    exit;
}

if ($senha !== $confirmar_senha) {
    definir_toast('error', 'As senhas digitadas não coincidem.');
    header('Location: ../registro.php');
    exit;
}

try {
    // 1. Verifica se e-mail já existe com Prepared Statement
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    if ($stmt->fetch()) {
        definir_toast('error', 'Este endereço de e-mail já está cadastrado no sistema.');
        header('Location: ../registro.php');
        exit;
    }

    // 2. Hash obrigatório com BCRYPT (Regra de Negócio CORE)
    $senha_hash = password_hash($senha, PASSWORD_BCRYPT);

    // 3. Insere novo usuário via PDO
    $now_fn = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql') ? 'NOW()' : "datetime('now')";
    $insert = $pdo->prepare("INSERT INTO usuarios (nome, email, senha, criado_em) VALUES (:nome, :email, :senha, {$now_fn})");
    $insert->execute([
        ':nome' => $nome,
        ':email' => $email,
        ':senha' => $senha_hash
    ]);

    $novo_id = (int)$pdo->lastInsertId();

    // 4. Registra log de auditoria
    registrar_log($pdo, 'CADASTRO', "Novo usuário cadastrado: {$nome} ({$email})", $novo_id, $nome);

    // 5. Inicia sessão nativa do usuário
    $_SESSION['usuario'] = [
        'id' => $novo_id,
        'nome' => $nome,
        'email' => $email
    ];

    definir_toast('success', "Conta criada com sucesso! Seja bem-vindo(a) ao Conversor Bananal, {$nome}! 🍌");
    header('Location: ../index.php');
    exit;

} catch (PDOException $e) {
    definir_toast('error', 'Erro interno ao processar cadastro: ' . $e->getMessage());
    header('Location: ../registro.php');
    exit;
}
