<?php
/**
 * Exclusão de Itens do Histórico
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';
require_once dirname(__DIR__) . '/services/StorageService.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException("Método inválido.");
    }

    $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
    $sessionId = getSessionUserId();
    $usuarioId = $_SESSION['usuario']['id'] ?? null;
    $storage = new StorageService();
    $pdo = getDBConnection();

    if ($id) {
        // Exclusão unitária
        $stmtFind = $pdo->prepare("
            SELECT arquivo_convertido 
            FROM conversoes 
            WHERE id = :id AND (session_id = :sid OR usuario_id = :uid)
            LIMIT 1
        ");
        $stmtFind->execute([':id' => $id, ':sid' => $sessionId, ':uid' => $usuarioId]);
        $row = $stmtFind->fetch();

        if ($row && !empty($row['arquivo_convertido'])) {
            $path = $storage->getConvertedDir() . DIRECTORY_SEPARATOR . basename($row['arquivo_convertido']);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $stmtDel = $pdo->prepare("DELETE FROM conversoes WHERE id = :id AND (session_id = :sid OR usuario_id = :uid)");
        $stmtDel->execute([':id' => $id, ':sid' => $sessionId, ':uid' => $usuarioId]);

        registrarAuditoria('historico_delete_item', "Item de conversão #{$id} removido");

        echo json_encode(['success' => true, 'message' => 'Item removido do histórico!']);
    } else {
        // Exclusão em lote de toda a sessão
        $stmtFindAll = $pdo->prepare("SELECT arquivo_convertido FROM conversoes WHERE session_id = :sid OR usuario_id = :uid");
        $stmtFindAll->execute([':sid' => $sessionId, ':uid' => $usuarioId]);
        $rows = $stmtFindAll->fetchAll();

        foreach ($rows as $r) {
            if (!empty($r['arquivo_convertido'])) {
                $p = $storage->getConvertedDir() . DIRECTORY_SEPARATOR . basename($r['arquivo_convertido']);
                if (file_exists($p)) @unlink($p);
            }
        }

        $stmtClear = $pdo->prepare("DELETE FROM conversoes WHERE session_id = :sid OR usuario_id = :uid");
        $stmtClear->execute([':sid' => $sessionId, ':uid' => $usuarioId]);

        registrarAuditoria('historico_limpar', "Todo o histórico foi limpo pelo usuário");

        echo json_encode(['success' => true, 'message' => 'Histórico limpo com sucesso!']);
    }

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
