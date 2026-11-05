<?php
/**
 * Limpeza do Histórico da Sessão
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';
require_once dirname(__DIR__) . '/services/StorageService.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Método inválido.'], JSON_UNESCAPED_UNICODE);
    exit;
}

$sessionId = getSessionUserId();
$storage = new StorageService();

try {
    $pdo = getDBConnection();
    
    // Busca arquivos da sessão para exclusão física dos temporários
    $stmtSelect = $pdo->prepare("SELECT arquivo_convertido FROM conversoes WHERE session_id = :session_id");
    $stmtSelect->execute([':session_id' => $sessionId]);
    $files = $stmtSelect->fetchAll();

    foreach ($files as $row) {
        if (!empty($row['arquivo_convertido'])) {
            $path = $storage->getConvertedDir() . DIRECTORY_SEPARATOR . basename($row['arquivo_convertido']);
            if (file_exists($path)) {
                @unlink($path);
            }
        }
    }

    // Exclui registros no banco
    $stmtDelete = $pdo->prepare("DELETE FROM conversoes WHERE session_id = :session_id");
    $stmtDelete->execute([':session_id' => $sessionId]);

    echo json_encode([
        'success' => true,
        'message' => 'Histórico limpo com sucesso!'
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => 'Falha ao limpar histórico: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
