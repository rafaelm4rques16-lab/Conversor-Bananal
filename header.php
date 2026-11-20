<?php
/**
 * Header Global - Conversor Bananal Pro
 */
$currentPage = $_GET['page'] ?? 'home';
?>
<header class="bg-yellow-400 border-b-2 border-yellow-500 sticky top-0 z-30 shadow-sm">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 h-16 flex items-center justify-between gap-4">
    
    <!-- Esquerda: Toggle Sidebar + Logo -->
    <div class="flex items-center gap-3">
      <button id="btn-toggle-sidebar" type="button" class="lg:hidden p-2 rounded-xl bg-yellow-300 text-stone-900 hover:bg-yellow-500 transition">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 6h16M4 12h16M4 18h16"></path></svg>
      </button>

      <a href="index.php" class="flex items-center gap-2 group">
        <div class="w-10 h-10 rounded-2xl bg-stone-900 text-yellow-300 flex items-center justify-center text-2xl shadow-sm transform group-hover:rotate-6 transition select-none">
          🍌
        </div>
        <div>
          <span class="text-lg font-black text-stone-900 tracking-tight leading-none block">BANANAL<span class="text-stone-800 ml-1">PRO</span></span>
          <span class="text-[9px] font-bold text-stone-800 uppercase tracking-widest block opacity-75">Conversor & Transcritor</span>
        </div>
      </a>
    </div>

    <!-- Centro: Menu de Ferramentas -->
    <nav class="hidden lg:flex items-center gap-1 text-xs font-bold text-stone-900">
      <a href="index.php?page=home" class="px-3 py-2 rounded-xl <?= $currentPage === 'home' ? 'bg-yellow-500/60 shadow-2xs' : 'hover:bg-yellow-300' ?> transition">
        🚀 Todos Formatos
      </a>
      <a href="index.php?page=office" class="px-3 py-2 rounded-xl <?= $currentPage === 'office' ? 'bg-yellow-500/60 shadow-2xs' : 'hover:bg-yellow-300' ?> transition">
        📝 Word / PPT / Excel
      </a>
      <a href="index.php?page=audio_texto" class="px-3 py-2 rounded-xl <?= $currentPage === 'audio_texto' ? 'bg-yellow-500/60 shadow-2xs' : 'hover:bg-yellow-300' ?> transition">
        🎧 Áudio/Vídeo ➔ Texto
      </a>
      <a href="index.php?page=youtube" class="px-3 py-2 rounded-xl <?= $currentPage === 'youtube' ? 'bg-yellow-500/60 shadow-2xs' : 'hover:bg-yellow-300' ?> transition">
        ▶️ Transcrever YouTube
      </a>
      <a href="index.php?page=vivo" class="px-3 py-2 rounded-xl <?= $currentPage === 'vivo' ? 'bg-yellow-500/60 shadow-2xs' : 'hover:bg-yellow-300' ?> transition">
        🎙️ Transcrição ao Vivo
      </a>
    </nav>

    <!-- Direita: Ferramenta Extra -->
    <div class="flex items-center gap-2">
      <a href="standalone.html" class="hidden sm:inline-flex px-2.5 py-1.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-yellow-300 text-xs font-bold shadow-xs transition" title="Modo 100% Offline sem servidor">
        ⚡ Standalone
      </a>
    </div>

  </div>
</header>