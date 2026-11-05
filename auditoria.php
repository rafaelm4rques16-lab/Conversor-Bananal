<?php
/**
 * Trilha de Auditoria e Segurança - Conversor Bananal
 */
declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';

$pdo = getDBConnection();

// Busca os últimos 100 registros de auditoria
$stmt = $pdo->prepare("
    SELECT a.id, a.acao, a.detalhes, a.ip, a.user_agent, a.created_at, u.nome as usuario_nome, u.email as usuario_email
    FROM auditoria a
    LEFT JOIN usuarios u ON a.usuario_id = u.id
    ORDER BY a.id DESC
    LIMIT 100
");
$stmt->execute();
$logs = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Trilha de Auditoria - Conversor Bananal 🍌</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-900 font-sans flex flex-col justify-between">

  <!-- Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
    
    <!-- Topo da Auditoria -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2.5">
          <span class="text-3xl">🛡️</span>
          <h1 class="text-2xl sm:text-3xl font-black text-stone-900">Trilha de Auditoria</h1>
        </div>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Monitoramento de acessos, conversões e alterações no sistema em conformidade com as regras de segurança</p>
      </div>

      <div class="flex items-center gap-2">
        <a href="backend/export_logs.php?format=csv" class="px-4 py-2.5 rounded-xl bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black text-xs shadow-xs transition flex items-center gap-1.5">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
          <span>Exportar CSV</span>
        </a>
        <a href="backend/export_logs.php?format=json" class="px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-yellow-300 font-bold text-xs shadow-xs transition flex items-center gap-1.5">
          <span>{ } Exportar JSON</span>
        </a>
      </div>
    </div>

    <!-- Tabela de Auditoria -->
    <div class="bg-white rounded-3xl border-2 border-yellow-200 shadow-md overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm">
          <thead>
            <tr class="bg-yellow-100/60 border-b border-yellow-200 text-stone-700 font-bold uppercase text-[11px] tracking-wider">
              <th class="py-3.5 px-4">ID</th>
              <th class="py-3.5 px-4">Ação</th>
              <th class="py-3.5 px-4">Detalhes</th>
              <th class="py-3.5 px-4">Usuário</th>
              <th class="py-3.5 px-4">IP</th>
              <th class="py-3.5 px-4">Data/Hora</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-100 text-stone-800 font-medium">
            <?php if (empty($logs)): ?>
              <tr>
                <td colspan="6" class="py-12 text-center text-stone-400">Nenhum registro de auditoria gerado ainda.</td>
              </tr>
            <?php else: ?>
              <?php foreach ($logs as $log): ?>
                <tr class="hover:bg-yellow-50/40 transition">
                  <td class="py-3 px-4 font-mono text-[11px] text-stone-400">#<?= $log['id'] ?></td>
                  <td class="py-3 px-4 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded-md font-mono font-bold text-[11px] bg-stone-100 text-stone-800 border border-stone-200 uppercase">
                      <?= htmlspecialchars($log['acao']) ?>
                    </span>
                  </td>
                  <td class="py-3 px-4 max-w-xs sm:max-w-md truncate" title="<?= htmlspecialchars($log['detalhes'] ?? '') ?>">
                    <?= htmlspecialchars($log['detalhes'] ?? '-') ?>
                  </td>
                  <td class="py-3 px-4 whitespace-nowrap">
                    <?php if (!empty($log['usuario_nome'])): ?>
                      <span class="font-bold text-stone-900"><?= htmlspecialchars($log['usuario_nome']) ?></span>
                    <?php else: ?>
                      <span class="text-stone-400 text-xs italic">Sessão Anônima</span>
                    <?php endif; ?>
                  </td>
                  <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-stone-500">
                    <?= htmlspecialchars($log['ip']) ?>
                  </td>
                  <td class="py-3 px-4 whitespace-nowrap font-mono text-[11px] text-stone-500">
                    <?= date('d/m/Y H:i:s', strtotime($log['created_at'])) ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </div>

  </main>

  <!-- Rodapé -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/toast.js"></script>
  <script src="assets/js/modal.js"></script>
</body>
</html>
