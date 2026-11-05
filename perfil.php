<?php
/**
 * Painel de Perfil do Usuário - Conversor Bananal
 */
declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/services/StorageService.php';

// Redireciona para login se não autenticado
if (!isset($_SESSION['usuario']['id'])) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$pdo = getDBConnection();

// Busca estatísticas do usuário
$stmtStats = $pdo->prepare("
    SELECT 
        COUNT(id) as total_conversoes,
        COALESCE(SUM(tamanho_origem), 0) as total_bytes,
        COUNT(CASE WHEN status = 'concluido' THEN 1 END) as total_sucesso
    FROM conversoes 
    WHERE usuario_id = :uid OR session_id = :sid
");
$stmtStats->execute([
    ':uid' => $usuario['id'],
    ':sid' => getSessionUserId()
]);
$stats = $stmtStats->fetch() ?: ['total_conversoes' => 0, 'total_bytes' => 0, 'total_sucesso' => 0];
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meu Perfil - Conversor Bananal 🍌</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-900 font-sans flex flex-col justify-between">

  <!-- Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 flex-1 w-full">
    
    <!-- Topo do Perfil -->
    <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-8 mb-8 flex flex-col sm:flex-row items-center justify-between gap-6">
      <div class="flex items-center gap-4">
        <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-3xl bg-yellow-400 text-stone-950 font-black text-3xl sm:text-4xl flex items-center justify-center shadow-md select-none">
          <?= strtoupper(substr($usuario['nome'], 0, 1)) ?>
        </div>
        <div>
          <div class="flex items-center gap-2">
            <h1 class="text-xl sm:text-2xl font-black text-stone-900"><?= htmlspecialchars($usuario['nome']) ?></h1>
            <span class="text-[10px] uppercase font-bold px-2 py-0.5 rounded-full bg-yellow-200 text-yellow-900"><?= htmlspecialchars($usuario['cargo'] ?? 'usuario') ?></span>
          </div>
          <p class="text-xs sm:text-sm text-stone-500 font-mono mt-0.5"><?= htmlspecialchars($usuario['email']) ?></p>
        </div>
      </div>

      <div class="flex items-center gap-2 w-full sm:w-auto">
        <a href="historico.php" class="flex-1 sm:flex-initial text-xs font-bold px-4 py-2.5 rounded-xl bg-yellow-100 hover:bg-yellow-200 text-yellow-950 transition border border-yellow-300 text-center">
          🕒 Ver Meu Histórico
        </a>
        <a href="backend/auth_logout.php" class="flex-1 sm:flex-initial text-xs font-bold px-4 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-yellow-300 transition text-center shadow-xs">
          Sair
        </a>
      </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs text-center sm:text-left">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Total de Conversões</span>
        <div class="text-2xl sm:text-3xl font-black text-stone-900 mt-1"><?= $stats['total_conversoes'] ?></div>
        <p class="text-xs text-stone-500 mt-1">arquivos processados</p>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs text-center sm:text-left">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Sucesso na Conversão</span>
        <div class="text-2xl sm:text-3xl font-black text-emerald-600 mt-1"><?= $stats['total_sucesso'] ?></div>
        <p class="text-xs text-stone-500 mt-1">sem falhas registradas</p>
      </div>

      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs text-center sm:text-left">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Volume Total</span>
        <div class="text-2xl sm:text-3xl font-black text-yellow-600 mt-1"><?= StorageService::formatBytes((int)$stats['total_bytes']) ?></div>
        <p class="text-xs text-stone-500 mt-1">trafegados com segurança</p>
      </div>
    </div>

    <!-- Formulário de Atualização Cadastral e Senha -->
    <div class="bg-white rounded-3xl border-2 border-yellow-200 shadow-md p-6 sm:p-8">
      <h3 class="text-lg font-black text-stone-900 mb-4 flex items-center gap-2">
        <span>⚙️</span>
        <span>Atualizar Dados & Senha</span>
      </h3>

      <form id="form-perfil" class="space-y-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Nome Completo</label>
            <input type="text" name="nome" required value="<?= htmlspecialchars($usuario['nome']) ?>"
                   class="w-full px-4 py-3 rounded-xl border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">E-mail</label>
            <input type="email" name="email" required value="<?= htmlspecialchars($usuario['email']) ?>"
                   class="w-full px-4 py-3 rounded-xl border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm font-medium">
          </div>
        </div>

        <div class="pt-4 border-t border-yellow-100">
          <p class="text-xs font-bold text-stone-600 mb-3">Preencha abaixo apenas se desejar alterar sua senha:</p>
          
          <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
            <div>
              <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Senha Atual</label>
              <input type="password" name="senha_atual" placeholder="••••••••"
                     class="w-full px-3 py-2.5 rounded-xl border border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Nova Senha</label>
              <input type="password" name="nova_senha" minlength="6" placeholder="Mínimo 6 dígitos"
                     class="w-full px-3 py-2.5 rounded-xl border border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm">
            </div>

            <div>
              <label class="block text-[11px] font-bold text-stone-500 uppercase tracking-wider mb-1">Confirmar Nova Senha</label>
              <input type="password" name="conf_senha" minlength="6" placeholder="Repita a nova senha"
                     class="w-full px-3 py-2.5 rounded-xl border border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm">
            </div>
          </div>
        </div>

        <div class="pt-4 flex justify-end">
          <button type="submit" id="btn-salvar-perfil" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-900 font-black rounded-xl shadow-md transition transform active:scale-95 text-sm flex items-center gap-2">
            <span>Salvar Alterações</span>
            <span>🍌</span>
          </button>
        </div>
      </form>
    </div>

  </main>

  <!-- Rodapé -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/toast.js"></script>
  <script src="assets/js/modal.js"></script>
  <script>
    document.getElementById('form-perfil').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-salvar-perfil');
      btn.disabled = true;
      btn.classList.add('opacity-50');

      const formData = new FormData(e.target);

      try {
        const res = await fetch('backend/profile_update.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (res.ok && data.success) {
          BananalToast.success(data.message || 'Perfil salvo com sucesso!');
          setTimeout(() => location.reload(), 1000);
        } else {
          BananalToast.error(data.error || 'Erro ao atualizar dados.');
          btn.disabled = false;
          btn.classList.remove('opacity-50');
        }
      } catch (err) {
        BananalToast.error('Erro de conexão com o servidor.');
        btn.disabled = false;
        btn.classList.remove('opacity-50');
      }
    });
  </script>
</body>
</html>
