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
$total_logs = (int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria")->fetchColumn();
$total_conversoes_log = (int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao = 'CONVERSAO_ARQUIVO'")->fetchColumn();
$total_logins_log = (int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao = 'LOGIN'")->fetchColumn();
$total_exclusoes_log = (int)$pdo->query("SELECT COUNT(*) FROM logs_auditoria WHERE acao LIKE '%EXCLUSAO%' OR acao LIKE '%LIMPEZA%'")->fetchColumn();

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
        Registro obrigatório de todas as operações, alterações cadastrais e conversões efetuadas.
      </p>
    </div>

    <!-- Exportações de Logs -->
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
      <div class="w-12 h-12 rounded-xl bg-leaf-100 text-leaf-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🔄
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Conversões</span>
        <h4 class="text-2xl font-bold font-display text-leaf-700"><?= number_format($total_conversoes_log) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-sky-100 text-sky-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🔑
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Autenticações</span>
        <h4 class="text-2xl font-bold font-display text-sky-900"><?= number_format($total_logins_log) ?></h4>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border border-banana-200 shadow-sm flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-rose-100 text-rose-800 flex items-center justify-center text-2xl font-bold shrink-0">
        🗑️
      </div>
      <div>
        <span class="text-xs text-slate-400 font-bold uppercase tracking-wider">Remoções</span>
        <h4 class="text-2xl font-bold font-display text-rose-700"><?= number_format($total_exclusoes_log) ?></h4>
      </div>
    </div>
  </div>

  <!-- Filtro de Logs -->
  <div class="bg-white rounded-3xl p-6 border border-banana-200 shadow-sm">
    <form method="GET" action="auditoria.php" class="grid grid-cols-1 sm:grid-cols-12 gap-4 items-center">
      
      <div class="sm:col-span-6 relative">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
        <input 
          type="text" 
          name="busca" 
          value="<?= htmlspecialchars($busca) ?>" 
          placeholder="Buscar nos detalhes, nome do usuário ou IP..."
          class="w-full pl-10 pr-4 py-2.5 rounded-2xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-xs outline-none bg-banana-50/40"
        >
      </div>

      <div class="sm:col-span-4">
        <select name="acao" class="w-full px-3 py-2.5 rounded-2xl border border-banana-200 focus:border-banana-500 text-xs outline-none bg-white font-medium text-slate-700">
          <option value="">Todas as Ações Auditadas</option>
          <option value="LOGIN" <?= $acao_filtro === 'LOGIN' ? 'selected' : '' ?>>LOGIN (Sucesso)</option>
          <option value="LOGIN_FALHA" <?= $acao_filtro === 'LOGIN_FALHA' ? 'selected' : '' ?>>LOGIN_FALHA (Tentativa frustrada)</option>
          <option value="LOGOUT" <?= $acao_filtro === 'LOGOUT' ? 'selected' : '' ?>>LOGOUT (Desconexão)</option>
          <option value="CADASTRO" <?= $acao_filtro === 'CADASTRO' ? 'selected' : '' ?>>CADASTRO (Novo Usuário)</option>
          <option value="CONVERSAO_ARQUIVO" <?= $acao_filtro === 'CONVERSAO_ARQUIVO' ? 'selected' : '' ?>>CONVERSAO_ARQUIVO (Processamento)</option>
          <option value="ALTERACAO_PERFIL" <?= $acao_filtro === 'ALTERACAO_PERFIL' ? 'selected' : '' ?>>ALTERACAO_PERFIL (Atualização de dados)</option>
          <option value="ALTERACAO_SENHA" <?= $acao_filtro === 'ALTERACAO_SENHA' ? 'selected' : '' ?>>ALTERACAO_SENHA (Troca de senha Bcrypt)</option>
          <option value="EXCLUSAO_HISTORICO" <?= $acao_filtro === 'EXCLUSAO_HISTORICO' ? 'selected' : '' ?>>EXCLUSAO_HISTORICO (Registro apagado)</option>
          <option value="LIMPEZA_HISTORICO" <?= $acao_filtro === 'LIMPEZA_HISTORICO' ? 'selected' : '' ?>>LIMPEZA_HISTORICO (Limpeza completa)</option>
        </select>
      </div>

      <div class="sm:col-span-2 flex gap-2">
        <button type="submit" class="w-full py-2.5 rounded-2xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-xs shadow-sm transition">
          Filtrar
        </button>
        <?php if (!empty($busca) || !empty($acao_filtro)): ?>
          <a href="auditoria.php" class="p-2.5 rounded-2xl bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs font-bold transition" title="Limpar Filtros">
            ✕
          </a>
        <?php endif; ?>
      </div>

    </form>
  </div>

  <!-- Tabela de Logs de Auditoria -->
  <div class="bg-white rounded-3xl border border-banana-200 shadow-md overflow-hidden">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs text-slate-600">
        <thead class="bg-banana-100/70 border-b border-banana-200 text-banana-950 font-bold uppercase tracking-wider text-[11px]">
          <tr>
            <th class="py-3.5 px-4"># ID</th>
            <th class="py-3.5 px-4">Ação</th>
            <th class="py-3.5 px-4">Usuário</th>
            <th class="py-3.5 px-4">Detalhes da Operação</th>
            <th class="py-3.5 px-4">Endereço IP</th>
            <th class="py-3.5 px-4">Data / Hora</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-banana-100 font-medium">
          <?php if (empty($logs)): ?>
            <tr>
              <td colspan="6" class="text-center py-12 text-slate-400 italic">
                Nenhum evento registrado nos critérios selecionados.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($logs as $log): ?>
              <tr class="hover:bg-banana-50/60 transition">
                <td class="py-3.5 px-4 font-mono font-bold text-slate-400">
                  #<?= $log['id'] ?>
                </td>
                <td class="py-3.5 px-4">
                  <span class="inline-block px-2.5 py-0.5 rounded-full text-[10px] font-bold border uppercase <?= badge_acao($log['acao']) ?>">
                    <?= htmlspecialchars($log['acao']) ?>
                  </span>
                </td>
                <td class="py-3.5 px-4 font-bold text-slate-800">
                  <?= htmlspecialchars($log['usuario_nome'] ?? 'Visitante') ?>
                </td>
                <td class="py-3.5 px-4 text-slate-700 max-w-md truncate" title="<?= htmlspecialchars($log['detalhes']) ?>">
                  <?= htmlspecialchars($log['detalhes']) ?>
                </td>
                <td class="py-3.5 px-4 font-mono text-[11px] text-slate-500">
                  <?= htmlspecialchars($log['ip_address']) ?>
                </td>
                <td class="py-3.5 px-4 text-slate-500 text-[11px] whitespace-nowrap">
                  <?= date('d/m/Y H:i:s', strtotime($log['criado_em'])) ?>
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
