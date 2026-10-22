<?php
$page_title = 'Meu Perfil - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

if (!usuario_esta_logado()) {
    definir_toast('warning', 'Faça login para acessar suas configurações de perfil.');
    header('Location: login.php');
    exit;
}

$u = usuario_autenticado();

// Busca dados atualizados do usuário no banco
$stmt = $pdo->prepare("SELECT id, nome, email, criado_em, atualizado_em FROM usuarios WHERE id = :id LIMIT 1");
$stmt->execute([':id' => $u['id']]);
$usuario_db = $stmt->fetch();

// Estatísticas do usuário
$stmt_stat = $pdo->prepare("SELECT COUNT(*) as total_conversoes, COALESCE(SUM(tamanho_original), 0) as total_bytes FROM conversoes WHERE usuario_id = :id");
$stmt_stat->execute([':id' => $u['id']]);
$stats = $stmt_stat->fetch();

// Histórico recente de auditoria deste usuário
$stmt_logs = $pdo->prepare("SELECT acao, detalhes, ip_address, criado_em FROM logs_auditoria WHERE usuario_id = :id ORDER BY id DESC LIMIT 5");
$stmt_logs->execute([':id' => $u['id']]);
$user_logs = $stmt_logs->fetchAll();
?>

<main class="max-w-5xl mx-auto px-4 py-8 flex-grow w-full space-y-8">
  
  <!-- Banner do Perfil -->
  <div class="bg-gradient-to-r from-banana-300 via-banana-200 to-banana-100 rounded-3xl p-6 sm:p-8 border border-banana-300 shadow-sm flex flex-col sm:flex-row items-center sm:items-start gap-6">
    <div class="w-20 h-20 rounded-3xl bg-banana-400 text-banana-950 font-display text-4xl font-bold flex items-center justify-center shadow-md banana-bounce shrink-0">
      <?= mb_substr($usuario_db['nome'], 0, 1, 'UTF-8') ?>
    </div>
    <div class="text-center sm:text-left flex-grow">
      <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2 mb-1">
        <h2 class="text-2xl sm:text-3xl font-extrabold font-display text-banana-950">
          <?= htmlspecialchars($usuario_db['nome']) ?>
        </h2>
        <span class="text-xs bg-leaf-500 text-white font-bold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
          Membro Bananal
        </span>
      </div>
      <p class="text-sm text-banana-900 font-medium"><?= htmlspecialchars($usuario_db['email']) ?></p>
      <p class="text-xs text-banana-800 mt-2">
        Membro desde: <?= date('d/m/Y \à\s H:i', strtotime($usuario_db['criado_em'])) ?>
      </p>
    </div>

    <!-- Estatísticas rápidas -->
    <div class="flex gap-4">
      <div class="bg-white/80 backdrop-blur-sm px-4 py-3 rounded-2xl border border-banana-300 text-center shadow-sm">
        <span class="block text-2xl font-bold text-banana-950 font-display"><?= $stats['total_conversoes'] ?></span>
        <span class="block text-[11px] font-bold text-banana-800 uppercase">Conversões</span>
      </div>
      <div class="bg-white/80 backdrop-blur-sm px-4 py-3 rounded-2xl border border-banana-300 text-center shadow-sm">
        <span class="block text-2xl font-bold text-leaf-700 font-display"><?= round($stats['total_bytes'] / (1024 * 1024), 1) ?> MB</span>
        <span class="block text-[11px] font-bold text-leaf-800 uppercase">Processados</span>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Formulário: Atualizar Nome -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-md">
      <div class="flex items-center gap-3 mb-6 pb-3 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-banana-100 flex items-center justify-center text-banana-700">
          <i data-lucide="user-check" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold font-display text-lg text-banana-950">Dados Pessoais</h3>
          <p class="text-xs text-slate-400">Atualize seu nome de exibição no sistema</p>
        </div>
      </div>

      <form action="backend/profile_update.php" method="POST" class="space-y-4">
        <input type="hidden" name="acao" value="atualizar_nome">

        <div>
          <label class="block text-xs font-bold text-banana-950 mb-1.5">Nome Completo</label>
          <input 
            type="text" 
            name="nome" 
            required 
            value="<?= htmlspecialchars($usuario_db['nome']) ?>"
            class="w-full px-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-banana-950 mb-1.5">E-mail Cadastrado (Não alterável)</label>
          <input 
            type="email" 
            disabled 
            value="<?= htmlspecialchars($usuario_db['email']) ?>"
            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 bg-slate-50 text-slate-400 text-sm cursor-not-allowed"
          >
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            class="px-6 py-2.5 rounded-xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-xs shadow-md transition"
          >
            Salvar Nome
          </button>
        </div>
      </form>
    </div>

    <!-- Formulário: Alterar Senha Segura (Bcrypt) -->
    <div class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-md">
      <div class="flex items-center gap-3 mb-6 pb-3 border-b border-slate-100">
        <div class="w-10 h-10 rounded-xl bg-rose-100 flex items-center justify-center text-rose-600">
          <i data-lucide="key" class="w-5 h-5"></i>
        </div>
        <div>
          <h3 class="font-bold font-display text-lg text-banana-950">Segurança da Senha</h3>
          <p class="text-xs text-slate-400">Tratamento obrigatório com hash Bcrypt</p>
        </div>
      </div>

      <form action="backend/profile_update.php" method="POST" class="space-y-4">
        <input type="hidden" name="acao" value="alterar_senha">

        <div>
          <label class="block text-xs font-bold text-banana-950 mb-1.5">Senha Atual</label>
          <input 
            type="password" 
            name="senha_atual" 
            required 
            placeholder="Digite a senha atual"
            class="w-full px-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-banana-950 mb-1.5">Nova Senha</label>
          <input 
            type="password" 
            name="nova_senha" 
            required 
            minlength="6"
            placeholder="Mínimo 6 caracteres"
            class="w-full px-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition"
          >
        </div>

        <div>
          <label class="block text-xs font-bold text-banana-950 mb-1.5">Confirmar Nova Senha</label>
          <input 
            type="password" 
            name="confirmar_nova_senha" 
            required 
            minlength="6"
            placeholder="Repita a nova senha"
            class="w-full px-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition"
          >
        </div>

        <div class="pt-2">
          <button 
            type="submit" 
            class="px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-xs shadow-md transition"
          >
            Atualizar Senha
          </button>
        </div>
      </form>
    </div>

  </div>

  <!-- Atividades Recentes de Auditoria deste Usuário -->
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-md">
    <div class="flex items-center justify-between mb-4">
      <div class="flex items-center gap-2">
        <span class="text-xl">🛡️</span>
        <h3 class="font-bold font-display text-lg text-banana-950">Suas Ações Registradas na Auditoria</h3>
      </div>
      <a href="auditoria.php" class="text-xs font-bold text-banana-700 hover:text-banana-900 underline">
        Ver logs gerais
      </a>
    </div>

    <?php if (empty($user_logs)): ?>
      <p class="text-xs text-slate-400 italic py-4">Nenhuma ação recente registrada para este usuário.</p>
    <?php else: ?>
      <div class="divide-y divide-slate-100">
        <?php foreach ($user_logs as $log): ?>
          <div class="py-3 flex items-center justify-between gap-4">
            <div>
              <span class="inline-block px-2 py-0.5 rounded text-[10px] font-bold bg-banana-100 text-banana-900 mb-1">
                <?= htmlspecialchars($log['acao']) ?>
              </span>
              <p class="text-xs text-slate-700"><?= htmlspecialchars($log['detalhes']) ?></p>
            </div>
            <div class="text-right text-[11px] text-slate-400 shrink-0">
              <span><?= date('d/m/Y H:i', strtotime($log['criado_em'])) ?></span><br>
              <span class="font-mono text-[10px]">IP: <?= htmlspecialchars($log['ip_address']) ?></span>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
