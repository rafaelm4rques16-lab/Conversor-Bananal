<?php
/**
 * Listagem do Histórico de Conversões da Sessão Atual
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';
require_once dirname(__DIR__) . '/services/StorageService.php';

$sessionId = getSessionUserId();

try {
    $pdo = getDBConnection();
    $stmt = $pdo->prepare("
        SELECT id, nome_original, formato_origem, tamanho_origem, formato_destino,
               arquivo_convertido, tamanho_convertido, status, mensagem_erro,
               token_download, created_at, concluido_em
        FROM conversoes
        WHERE session_id = :session_id
        ORDER BY id DESC
        LIMIT 50
    ");
    $stmt->execute([':session_id' => $sessionId]);
    $items = $stmt->fetchAll();

    $history = array_map(function($row) {
        $baseName = pathinfo($row['nome_original'], PATHINFO_FILENAME);
        $convertedName = $baseName . '.' . $row['formato_destino'];

        return [
            'id'                       => (int)$row['id'],
            'nome_original'            => $row['nome_original'],
            'nome_convertido'          => $convertedName,
            'formato_origem'           => strtoupper($row['formato_origem']),
            'formato_destino'          => strtoupper($row['formato_destino']),
            'tamanho_origem_txt'       => StorageService::formatBytes((int)$row['tamanho_origem']),
            'tamanho_convertido_txt'   => $row['tamanho_convertido'] ? StorageService::formatBytes((int)$row['tamanho_convertido']) : '-',
            'status'                   => $row['status'],
            'mensagem_erro'            => $row['mensagem_erro'],
            'token'                    => $row['token_download'],
            'download_url'             => ($row['status'] === 'concluido') ? 'backend/download.php?token=' . urlencode($row['token_download']) : null,
            'data_criacao'             => date('d/m/Y H:i', strtotime($row['created_at'])),
        ];
    }, $items);

    echo json_encode([
        'success' => true,
        'count'   => count($history),
        'items'   => $history
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error'   => "Erro ao carregar histórico: " . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
