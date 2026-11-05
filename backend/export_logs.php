<?php
/**
 * Exportador de Trilha de Auditoria (CSV / JSON)
 * Conversor Bananal
 */

declare(strict_types=1);

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';

// Apenas administradores ou usuários logados podem exportar
if (!isset($_SESSION['usuario'])) {
    http_response_code(403);
    die("Acesso restrito. Faça login para exportar a trilha de auditoria.");
}

$format = strtolower($_GET['format'] ?? 'csv');
$pdo = getDBConnection();

$stmt = $pdo->prepare("
    SELECT a.id, a.acao, a.detalhes, a.ip, a.user_agent, a.created_at, u.nome as usuario_nome, u.email as usuario_email
    FROM auditoria a
    LEFT JOIN usuarios u ON a.usuario_id = u.id
    ORDER BY a.id DESC
    LIMIT 2000
");
$stmt->execute();
$logs = $stmt->fetchAll();

$timestamp = date('Y-m-d_His');

if ($format === 'json') {
    header('Content-Type: application/json; charset=UTF-8');
    header("Content-Disposition: attachment; filename=\"auditoria_bananal_{$timestamp}.json\"");
    echo json_encode(['gerado_em' => date('Y-m-d H:i:s'), 'total' => count($logs), 'logs' => $logs], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

// Formato padrão: CSV
header('Content-Type: text/csv; charset=UTF-8');
header("Content-Disposition: attachment; filename=\"auditoria_bananal_{$timestamp}.csv\"");
header('Pragma: no-cache');
header('Expires: 0');

$output = fopen('php://output', 'w');
// UTF-8 BOM para compatibilidade com Microsoft Excel
fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

fputcsv($output, ['ID', 'Ação', 'Detalhes', 'Usuário', 'E-mail', 'IP', 'Navegador / User Agent', 'Data/Hora'], ';');

foreach ($logs as $log) {
    fputcsv($output, [
        $log['id'],
        $log['acao'],
        $log['detalhes'],
        $log['usuario_nome'] ?? 'Anônimo / Sistema',
        $log['usuario_email'] ?? '-',
        $log['ip'],
        $log['user_agent'],
        $log['created_at']
    ], ';');
}

fclose($output);
exit;
