<?php
$page_title = 'Entrar na Conta - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

// Se já estiver logado, redireciona para a página principal
if (usuario_esta_logado()) {
    header('Location: index.php');
    exit;
}
?>

<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full flex flex-col justify-center">
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-xl relative overflow-hidden">
    
    <!-- Decoração temática -->
    <div class="absolute -right-6 -top-6 text-7xl opacity-20 pointer-events-none select-none">
      🍌
    </div>

    <div class="text-center mb-8">
      <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-banana-400 to-banana-300 flex items-center justify-center text-3xl shadow-md banana-bounce">
        🍌
      </div>
      <h2 class="text-2xl font-bold font-display text-banana-950">Acessar sua Conta</h2>
      <p class="text-xs text-slate-500 mt-1">Conecte-se para salvar seu histórico e gerenciar conversões</p>
    </div>

    <!-- Dica de Credencial Padrão -->
    <div class="mb-6 p-3.5 bg-banana-50 border border-banana-300 rounded-2xl text-xs text-banana-900 flex items-start gap-2.5">
      <span class="text-base select-none">💡</span>
      <div>
        <strong class="font-bold">Acesso Rápido de Testes:</strong><br>
        Email: <code class="bg-banana-200/80 px-1.5 py-0.5 rounded font-mono text-banana-950">admin@bananal.com</code><br>
        Senha: <code class="bg-banana-200/80 px-1.5 py-0.5 rounded font-mono text-banana-950">bananal123</code>
      </div>
    </div>

    <form action="backend/auth_login.php" method="POST" class="space-y-4">
      <div>
        <label for="email" class="block text-xs font-bold text-banana-950 mb-1.5">E-mail Cadastrado</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="mail" class="w-4 h-4"></i>
          </span>
          <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            placeholder="seu.email@exemplo.com"
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition bg-white"
          >
        </div>
      </div>

      <div>
        <label for="senha" class="block text-xs font-bold text-banana-950 mb-1.5">Senha de Acesso</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="lock" class="w-4 h-4"></i>
          </span>
          <input 
            type="password" 
            id="senha" 
            name="senha" 
            required 
            placeholder="Sua senha secreta"
            class="w-full pl-10 pr-10 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition bg-white"
          >
          <button 
            type="button" 
            onclick="togglePasswordVisibility('senha', this)" 
            class="absolute right-3.5 top-3 text-slate-400 hover:text-slate-600 focus:outline-none"
          >
            <i data-lucide="eye" class="w-4 h-4"></i>
          </button>
        </div>
      </div>

      <div class="pt-2">
        <button 
          type="submit" 
          class="w-full py-3 rounded-2xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold font-display shadow-lg shadow-banana-400/25 transition flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-[0.99]"
        >
          <span>Entrar no Bananal</span>
          <i data-lucide="arrow-right" class="w-4 h-4"></i>
        </button>
      </div>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
      Não possui uma conta? 
      <a href="registro.php" class="font-bold text-banana-700 hover:text-banana-900 underline ml-1">
        Criar conta grátis
      </a>
    </div>

  </div>
</main>

<script>
  function togglePasswordVisibility(inputId, btn) {
    const input = document.getElementById(inputId);
    if (input.type === 'password') {
      input.type = 'text';
      btn.innerHTML = '<i data-lucide="eye-off" class="w-4 h-4"></i>';
    } else {
      input.type = 'password';
      btn.innerHTML = '<i data-lucide="eye" class="w-4 h-4"></i>';
    }
    lucide.createIcons();
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
