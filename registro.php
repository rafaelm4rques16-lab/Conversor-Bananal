<?php
$page_title = 'Criar Conta Gratuita - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';

if (usuario_esta_logado()) {
    header('Location: index.php');
    exit;
}
?>

<main class="max-w-md mx-auto px-4 py-12 flex-grow w-full flex flex-col justify-center">
  <div class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-xl relative overflow-hidden">
    
    <div class="absolute -right-6 -top-6 text-7xl opacity-20 pointer-events-none select-none">
      🌴
    </div>

    <div class="text-center mb-6">
      <div class="w-16 h-16 mx-auto mb-3 rounded-2xl bg-gradient-to-tr from-banana-400 to-banana-300 flex items-center justify-center text-3xl shadow-md banana-bounce">
        🍌
      </div>
      <h2 class="text-2xl font-bold font-display text-banana-950">Criar Nova Conta</h2>
      <p class="text-xs text-slate-500 mt-1">Junte-se ao conversor de arquivos mais rico em potássio!</p>
    </div>

    <form action="backend/auth_register.php" method="POST" class="space-y-4">
      <div>
        <label for="nome" class="block text-xs font-bold text-banana-950 mb-1.5">Nome Completo</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="user" class="w-4 h-4"></i>
          </span>
          <input 
            type="text" 
            id="nome" 
            name="nome" 
            required 
            placeholder="Ex: Rafael Marques"
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition bg-white"
          >
        </div>
      </div>

      <div>
        <label for="email" class="block text-xs font-bold text-banana-950 mb-1.5">E-mail</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="mail" class="w-4 h-4"></i>
          </span>
          <input 
            type="email" 
            id="email" 
            name="email" 
            required 
            placeholder="seu.email@dominio.com"
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition bg-white"
          >
        </div>
      </div>

      <div>
        <label for="senha" class="block text-xs font-bold text-banana-950 mb-1.5">Senha Segura (Mínimo 6 caracteres)</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="lock" class="w-4 h-4"></i>
          </span>
          <input 
            type="password" 
            id="senha" 
            name="senha" 
            required 
            minlength="6"
            placeholder="Crie uma senha forte"
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

      <div>
        <label for="confirmar_senha" class="block text-xs font-bold text-banana-950 mb-1.5">Confirmar Senha</label>
        <div class="relative">
          <span class="absolute left-3.5 top-3 text-slate-400">
            <i data-lucide="shield-check" class="w-4 h-4"></i>
          </span>
          <input 
            type="password" 
            id="confirmar_senha" 
            name="confirmar_senha" 
            required 
            minlength="6"
            placeholder="Digite a senha novamente"
            class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-banana-200 focus:border-banana-500 focus:ring-2 focus:ring-banana-300 text-sm outline-none transition bg-white"
          >
        </div>
      </div>

      <div class="p-3 bg-emerald-50 border border-emerald-200 rounded-xl text-[11px] text-emerald-800 flex items-center gap-2">
        <i data-lucide="lock" class="w-4 h-4 shrink-0 text-emerald-600"></i>
        <span>Sua senha é protegida nativamente com <strong>criptografia Bcrypt</strong> para máxima segurança.</span>
      </div>

      <div class="pt-2">
        <button 
          type="submit" 
          class="w-full py-3 rounded-2xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold font-display shadow-lg shadow-banana-400/25 transition flex items-center justify-center gap-2 hover:scale-[1.01] active:scale-[0.99]"
        >
          <span>Concluir Cadastro</span>
          <i data-lucide="check" class="w-4 h-4"></i>
        </button>
      </div>
    </form>

    <div class="mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-500">
      Já tem uma conta cadastrada? 
      <a href="login.php" class="font-bold text-banana-700 hover:text-banana-900 underline ml-1">
        Fazer login
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
