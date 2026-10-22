<?php
/**
 * Conversor Bananal 🍌 - Exportação de Logs de Auditoria (CSV / JSON)
 */
require_once __DIR__ . '/../config/bd.php';

$formato = strtolower($_GET['formato'] ?? 'csv');

try {
    $stmt = $pdo->query("SELECT id, usuario_id, usuario_nome, acao, detalhes, ip_address, user_agent, criado_em FROM logs_auditoria ORDER BY id DESC");
    $logs = $stmt->fetchAll();

    // Registra auditoria da exportação
    $u = usuario_autenticado();
    $uid = $u['id'] ?? null;
    $unome = $u['nome'] ?? 'Visitante';
    registrar_log($pdo, 'EXPORTACAO_LOGS', "Relatório completo de auditoria exportado no formato {$formato} (" . count($logs) . " registros).", $uid, $unome);

    $data_hora = date('Y-m-d_H-i-s');

    if ($formato === 'json') {
        header('Content-Type: application/json; charset=utf-8');
        header("Content-Disposition: attachment; filename=auditoria_bananal_{$data_hora}.json");
        echo json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Padrão: CSV
    header('Content-Type: text/csv; charset=utf-8');
    header("Content-Disposition: attachment; filename=auditoria_bananal_{$data_hora}.csv");

    $output = fopen('php://output', 'w');
    // Adiciona BOM para UTF-8 no Excel
    fprintf($output, chr(0xEF).chr(0xBB).chr(0xBF));

    // Cabeçalho CSV
    fputcsv($output, ['ID', 'ID Usuário', 'Usuário', 'Ação', 'Detalhes', 'Endereço IP', 'Navegador/Agente', 'Data/Hora'], ';');

    foreach ($logs as $log) {
        fputcsv($output, [
            $log['id'],
            $log['usuario_id'] ?? 'N/A',
            $log['usuario_nome'] ?? 'N/A',
            $log['acao'],
            $log['detalhes'],
            $log['ip_address'],
            $log['user_agent'],
            $log['criado_em']
        ], ';');
    }

    fclose($output);
    exit;

} catch (PDOException $e) {
    die("Erro ao gerar exportação: " . $e->getMessage());
}
