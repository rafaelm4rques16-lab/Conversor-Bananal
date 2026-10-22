<?php
$page_title = 'Logs de Auditoria do Sistema - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

// Filtros
$acao_filtro = trim($_GET['acao'] ?? '');
$busca = trim($_GET['busca'] ?? '');

$sql = "SELECT * FROM logs_auditoria WHERE 1=1";
$params = [];

if (!empty($acao_filtro)) {
    $sql .= " AND acao = :acao";
    $params[':acao'] = $acao_filtro;
}

if (!empty($busca)) {
    $sql .= " AND (detalhes LIKE :busca OR usuario_nome LIKE :busca OR ip_address LIKE :busca)";
    $params[':busca'] = "%{$busca}%";
}

$sql .= " ORDER BY id DESC LIMIT 150";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$logs = $stmt->fetchAll();

// Métricas de auditoria
$total_logs = $pdo->query("SELECT COUNT(*) FROM logs_auditoria")->fetchColumn();
$total_conversoes_log = $pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao = 'CONVERSAO_ARQUIVO'")->fetchColumn();
$total_logins_log = $pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao = 'LOGIN'")->fetchColumn();
$total_exclusoes_log = $pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao LIKE '%EXCLUSAO%' OR acao LIKE '%LIMPEZA%'")->fetchColumn();

// Cores dos badges por tipo de ação
function badge_acao($acao) {
    switch ($acao) {
        case 'LOGIN':
            return 'bg-sky-100 text-sky-800 border-sky-300';
        case 'LOGOUT':
            return 'bg-slate-100 text-slate-700 border-slate-300';
        case 'LOGIN_FALHA':
            return 'bg-rose-100 text-rose-800 border-rose-300';
        case 'CADASTRO':
            return 'bg-emerald-100 text-emerald-800 border-emerald-300';
        case 'CONVERSAO_ARQUIVO':
            return 'bg-banana-100 text-banana-900 border-banana-300';
        case 'ALTERACAO_SENHA':
            return 'bg-amber-100 text-amber-800 border-amber-300';
        case 'ALTERACAO_PERFIL':
            return 'bg-indigo-100 text-indigo-800 border-indigo-300';
        case 'EXCLUSAO_HISTORICO':
        case 'LIMPEZA_HISTORICO':
            return 'bg-red-100 text-red-800 border-red-300';
        default:
            return 'bg-slate-100 text-slate-800 border-slate-200';
    }
}
?>

