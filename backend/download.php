<?php
/**
 * Download Seguro de Arquivo Convertido
 * Conversor Bananal
 */

declare(strict_types=1);

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';
require_once dirname(__DIR__) . '/services/StorageService.php';

$token = trim($_GET['token'] ?? '');

if (empty($token) || !preg_match('/^[a-f0-9]{48}$/i', $token)) {
    http_response_code(400);
    die("Token de download inválido ou expirado.");
}

$sessionId = getSessionUserId();
$storage = new StorageService();

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT id, nome_original, formato_destino, arquivo_convertido, status
        FROM conversoes
        WHERE token_download = :token AND session_id = :session_id
        LIMIT 1
    ");
    $stmt->execute([
        ':token'      => $token,
        ':session_id' => $sessionId
    ]);
    $record = $stmt->fetch();

    if (!$record) {
        http_response_code(404);
        die("Arquivo não encontrado ou permissão de download expirada.");
    }

    if ($record['status'] !== 'concluido' || empty($record['arquivo_convertido'])) {
        http_response_code(400);
        die("A conversão deste arquivo ainda não foi concluída com sucesso.");
    }

    $filepath = $storage->getConvertedDir() . DIRECTORY_SEPARATOR . basename($record['arquivo_convertido']);

    if (!file_exists($filepath)) {
        http_response_code(410);
        die("O arquivo expirou do servidor temporário. Faça uma nova conversão.");
    }

    $baseName = pathinfo($record['nome_original'], PATHINFO_FILENAME);
    $downloadName = $baseName . '.' . $record['formato_destino'];

    // Headers de download seguro
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . addslashes($downloadName) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($filepath));

    // Limpa qualquer buffer de saída antes do envio
    if (ob_get_level()) {
        ob_end_clean();
    }

    readfile($filepath);
    exit;

} catch (Throwable $e) {
    http_response_code(500);
    die("Erro ao processar download: " . htmlspecialchars($e->getMessage()));
}
