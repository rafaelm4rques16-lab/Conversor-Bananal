<?php
/**
 * Conversor Bananal 🍌 - API de Registro e Processamento de Conversões
 */
require_once __DIR__ . '/../config/bd.php';

header('Content-Type: application/json; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Método não permitido']);
    exit;
}

// Suporte tanto para JSON no corpo quanto POST multipart tradicional
$input_json = file_get_contents('php://input');
$data = json_decode($input_json, true) ?? $_POST;

$nome_original = trim($data['nome_original'] ?? 'arquivo_sem_nome');
$formato_origem = strtolower(trim($data['formato_origem'] ?? 'desconhecido'));
$formato_destino = strtolower(trim($data['formato_destino'] ?? 'desconhecido'));
$tamanho_original = (int)($data['tamanho_original'] ?? 0);
$tamanho_convertido = (int)($data['tamanho_convertido'] ?? 0);
$status = in_array($data['status'] ?? 'sucesso', ['sucesso', 'falha']) ? $data['status'] : 'sucesso';
$caminho_arquivo = null;

// Se houve upload físico de arquivo convertido
if (!empty($_FILES['arquivo_convertido']['name'])) {
    $upload_dir = __DIR__ . '/../uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }
    
    $safe_name = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $_FILES['arquivo_convertido']['name']);
    $final_path = $upload_dir . time() . '_' . $safe_name;
    
    if (move_uploaded_file($_FILES['arquivo_convertido']['tmp_name'], $final_path)) {
        $caminho_arquivo = 'uploads/' . basename($final_path);
        $tamanho_convertido = filesize($final_path);
    }
}

$usuario_id = null;
$usuario_nome = 'Visitante Convidado';

if (usuario_esta_logado()) {
    $u = usuario_autenticado();
    $usuario_id = $u['id'];
    $usuario_nome = $u['nome'];
}

try {
    // 1. Grava no banco de dados com PDO seguro
    $sql = "INSERT INTO conversoes 
            (usuario_id, nome_original, formato_origem, formato_destino, tamanho_original, tamanho_convertido, caminho_arquivo, status, ip_address, criado_em)
            VALUES 
            (:usuario_id, :nome_original, :formato_origem, :formato_destino, :tamanho_original, :tamanho_convertido, :caminho_arquivo, :status, :ip, NOW())";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':usuario_id' => $usuario_id,
        ':nome_original' => $nome_original,
        ':formato_origem' => $formato_origem,
        ':formato_destino' => $formato_destino,
        ':tamanho_original' => $tamanho_original,
        ':tamanho_convertido' => $tamanho_convertido,
        ':caminho_arquivo' => $caminho_arquivo,
        ':status' => $status,
        ':ip' => obter_ip_cliente()
    ]);

    $conversao_id = (int)$pdo->lastInsertId();

    // 2. Registra o evento no log de auditoria
    $detalhes_log = "Arquivo '{$nome_original}' convertido de .{$formato_origem} para .{$formato_destino} | Orig: {$tamanho_original}B -> Dest: {$tamanho_convertido}B | Status: {$status}";
    registrar_log($pdo, 'CONVERSAO_ARQUIVO', $detalhes_log, $usuario_id, $usuario_nome);

    echo json_encode([
        'success' => true,
        'id' => $conversao_id,
        'message' => 'Conversão registrada com sucesso no banco de dados e na auditoria.',
        'data' => [
            'id' => $conversao_id,
            'nome_original' => $nome_original,
            'formato_origem' => $formato_origem,
            'formato_destino' => $formato_destino,
            'tamanho_original' => $tamanho_original,
            'tamanho_convertido' => $tamanho_convertido,
            'caminho_arquivo' => $caminho_arquivo,
            'status' => $status
        ]
    ]);
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => 'Falha ao registrar conversão no MySQL: ' . $e->getMessage()
    ]);
    exit;
}
