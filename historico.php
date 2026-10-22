<?php
$page_title = 'Histórico de Conversões - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

// Filtros de busca
$busca = trim($_GET['busca'] ?? '');
$formato_filtro = trim($_GET['formato'] ?? '');
$periodo = trim($_GET['periodo'] ?? '');

// Constrói a consulta dinâmica com PDO seguro
$sql = "SELECT c.*, u.nome as usuario_nome, u.email as usuario_email 
        FROM conversoes c 
        LEFT JOIN usuarios u ON c.usuario_id = u.id 
        WHERE 1=1";
$params = [];

if (!empty($busca)) {
    $sql .= " AND (c.nome_original LIKE :busca OR c.formato_origem LIKE :busca OR c.formato_destino LIKE :busca)";
    $params[':busca'] = "%{$busca}%";
}

if (!empty($formato_filtro)) {
    $sql .= " AND c.formato_destino = :formato";
    $params[':formato'] = $formato_filtro;
}

if ($periodo === 'hoje') {
    $sql .= " AND DATE(c.criado_em) = CURDATE()";
} elseif ($periodo === '7dias') {
    $sql .= " AND c.criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
}

$sql .= " ORDER BY c.id DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$conversoes = $stmt->fetchAll();

// Métricas gerais de histórico
$total_conversoes = $pdo->query("SELECT COUNT(*) FROM conversoes")->fetchColumn();
$total_bytes_convertidos = $pdo->query("SELECT COALESCE(SUM(tamanho_convertido), 0) FROM conversoes")->fetchColumn();
$formato_top = $pdo->query("SELECT formato_destino, COUNT(*) as qtd FROM conversoes GROUP BY formato_destino ORDER BY qtd DESC LIMIT 1")->fetch();

