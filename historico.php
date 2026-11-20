<?php
/**
 * View - Histórico Geral de Conversões
 * Conversor Bananal Pro
 */
$pdo = getDB();
$sid = getSessionId();
$uid = $_SESSION['usuario']['id'] ?? null;

$stmt = $pdo->prepare("
    SELECT id, nome_original, formato_origem, formato_destino, tamanho_origem, tamanho_convertido, status, token_download, created_at 
    FROM conversoes 
    WHERE session_id = :s OR (:u IS NOT NULL AND usuario_id = :u)
    ORDER BY id DESC LIMIT 100
");
$stmt->execute([':s' => $sid, ':u' => $uid]);
$items = $stmt->fetchAll();
?>
<section class="max-w-5xl mx-auto space-y-6">
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
    <div>
      <h1 class="text-2xl sm:text-3xl font-black text-stone-900 flex items-center gap-2">
        <span>🕒</span> Histórico de Conversões
      </h1>
      <p class="text-xs text-stone-500 mt-1">Arquivos processados durante a sua navegação</p>
    </div>

    <?php if (!empty($items)): ?>
      <button onclick="handleClearHistory()" class="px-4 py-2 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold rounded-xl border border-red-300 transition">
        Limpar Histórico Completo
      </button>
    <?php endif; ?>
  </div>

  <div class="bg-white rounded-3xl border-2 border-yellow-200 shadow-md overflow-hidden">
    <?php if (empty($items)): ?>
      <div class="text-center py-16 px-4">
        <span class="text-5xl block mb-3 animate-pulse">🍌</span>
        <h3 class="text-base font-black text-stone-800">Nenhuma conversão registrada</h3>
        <p class="text-xs text-stone-500 mt-1 mb-5">Arraste seus arquivos no Conversor Universal para começar!</p>
        <a href="index.php?page=home" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs shadow-xs transition">
          Ir para o Conversor
        </a>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse text-xs sm:text-sm">
          <thead>
            <tr class="bg-yellow-100/60 border-b border-yellow-200 text-stone-700 font-bold uppercase text-[11px] tracking-wider">
              <th class="py-3 px-4">Arquivo</th>
              <th class="py-3 px-4">Conversão</th>
              <th class="py-3 px-4">Tamanho</th>
              <th class="py-3 px-4">Data/Hora</th>
              <th class="py-3 px-4 text-right">Ação</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-100 text-stone-800 font-medium">
            <?php foreach ($items as $item): ?>
              <tr class="hover:bg-yellow-50/40 transition">
                <td class="py-3.5 px-4 font-bold text-stone-900 truncate max-w-xs" title="<?= htmlspecialchars($item['nome_original']) ?>">
                  <?= htmlspecialchars($item['nome_original']) ?>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap">
                  <span class="px-2 py-0.5 rounded font-bold bg-yellow-200 text-yellow-950 uppercase text-xs"><?= htmlspecialchars($item['formato_origem']) ?></span>
                  <span class="mx-1 text-stone-400">➔</span>
                  <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-900 uppercase text-xs"><?= htmlspecialchars($item['formato_destino']) ?></span>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-stone-600">
                  <?= Storage::formatBytes((int)$item['tamanho_origem']) ?>
                </td>
                <td class="py-3.5 px-4 whitespace-nowrap font-mono text-xs text-stone-500">
                  <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                </td>
                <td class="py-3.5 px-4 text-right whitespace-nowrap">
                  <a href="api.php?action=download&token=<?= urlencode($item['token_download']) ?>" class="px-3 py-1.5 rounded-xl bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-bold text-xs shadow-2xs transition inline-flex items-center gap-1" download>
                    <span>Baixar</span> ⬇
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>
</section>
