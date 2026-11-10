<?php
$page_title = 'Histórico de Conversões - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

// Filtros de busca
$busca = trim($_GET['busca'] ?? '');
$formato_filtro = trim($_GET['formato'] ?? '');
$periodo = trim($_GET['periodo'] ?? '');

$is_mysql = ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'mysql');

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
    if ($is_mysql) {
        $sql .= " AND DATE(c.criado_em) = CURDATE()";
    } else {
        $sql .= " AND DATE(c.criado_em) = DATE('now')";
    }
} elseif ($periodo === '7dias') {
    if ($is_mysql) {
        $sql .= " AND c.criado_em >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
    } else {
        $sql .= " AND c.criado_em >= datetime('now', '-7 days')";
    }
}

$sql .= " ORDER BY c.id DESC LIMIT 100";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$conversoes = $stmt->fetchAll();

// Métricas gerais de histórico
$total_conversoes = (int)$pdo->query("SELECT COUNT(*) FROM conversoes")->fetchColumn();
$total_bytes_convertidos = (float)$pdo->query("SELECT COALESCE(SUM(tamanho_convertido), 0) FROM conversoes")->fetchColumn();
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
        Consulte, filtre e audite todas as conversões de arquivos efetuadas no sistema.
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
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Volume Processado</span>
        <h4 class="text-2xl font-bold font-display text-leaf-700"><?= formatar_tamanho($total_bytes_convertidos) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-amber-100 text-amber-800 flex items-center justify-center text-2xl font-bold shrink-0">
        ⭐
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Formato Mais Usado</span>
        <h4 class="text-2xl font-bold font-display text-banana-950 uppercase"><?= $formato_top ? htmlspecialchars($formato_top['formato_destino']) : 'Nenhum' ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🛡️
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Status Segurança</span>
        <h4 class="text-lg font-bold font-display text-sky-900">100% Auditado</h4>
      </div>
    </div>
  </div>

  <!-- Barra de Filtros e Busca -->
  <div class="bg-white rounded-3xl p-6 border border-banana-200 shadow-sm">
    <form method="GET" action="historico.php" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
      
      <div class="sm:col-span-5 relative">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
        <input 
          type="text" 
          name="busca" 
          value="<?= htmlspecialchars($busca) ?>" 
          placeholder="Buscar por nome do arquivo ou formato..."
          class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-xs outline-none bg-banana-50/40"
        >
      </div>

      <div class="sm:col-span-3">
        <select name="formato" class="w-full px-3 py-2.5 rounded-2xl border border-banana-200 focus:border-banana-500 text-xs outline-none bg-white font-medium text-slate-700">
          <option value="">Todos os Formatos</option>
          <option value="png" <?= $formato_filtro === 'png' ? 'selected' : '' ?>>PNG</option>
          <option value="jpg" <?= $formato_filtro === 'jpg' ? 'selected' : '' ?>>JPEG / JPG</option>
          <option value="webp" <?= $formato_filtro === 'webp' ? 'selected' : '' ?>>WEBP</option>
          <option value="pdf" <?= $formato_filtro === 'pdf' ? 'selected' : '' ?>>PDF</option>
          <option value="wav" <?= $formato_filtro === 'wav' ? 'selected' : '' ?>>WAV</option>
          <option value="mp3" <?= $formato_filtro === 'mp3' ? 'selected' : '' ?>>MP3</option>
          <option value="json" <?= $formato_filtro === 'json' ? 'selected' : '' ?>>JSON</option>
          <option value="csv" <?= $formato_filtro === 'csv' ? 'selected' : '' ?>>CSV</option>
        </select>
      </div>

      <div class="sm:col-span-2">
        <select name="periodo" class="w-full px-3 py-2.5 rounded-2xl border border-banana-200 focus:border-banana-500 text-xs outline-none bg-white font-medium text-slate-700">
          <option value="">Todo o Período</option>
          <option value="hoje" <?= $periodo === 'hoje' ? 'selected' : '' ?>>Hoje</option>
          <option value="7dias" <?= $periodo === '7dias' ? 'selected' : '' ?>>Últimos 7 dias</option>
        </select>
      </div>

      <div class="sm:col-span-2 flex gap-2">
        <button type="submit" class="w-full py-2.5 rounded-2xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-xs shadow-sm transition">
          Filtrar
        </button>
        <?php if (!empty($busca) || !empty($formato_filtro) || !empty($periodo)): ?>
          <a href="historico.php" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Limpar Filtros">
            ✕
          </a>
        <?php endif; ?>
      </div>

    </form>
  </div>

  <!-- Tabela de Histórico -->
  <div class="bg-white rounded-3xl border border-banana-200 shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-banana-100/70 border-b border-banana-200 text-banana-950 font-bold uppercase tracking-wider text-[11px]">
          <tr>
            <th class="py-3.5 px-4"># ID</th>
            <th class="py-3.5 px-4">Arquivo Original</th>
            <th class="py-3.5 px-4">Conversão</th>
            <th class="py-3.5 px-4">Tamanhos (Orig ➔ Dest)</th>
            <th class="py-3.5 px-4">Usuário</th>
            <th class="py-3.5 px-4">Status</th>
            <th class="py-3.5 px-4">Data / Hora</th>
            <th class="py-3.5 px-4 text-right">Ações</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-banana-100 font-medium">
          <?php if (empty($conversoes)): ?>
            <tr>
              <td colspan="8" class="text-center py-12 text-slate-400 italic">
                <div class="flex flex-col items-center justify-center gap-2">
                  <span class="text-4xl">🍌</span>
                  <span>Nenhum registro encontrado no histórico.</span>
                  <a href="index.php" class="text-banana-700 font-bold underline text-xs mt-1">
                    Fazer sua primeira conversão agora
                  </a>
                </div>
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($conversoes as $row): ?>
              <tr class="hover:bg-banana-50/60 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-slate-400">
                  #<?= $row['id'] ?>
                </td>
                <td class="py-3.5 px-4 font-bold text-banana-950">
                  <div class="flex items-center gap-2 max-w-xs truncate" title="<?= htmlspecialchars($row['nome_original']) ?>">
                    <span>🍌</span>
                    <span class="truncate"><?= htmlspecialchars($row['nome_original']) ?></span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="inline-flex items-center gap-1.5 font-bold uppercase">
                    <span class="bg-banana-100 text-banana-800 px-2 py-0.5 rounded-lg text-[10px] border border-banana-200">
                      <?= htmlspecialchars($row['formato_origem']) ?>
                    </span>
                    <span class="text-banana-600">➔</span>
                    <span class="bg-leaf-100 text-leaf-800 px-2 py-0.5 rounded-lg text-[10px] border border-leaf-200">
                      <?= htmlspecialchars($row['formato_destino']) ?>
                    </span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-slate-500">
                  <?= formatar_tamanho($row['tamanho_original']) ?> ➔ <strong class="text-banana-900"><?= formatar_tamanho($row['tamanho_convertido']) ?></strong>
                </td>
                <td class="py-3.5 px-4">
                  <?php if (!empty($row['usuario_nome'])): ?>
                    <span class="font-bold text-slate-800"><?= htmlspecialchars($row['usuario_nome']) ?></span>
                  <?php else: ?>
                    <span class="text-slate-400 italic">Visitante Convidado</span>
                  <?php endif; ?>
                </td>
                <td class="py-3.5 px-4">
                  <?php if ($row['status'] === 'sucesso'): ?>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded-full border border-emerald-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Concluído
                    </span>
                  <?php else: ?>
                    <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700 bg-rose-50 px-2 py-0.5 rounded-full border border-rose-200">
                      <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Falha
                    </span>
                  <?php endif; ?>
                </td>
                <td class="py-3.5 px-4 text-slate-500 text-[11px]">
                  <?= date('d/m/Y H:i', strtotime($row['criado_em'])) ?>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <button 
                    onclick="confirmarExclusaoRegistro(<?= $row['id'] ?>, '<?= htmlspecialchars(addslashes($row['nome_original'])) ?>')"
                    class="p-1.5 text-slate-400 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition"
                    title="Excluir este registro (com auditoria)"
                  >
                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                  </button>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<script>
  // Exclusão individual com auditoria
  async function confirmarExclusaoRegistro(id, nome) {
    const confirmou = await bananalConfirm(
      'Excluir do Histórico?',
      `Deseja realmente remover o registro #${id} (${nome}) do histórico?\nEsta ação será registrada nos logs de auditoria.`,
      'Sim, Excluir',
      'Cancelar'
    );

    if (confirmou) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'backend/history_delete.php';

      const actionInput = document.createElement('input');
      actionInput.type = 'hidden';
      actionInput.name = 'action';
      actionInput.value = 'delete_single';
      form.appendChild(actionInput);

      const idInput = document.createElement('input');
      idInput.type = 'hidden';
      idInput.name = 'id';
      idInput.value = id;
      form.appendChild(idInput);

      document.body.appendChild(form);
      form.submit();
    }
  }

  // Limpeza completa do histórico
  async function confirmarLimpezaHistorico() {
    const confirmou = await bananalConfirm(
      'Limpar Todo o Histórico?',
      'Esta ação removerá todos os registros de conversão da sua conta/sessão e será registrada nos logs de auditoria.',
      'Sim, Limpar Tudo',
      'Cancelar'
    );

    if (confirmou) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.action = 'backend/history_delete.php';

      const actionInput = document.createElement('input');
      actionInput.type = 'hidden';
      actionInput.name = 'action';
      actionInput.value = 'clear_all';
      form.appendChild(actionInput);

      document.body.appendChild(form);
      form.submit();
    }
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
