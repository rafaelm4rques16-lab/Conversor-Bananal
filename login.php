<?php
/**
 * Tela de Autenticação - Conversor Bananal
 */
declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';

// Se já estiver logado, redireciona para a página principal
if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login - Conversor Bananal 🍌</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-900 font-sans flex flex-col justify-between">

  <!-- Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="flex-1 flex items-center justify-center p-4 sm:p-6 my-8">
    <div class="bg-white w-full max-w-md rounded-3xl border-2 border-yellow-300 shadow-xl p-6 sm:p-8 relative overflow-hidden">
      
      <!-- Detalhe decorativo no topo -->
      <div class="absolute -top-10 -right-10 w-28 h-28 bg-yellow-200/50 rounded-full blur-xl pointer-events-none"></div>

      <div class="text-center mb-6">
        <div class="w-16 h-16 mx-auto rounded-3xl bg-yellow-400 flex items-center justify-center text-4xl shadow-md mb-3 select-none">
          🍌
        </div>
        <h1 class="text-2xl font-black text-stone-900">Acesse sua Conta</h1>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Gerencie seu histórico e arquivos convertidos</p>
      </div>

      <!-- Badge de Credenciais de Demonstração -->
      <div class="mb-5 p-3 rounded-2xl bg-yellow-100/70 border border-yellow-300 text-xs text-stone-800">
        <div class="font-bold text-yellow-950 flex items-center gap-1.5 mb-1">
          <span>💡</span> Credenciais de Teste / Administrador:
        </div>
        <div class="font-mono text-[11px] text-stone-700 space-y-0.5">
          <div>E-mail: <strong class="text-stone-900">admin@bananal.com</strong></div>
          <div>Senha: <strong class="text-stone-900">bananal123</strong></div>
        </div>
      </div>

      <!-- Formulário de Login -->
      <form id="form-login" class="space-y-4">
        <div>
          <label for="email" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">E-mail</label>
          <input type="email" id="email" name="email" required placeholder="seu@email.com" value="admin@bananal.com"
                 class="w-full px-4 py-3 rounded-xl border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm font-medium">
        </div>

        <div>
          <label for="senha" class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-1">Senha</label>
          <input type="password" id="senha" name="senha" required placeholder="••••••••" value="bananal123"
                 class="w-full px-4 py-3 rounded-xl border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition bg-stone-50 text-sm font-medium">
        </div>

        <button type="submit" id="btn-submit-login" class="w-full py-3.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl shadow-md transition transform active:scale-95 text-sm sm:text-base flex items-center justify-center gap-2">
          <span>Entrar no Bananal</span>
          <span>🍌</span>
        </button>
      </form>

      <div class="mt-6 pt-5 border-t border-yellow-100 text-center text-xs text-stone-600">
        Não possui uma conta? 
        <a href="registro.php" class="font-bold text-yellow-800 hover:text-yellow-950 underline ml-1">Cadastre-se grátis</a>
      </div>

    </div>
  </main>

  <!-- Rodapé -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/toast.js"></script>
  <script src="assets/js/modal.js"></script>
  <script>
    document.getElementById('form-login').addEventListener('submit', async (e) => {
      e.preventDefault();
      const btn = document.getElementById('btn-submit-login');
      btn.disabled = true;
      btn.classList.add('opacity-50');

      const formData = new FormData(e.target);

      try {
        const res = await fetch('backend/auth_login.php', {
          method: 'POST',
          body: formData
        });
        const data = await res.json();

        if (res.ok && data.success) {
          BananalToast.success(data.message || 'Login efetuado com sucesso!');
          setTimeout(() => {
            window.location.href = data.redirect || 'index.php';
          }, 800);
        } else {
          BananalToast.error(data.error || 'Falha ao efetuar login.');
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