function formatar_tamanho($bytes) {
    if ($bytes <= 0) return '0 B';
    $unidades = ['B', 'KB', 'MB', 'GB'];
    $i = floor(log($bytes, 1024));
    return round($bytes / pow(1024, $i), 2) . ' ' . $unidades[$i];
}
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

  <!-- Cabeçalho da Página -->
  <div class="bg-gradient-to-r from-banana-200 via-banana-100 to-banana-50 rounded-3xl p-6 sm:p-8 border border-banana-300 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <div class="flex items-center gap-2 mb-2">
        <span class="bg-white text-banana-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
          📜 Histórico de Atividades
        </span>
        <span class="text-xs text-banana-800 font-medium">Banco de Dados MySQL & Auditoria</span>
      </div>
      <h2 class="text-3xl font-extrabold font-display text-banana-950">
        Histórico das Conversões 🍌
      </h2>
      <p class="text-sm text-banana-900 mt-1">
        Consulte, filtre e audite todas as conversões de arquivos realizadas no sistema.
      </p>
    </div>

    <!-- Botões de Ação Rápida -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="auditoria.php" class="px-4 py-2.5 rounded-xl bg-white hover:bg-banana-50 text-banana-900 border border-banana-300 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="shield-check" class="w-4 h-4 text-leaf-600"></i>
        <span>Ver Logs de Auditoria</span>
      </a>
      <button onclick="confirmarLimpezaHistorico()" class="px-4 py-2.5 rounded-xl bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold transition flex items-center gap-1.5">
        <i data-lucide="trash-2" class="w-4 h-4"></i>
        <span>Limpar Histórico</span>
      </button>
    </div>
  </div>

  <!-- Cards de Métricas -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🔄
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total Registrado</span>
        <h4 class="text-2xl font-bold font-display text-banana-950"><?= number_format($total_conversoes) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-leaf-100 text-leaf-800 flex items-center justify-center text-2xl font-bold shrink-0">
        📦
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Dados Convertidos</span>
        <h4 class="text-2xl font-bold font-display text-banana-950"><?= formatar_tamanho($total_bytes_convertidos) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl font-bold shrink-0">
        ⭐
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Formato Top</span>
        <h4 class="text-2xl font-bold font-display text-banana-950 uppercase">
          <?= htmlspecialchars($formato_top['formato_destino'] ?? 'PNG') ?>
        </h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🛡️
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Taxa de Sucesso</span>
        <h4 class="text-2xl font-bold font-display text-emerald-700">100%</h4>
      </div>
    </div>
  </div>

  <!-- Barra de Filtros e Busca -->
  <form method="GET" action="historico.php" class="bg-white p-4 rounded-2xl border border-banana-200 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
    <div class="relative w-full md:w-96">
      <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
      <input 
        type="text" 
        name="busca" 
        value="<?= htmlspecialchars($busca) ?>"
        placeholder="Buscar por nome do arquivo ou formato..."
        class="w-full pl-10 pr-4 py-2 rounded-xl bg-banana-50/50 border border-banana-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-banana-400"
      >
    </div>

    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
      <select name="formato" class="px-3 py-2 rounded-xl bg-banana-50/50 border border-banana-200 text-xs text-slate-700 font-medium focus:outline-none">
        <option value="">Todos os Formatos</option>
        <option value="png" <?= $formato_filtro === 'png' ? 'selected' : '' ?>>PNG</option>
        <option value="jpg" <?= $formato_filtro === 'jpg' ? 'selected' : '' ?>>JPG</option>
        <option value="webp" <?= $formato_filtro === 'webp' ? 'selected' : '' ?>>WEBP</option>
        <option value="svg" <?= $formato_filtro === 'svg' ? 'selected' : '' ?>>SVG</option>
        <option value="pdf" <?= $formato_filtro === 'pdf' ? 'selected' : '' ?>>PDF</option>
        <option value="wav" <?= $formato_filtro === 'wav' ? 'selected' : '' ?>>WAV</option>
        <option value="json" <?= $formato_filtro === 'json' ? 'selected' : '' ?>>JSON</option>
        <option value="csv" <?= $formato_filtro === 'csv' ? 'selected' : '' ?>>CSV</option>
      </select>

      <select name="periodo" class="px-3 py-2 rounded-xl bg-banana-50/50 border border-banana-200 text-xs text-slate-700 font-medium focus:outline-none">
        <option value="">Todo o Período</option>
        <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
        <option value="7dias" <?= $periodo === '7dias' ? 'selected' : '' ?>>Últimos 7 dias</option>
      </select>

      <button type="submit" class="px-4 py-2 rounded-xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-xs transition flex items-center gap-1">
        <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filtrar
      </button>

      <?php if (!empty($busca) || !empty($formato_filtro) || !empty($periodo)): ?>
        <a href="historico.php" class="px-3 py-2 rounded-xl text-slate-500 hover:text-slate-700 text-xs transition">
          Limpar Filtros
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabela de Histórico (Mobile First) -->
  <div class="bg-white rounded-3xl border border-banana-200 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-banana-100/70 border-b border-banana-200 text-banana-950 uppercase font-bold text-[11px]">
          <tr>
            <th class="py-3.5 px-4"># ID</th>
            <th class="py-3.5 px-4">Arquivo Original</th>
            <th class="py-3.5 px-4">Conversão</th>
            <th class="py-3.5 px-4">Tamanhos</th>
            <th class="py-3.5 px-4">Usuário</th>
            <th class="py-3.5 px-4">Data e Hora</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
          <?php if (empty($conversoes)): ?>
            <tr>
              <td colspan="8" class="text-center py-12 text-slate-400">
                <span class="text-4xl block mb-2 select-none">🍌</span>
                Nenhuma conversão encontrada para os filtros selecionados.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($conversoes as $conv): ?>
              <tr class="hover:bg-banana-50/50 transition">
                <td class="py-3 px-4 font-mono text-slate-400">#<?= $conv['id'] ?></td>
                
                <td class="py-3 px-4">
                  <div class="flex items-center gap-2">
                    <span class="text-base">📄</span>
                    <strong class="text-banana-950 truncate max-w-[180px] block" title="<?= htmlspecialchars($conv['nome_original']) ?>">
                      <?= htmlspecialchars($conv['nome_original']) ?>
                    </strong>
                  </div>
                </td>

                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 font-bold text-banana-900 bg-banana-100 px-2.5 py-1 rounded-lg uppercase">
                    <?= htmlspecialchars($conv['formato_origem']) ?>
                    <i data-lucide="arrow-right" class="w-3 h-3 text-banana-600"></i>
                    <strong class="text-leaf-700"><?= htmlspecialchars($conv['formato_destino']) ?></strong>
                  </span>
                </td>

                <td class="py-3 px-4 text-slate-600">
                  <span class="block"><?= formatar_tamanho($conv['tamanho_original']) ?> ➔ <strong class="text-slate-800"><?= formatar_tamanho($conv['tamanho_convertido']) ?></strong></span>
                </td>

                <td class="py-3 px-4">
                  <?php if (!empty($conv['usuario_nome'])): ?>
                    <span class="inline-flex items-center gap-1 font-semibold text-banana-900">
                      <span class="w-5 h-5 rounded-full bg-banana-300 text-banana-950 flex items-center justify-center text-[10px] font-bold">
                        <?= mb_substr($conv['usuario_nome'], 0, 1) ?>
                      </span>
                      <?= htmlspecialchars($conv['usuario_nome']) ?>
                    </span>
                  <?php else: ?>
                    <span class="text-slate-400 italic">Convidado</span>
                  <?php endif; ?>
                </td>

                <td class="py-3 px-4 text-slate-500">
                  <span><?= date('d/m/Y', strtotime($conv['criado_em'])) ?></span>
                  <span class="text-[10px] text-slate-400 block"><?= date('H:i:s', strtotime($conv['criado_em'])) ?></span>
                </td>

                <td class="py-3 px-4">
                  <span class="inline-flex items-center gap-1 bg-emerald-100 text-emerald-800 text-[10px] font-bold px-2 py-0.5 rounded-full uppercase">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                    <?= htmlspecialchars($conv['status']) ?>
                  </span>
                </td>

                <td class="py-3 px-4 text-right">
                  <div class="flex items-center justify-end gap-1.5">
                    <button 
                      onclick="bananalAlert('Detalhes da Conversão #<?= $conv['id'] ?>', 'Arquivo: <?= addslashes(htmlspecialchars($conv['nome_original'])) ?>\nOrigem: .<?= $conv['formato_origem'] ?> (<?= formatar_tamanho($conv['tamanho_original']) ?>)\nDestino: .<?= $conv['formato_destino'] ?> (<?= formatar_tamanho($conv['tamanho_convertido']) ?>)\nData: <?= date('d/m/Y H:i:s', strtotime($conv['criado_em'])) ?>\nIP: <?= $conv['ip_address'] ?? 'N/A' ?>')"
                      class="p-1.5 text-slate-400 hover:text-banana-800 hover:bg-banana-100 rounded-lg transition"
                      title="Ver Detalhes"
                    >
                      <i data-lucide="info" class="w-4 h-4"></i>
                    </button>

                    <button 
                      onclick="confirmarExclusaoItem(<?= $conv['id'] ?>, '<?= addslashes(htmlspecialchars($conv['nome_original'])) ?>')"
                      class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                      title="Excluir Registro"
                    >
                      <i data-lucide="trash-2" class="w-4 h-4"></i>
                    </button>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<!-- Formulários ocultos para exclusão acionados via Modal -->
