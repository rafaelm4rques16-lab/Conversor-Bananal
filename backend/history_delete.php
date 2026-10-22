<?php
/**
 * Conversor Bananal 🍌 - Exclusão de Histórico com Auditoria Rigorosa
 */
require_once __DIR__ . '/../config/bd.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../historico.php');
    exit;
}

$u = usuario_autenticado();
$usuario_id = $u['id'] ?? null;
$usuario_nome = $u['nome'] ?? 'Visitante';

$action = $_POST['action'] ?? 'delete_single';
$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

try {
    if ($action === 'clear_all') {
        // Limpa todo o histórico pertencente ao usuário logado ou da sessão
        if ($usuario_id) {
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM conversoes WHERE usuario_id = :uid");
            $stmt->execute([':uid' => $usuario_id]);
            $count = $stmt->fetchColumn();

            $del = $pdo->prepare("DELETE FROM conversoes WHERE usuario_id = :uid");
            $del->execute([':uid' => $usuario_id]);

            // Auditoria
            registrar_log($pdo, 'LIMPEZA_HISTORICO', "O usuário limpou todo o seu histórico de conversões ({$count} registros removidos).", $usuario_id, $usuario_nome);
        } else {
            // Se for visitante, apaga por IP ou do dia
            $ip = obter_ip_cliente();
            $del = $pdo->prepare("DELETE FROM conversoes WHERE usuario_id IS NULL AND ip_address = :ip");
            $del->execute([':ip' => $ip]);

            registrar_log($pdo, 'LIMPEZA_HISTORICO', "Visitante limpou histórico associado ao IP {$ip}.", null, 'Visitante');
        }

        definir_toast('success', 'Todo o histórico de conversões foi excluído com sucesso.');
        header('Location: ../historico.php');
        exit;
    }

    if ($action === 'delete_single' && $id > 0) {
        // Busca o registro antes de excluir para salvar detalhes no log de auditoria
        $stmt = $pdo->prepare("SELECT * FROM conversoes WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $reg = $stmt->fetch();

        if ($reg) {
            // Se o usuário estiver logado, garante que só pode deletar o que for dele (a menos que seja admin)
            if ($usuario_id && $reg['usuario_id'] != $usuario_id && $u['email'] !== 'admin@bananal.com') {
                definir_toast('error', 'Você não tem permissão para remover este registro.');
                header('Location: ../historico.php');
                exit;
            }

            $del = $pdo->prepare("DELETE FROM conversoes WHERE id = :id");
            $del->execute([':id' => $id]);

            // Registra log de auditoria com detalhes completos do arquivo removido
            $detalhes = "Registro #{$id} de conversão do arquivo '{$reg['nome_original']}' ({$reg['formato_origem']} -> {$reg['formato_destino']}) foi excluído.";
            registrar_log($pdo, 'EXCLUSAO_HISTORICO', $detalhes, $usuario_id, $usuario_nome);

            definir_toast('success', "Registro #{$id} removido do histórico com sucesso.");
        } else {
            definir_toast('warning', 'Registro não encontrado.');
        }

        header('Location: ../historico.php');
        exit;
    }

    header('Location: ../historico.php');
    exit;

} catch (PDOException $e) {
    definir_toast('error', 'Erro ao excluir do histórico: ' . $e->getMessage());
    header('Location: ../historico.php');
    exit;
}
