<?php
/**
 * Header Global - Conversor Bananal
 */
if (!defined('BANANAL_ACCESS')) exit;

$usuarioLogado = $_SESSION['usuario'] ?? null;
?>
<header class="bg-yellow-400 border-b-2 border-yellow-500 sticky top-0 z-30 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
    
    <!-- Esquerda: Botão Menu Mobile + Logotipo Bananal -->
    <div class="flex items-center gap-3">
      <button id="btn-toggle-sidebar" type="button" class="lg:hidden p-2 rounded-xl bg-yellow-300 hover:bg-yellow-500 text-stone-900 transition focus:outline-none" aria-label="Abrir menu lateral">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </button>

      <a href="index.php" class="flex items-center gap-2.5 group">
        <div class="w-10 h-10 rounded-2xl bg-stone-900 flex items-center justify-center shadow-md transform group-hover:rotate-6 transition">
          <span class="text-2xl select-none">🍌</span>
        </div>
        <div>
          <span class="text-lg sm:text-xl font-black text-stone-900 tracking-tight leading-none block">CONVERSOR<span class="text-stone-800 ml-1">BANANAL</span></span>
          <span class="text-[10px] font-bold text-stone-800 uppercase tracking-widest block opacity-75">Descascador de Arquivos</span>
        </div>
      </a>
    </div>

    <!-- Centro: Links Principais de Navegação -->
    <nav class="hidden md:flex items-center gap-1.5 text-xs font-bold text-stone-900">
      <a href="index.php" class="px-3 py-2 rounded-xl hover:bg-yellow-300/80 transition flex items-center gap-1.5">
        <span>🚀</span> Início
      </a>
      <a href="historico.php" class="px-3 py-2 rounded-xl hover:bg-yellow-300/80 transition flex items-center gap-1.5">
        <span>🕒</span> Histórico
      </a>
      <a href="auditoria.php" class="px-3 py-2 rounded-xl hover:bg-yellow-300/80 transition flex items-center gap-1.5">
        <span>🛡️</span> Auditoria
      </a>
      <a href="manual.php" class="px-3 py-2 rounded-xl hover:bg-yellow-300/80 transition flex items-center gap-1.5">
        <span>📖</span> Manual & FAQ
      </a>
      <a href="conversor_bananal.html" class="px-2.5 py-1 rounded-lg bg-stone-900 text-yellow-300 hover:bg-stone-800 transition flex items-center gap-1 text-[11px]" title="Versão Offline sem necessidade de servidor">
        <span>⚡</span> Standalone
      </a>
    </nav>

    <!-- Direita: Perfil / Login / Registro -->
    <div class="flex items-center gap-2">
      <?php if ($usuarioLogado): ?>
        <div class="flex items-center gap-2">
          <a href="perfil.php" class="flex items-center gap-2 px-3 py-1.5 rounded-xl bg-yellow-300 hover:bg-yellow-200 transition border border-yellow-500/40 text-xs font-bold text-stone-900 shadow-xs">
            <span class="w-6 h-6 rounded-full bg-stone-900 text-yellow-300 flex items-center justify-center text-xs">
              <?= strtoupper(substr($usuarioLogado['nome'], 0, 1)) ?>
            </span>
            <span class="hidden sm:inline truncate max-w-[120px]"><?= htmlspecialchars($usuarioLogado['nome']) ?></span>
          </a>
          <a href="backend/auth_logout.php" class="p-2 rounded-xl text-stone-800 hover:text-red-700 hover:bg-yellow-500/50 transition" title="Encerrar Sessão">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
          </a>
        </div>
      <?php else: ?>
        <a href="login.php" class="text-xs font-bold px-3.5 py-2 rounded-xl bg-yellow-300 hover:bg-yellow-200 text-stone-900 transition shadow-xs">
          Entrar
        </a>
        <a href="registro.php" class="hidden sm:inline-flex text-xs font-black px-4 py-2 rounded-xl bg-stone-900 hover:bg-stone-800 text-yellow-300 transition shadow-md">
          Cadastrar
        </a>
      <?php endif; ?>
    </div>

  </div>
</header>