<form id="formDeleteSingle" action="backend/history_delete.php" method="POST" class="hidden">
  <input type="hidden" name="action" value="delete_single">
  <input type="hidden" name="id" id="deleteTargetId">
</form>

<form id="formClearAll" action="backend/history_delete.php" method="POST" class="hidden">
  <input type="hidden" name="action" value="clear_all">
</form>

<script>
  async function confirmarExclusaoItem(id, nomeArquivo) {
    const confirmou = await bananalConfirm(
      'Excluir Conversão do Histórico',
      `Tem certeza que deseja excluir o registro de "${nomeArquivo}"?\n\nEsta alteração será registrada no log de auditoria.`,
      'Sim, Excluir',
      'Cancelar',
      true
    );

    if (confirmou) {
      document.getElementById('deleteTargetId').value = id;
      document.getElementById('formDeleteSingle').submit();
    }
  }

  async function confirmarLimpezaHistorico() {
    const confirmou = await bananalConfirm(
      'Limpar Todo o Histórico',
      'Atenção: todos os registros do seu histórico serão excluídos.\nUm evento de auditoria será gerado com seu usuário e IP.',
      'Sim, Limpar Tudo',
      'Cancelar',
      true
    );

    if (confirmou) {
      document.getElementById('formClearAll').submit();
    }
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