<main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

  <!-- Cabeçalho de Auditoria -->
  <div class="bg-gradient-to-r from-leaf-200 via-banana-100 to-banana-50 rounded-3xl p-6 sm:p-8 border border-leaf-300 shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
    <div>
      <div class="flex items-center gap-2 mb-2">
        <span class="bg-white text-leaf-900 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1.5">
          <i data-lucide="shield-check" class="w-3.5 h-3.5 text-leaf-600"></i>
          Auditoria Ativa & Compliance
        </span>
        <span class="text-xs text-leaf-800 font-medium">Rastreabilidade Completa</span>
      </div>
      <h2 class="text-3xl font-extrabold font-display text-banana-950">
        Trilha de Auditoria do Sistema 🛡️
      </h2>
      <p class="text-sm text-slate-700 mt-1">
        Registro obrigatório de todas as operações, alterações cadastrais e conversões de arquivos efetuadas.
      </p>
    </div>

    <!-- Exportações -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="backend/export_logs.php?formato=csv" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="file-spreadsheet" class="w-4 h-4 text-emerald-600"></i>
        <span>Exportar CSV</span>
      </a>
      <a href="backend/export_logs.php?formato=json" class="px-4 py-2.5 rounded-xl bg-white hover:bg-slate-50 text-slate-800 border border-slate-300 text-xs font-bold transition flex items-center gap-1.5 shadow-sm">
        <i data-lucide="file-code" class="w-4 h-4 text-sky-600"></i>
        <span>Exportar JSON</span>
      </a>
    </div>
  </div>

  <!-- Cards de Métricas de Auditoria -->
  <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-2xl font-bold shrink-0">
        📋
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Total de Eventos</span>
        <h4 class="text-2xl font-bold font-display text-banana-950"><?= number_format($total_logs) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🔑
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Logins Efetuados</span>
        <h4 class="text-2xl font-bold font-display text-banana-950"><?= number_format($total_logins_log) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-leaf-100 text-leaf-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🔄
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Conversões Logadas</span>
        <h4 class="text-2xl font-bold font-display text-banana-950"><?= number_format($total_conversoes_log) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🗑️
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Exclusões Auditadas</span>
        <h4 class="text-2xl font-bold font-display text-rose-600"><?= number_format($total_exclusoes_log) ?></h4>
      </div>
    </div>
  </div>

  <!-- Filtros -->
  <form method="GET" action="auditoria.php" class="bg-white p-4 rounded-2xl border border-banana-200 shadow-sm flex flex-col md:flex-row gap-3 items-center justify-between">
    <div class="relative w-full md:w-96">
      <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
      <input 
        type="text" 
        name="busca" 
        value="<?= htmlspecialchars($busca) ?>"
        placeholder="Buscar por usuário, detalhe ou endereço IP..."
        class="w-full pl-10 pr-4 py-2 rounded-xl bg-banana-50/50 border border-banana-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-banana-400"
      >
    </div>

    <div class="flex flex-wrap items-center gap-2 w-full md:w-auto justify-end">
      <select name="acao" class="px-3 py-2 rounded-xl bg-banana-50/50 border border-banana-200 text-xs text-slate-700 font-medium focus:outline-none">
        <option value="">Todas as Ações</option>
        <option value="CONVERSAO_ARQUIVO" <?= $acao_filtro === 'CONVERSAO_ARQUIVO' ? 'selected' : '' ?>>CONVERSAO_ARQUIVO</option>
        <option value="LOGIN" <?= $acao_filtro === 'LOGIN' ? 'selected' : '' ?>>LOGIN</option>
        <option value="LOGOUT" <?= $acao_filtro === 'LOGOUT' ? 'selected' : '' ?>>LOGOUT</option>
        <option value="CADASTRO" <?= $acao_filtro === 'CADASTRO' ? 'selected' : '' ?>>CADASTRO</option>
        <option value="ALTERACAO_SENHA" <?= $acao_filtro === 'ALTERACAO_SENHA' ? 'selected' : '' ?>>ALTERACAO_SENHA</option>
        <option value="ALTERACAO_PERFIL" <?= $acao_filtro === 'ALTERACAO_PERFIL' ? 'selected' : '' ?>>ALTERACAO_PERFIL</option>
        <option value="EXCLUSAO_HISTORICO" <?= $acao_filtro === 'EXCLUSAO_HISTORICO' ? 'selected' : '' ?>>EXCLUSAO_HISTORICO</option>
        <option value="LIMPEZA_HISTORICO" <?= $acao_filtro === 'LIMPEZA_HISTORICO' ? 'selected' : '' ?>>LIMPEZA_HISTORICO</option>
        <option value="EXPORTACAO_LOGS" <?= $acao_filtro === 'EXPORTACAO_LOGS' ? 'selected' : '' ?>>EXPORTACAO_LOGS</option>
      </select>

      <button type="submit" class="px-4 py-2 rounded-xl bg-leaf-500 hover:bg-leaf-600 text-white font-bold text-xs transition flex items-center gap-1 shadow-sm">
        <i data-lucide="filter" class="w-3.5 h-3.5"></i> Filtrar Logs
      </button>

      <?php if (!empty($busca) || !empty($acao_filtro)): ?>
        <a href="auditoria.php" class="px-3 py-2 rounded-xl text-slate-500 hover:text-slate-700 text-xs transition">
          Limpar Filtros
        </a>
      <?php endif; ?>
    </div>
  </form>

  <!-- Tabela de Logs de Auditoria -->
  <div class="bg-white rounded-3xl border border-banana-200 shadow-xl overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs">
        <thead class="bg-banana-100/70 border-b border-banana-200 text-banana-950 uppercase font-bold text-[11px]">
          <tr>
            <th class="py-3.5 px-4"># ID</th>
            <th class="py-3.5 px-4">Ação</th>
            <th class="py-3.5 px-4">Usuário</th>
            <th class="py-3.5 px-4">Detalhes da Alteração</th>
            <th class="py-3.5 px-4">IP / Origem</th>
            <th class="py-3.5 px-4">Data e Hora</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 font-normal">
          <?php if (empty($logs)): ?>
            <tr>
              <td colspan="6" class="text-center py-12 text-slate-400">
                <span class="text-4xl block mb-2 select-none">🛡️</span>
                Nenhum log de auditoria encontrado para a busca.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($logs as $log): ?>
              <tr class="hover:bg-banana-50/50 transition">
                <td class="py-3 px-4 font-mono text-slate-400">#<?= $log['id'] ?></td>
                
                <td class="py-3 px-4">
                  <span class="inline-block px-2.5 py-1 rounded-lg border text-[10px] font-bold uppercase tracking-wider <?= badge_acao($log['acao']) ?>">
                    <?= htmlspecialchars($log['acao']) ?>
                  </span>
                </td>

                <td class="py-3 px-4">
                  <span class="font-bold text-banana-950 block"><?= htmlspecialchars($log['usuario_nome'] ?? 'Visitante') ?></span>
                  <?php if (!empty($log['usuario_id'])): ?>
                    <span class="text-[10px] text-slate-400">ID: <?= $log['usuario_id'] ?></span>
                  <?php endif; ?>
                </td>

                <td class="py-3 px-4 text-slate-700 max-w-md">
                  <p class="leading-relaxed"><?= htmlspecialchars($log['detalhes']) ?></p>
                </td>

                <td class="py-3 px-4 text-slate-500 font-mono text-[11px]">
                  <span><?= htmlspecialchars($log['ip_address']) ?></span>
                </td>

                <td class="py-3 px-4 text-slate-500 whitespace-nowrap">
                  <span><?= date('d/m/Y', strtotime($log['criado_em'])) ?></span>
                  <span class="text-[10px] text-slate-400 block"><?= date('H:i:s', strtotime($log['criado_em'])) ?></span>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
