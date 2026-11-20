<?php
/**
 * Trilha de Auditoria - Conversor Bananal Pro
 * Restrita exclusivamente a Administradores.
 */
if (!isset($_SESSION['usuario']) || ($_SESSION['usuario']['cargo'] ?? '') !== 'admin') {
    echo '
    <div class="max-w-md mx-auto bg-white rounded-3xl border-2 border-red-200 shadow-sm p-8 text-center my-12">
        <span class="text-4xl block mb-3">🔒</span>
        <h2 class="text-xl font-black text-stone-900">Acesso Restrito</h2>
        <p class="text-xs text-stone-600 mt-2 mb-6">Esta página de auditoria é exclusiva para administradores do sistema.</p>
        <a href="index.php?page=home" class="px-6 py-3 bg-yellow-400 font-black rounded-xl text-xs text-stone-950 shadow-xs inline-block">Voltar à Página Inicial</a>
    </div>';
    return;
}

try {
    $pdo = getDB();
    $stmt = $pdo->query("SELECT id, usuario_id, acao, detalhes, ip, created_at FROM auditoria ORDER BY id DESC LIMIT 100");
    $logs = $stmt->fetchAll();
} catch (Throwable $e) {
    $logs = [];
}
?>
<div class="space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-stone-900">Trilha de Auditoria 🛡️</h1>
      <p class="text-xs sm:text-sm text-stone-600 mt-1">Monitoramento de ações, eventos e segurança do sistema Bananal Pro.</p>
    </div>
    <div class="flex items-center gap-2">
      <button onclick="location.reload()" class="px-4 py-2.5 bg-white border border-yellow-300 font-bold text-xs rounded-xl text-stone-800 hover:bg-yellow-50">
        Atualizar Logs 🔄
      </button>
    </div>
  </div>

  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left border-collapse text-xs">
        <thead>
          <tr class="bg-yellow-100/60 border-b border-yellow-200 text-stone-800 font-black">
            <th class="p-4">ID</th>
            <th class="p-4">Usuário ID</th>
            <th class="p-4">Ação</th>
            <th class="p-4">Detalhes</th>
            <th class="p-4">Endereço IP</th>
            <th class="p-4">Data / Hora</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-yellow-100">
          <?php if (empty($logs)): ?>
            <tr>
              <td colspan="6" class="p-8 text-center text-stone-500 font-medium">Nenhum registro de auditoria encontrado.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($logs as $l): ?>
              <tr class="hover:bg-yellow-50/50 transition">
                <td class="p-4 font-bold text-stone-900">#<?= $l['id'] ?></td>
                <td class="p-4"><?= $l['usuario_id'] ? '#' . $l['usuario_id'] : '<span class="text-stone-400">Visitante</span>' ?></td>
                <td class="p-4 font-bold"><span class="bg-yellow-200 text-yellow-900 px-2 py-0.5 rounded-md"><?= htmlspecialchars($l['acao']) ?></span></td>
                <td class="p-4 text-stone-700 max-w-xs truncate" title="<?= htmlspecialchars($l['detalhes']) ?>"><?= htmlspecialchars($l['detalhes']) ?></td>
                <td class="p-4 font-mono text-stone-600"><?= htmlspecialchars($l['ip']) ?></td>
                <td class="p-4 text-stone-500"><?= date('d/m/Y H:i:s', strtotime($l['created_at'])) ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>