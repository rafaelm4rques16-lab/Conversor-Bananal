<?php
/**
 * Menu Lateral Estilo Convertio - Conversor Bananal Pro
 */
$currentPage = $_GET['page'] ?? 'home';
?>
<!-- Overlay Mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs z-40 hidden lg:hidden"></div>

<aside id="bananal-sidebar" class="fixed top-0 bottom-0 left-0 w-72 sm:w-80 bg-yellow-50 border-r-2 border-yellow-300 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl lg:shadow-none">
  
  <!-- Topo da Sidebar -->
  <div class="p-5 border-b border-yellow-200 flex items-center justify-between bg-yellow-100/60">
    <div class="flex items-center gap-2">
      <span class="text-3xl hover-bounce select-none">🍌</span>
      <div>
        <h2 class="text-sm font-black text-stone-900 leading-none">MENU BANANAL</h2>
        <span class="text-[10px] font-bold text-yellow-800 uppercase tracking-wider">Estúdio de Ferramentas</span>
      </div>
    </div>
    <button id="btn-close-sidebar" class="lg:hidden p-1.5 text-stone-600 hover:text-stone-900 rounded-lg hover:bg-yellow-200">✕</button>
  </div>

  <!-- Navegação de Ferramentas -->
  <div class="p-4 border-b border-yellow-200 space-y-1">
    <p class="text-[11px] font-black uppercase text-yellow-900 tracking-wider px-2 mb-2">Suíte de Ferramentas</p>
    
    <a href="index.php?page=home" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'home' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">🚀</span>
      <span>Conversor Universal</span>
    </a>

    <a href="index.php?page=office" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'office' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">📝</span>
      <span>Word, PPT & Excel ➔ PDF</span>
    </a>

    <a href="index.php?page=audio_texto" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'audio_texto' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">🎧</span>
      <span>Áudio/Vídeo ➔ Texto</span>
    </a>

    <a href="index.php?page=youtube" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'youtube' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">▶️</span>
      <span>Transcrever YouTube</span>
    </a>

    <a href="index.php?page=vivo" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'vivo' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">🎙️</span>
      <span>Transcrição ao Vivo</span>
    </a>

    <a href="index.php?page=legendas" class="flex items-center gap-3 px-3 py-2 rounded-xl text-xs font-bold transition <?= $currentPage === 'legendas' ? 'bg-yellow-300 text-stone-950 shadow-2xs' : 'text-stone-700 hover:bg-yellow-200/60' ?>">
      <span class="text-base">💬</span>
      <span>Gerador de Legendas (SRT)</span>
    </a>
  </div>

  <!-- Histórico em Tempo Real -->
  <div class="flex-1 overflow-y-auto p-4 flex flex-col">
    <div class="flex items-center justify-between mb-3 px-1">
      <div class="flex items-center gap-1.5 text-xs font-black uppercase text-stone-800 tracking-wider">
        <span>🕒</span>
        <span>Histórico Recente</span>
      </div>
      <button onclick="handleClearHistory()" class="text-[11px] font-bold text-red-600 hover:underline">Limpar</button>
    </div>

    <div id="sidebar-history-container" class="space-y-2 flex-1">
      <div class="text-center py-6 text-stone-400 text-xs">Carregando histórico...</div>
    </div>
  </div>

  <!-- Links do Sistema no Rodapé da Sidebar -->
  <div class="p-4 border-t border-yellow-200 bg-yellow-100/40 text-xs text-stone-600 flex items-center justify-between">
    <a href="index.php?page=historico" class="font-bold hover:text-stone-900 hover:underline">Ver Histórico Geral</a>
    <span>•</span>
    <a href="index.php?page=auditoria" class="font-bold hover:text-stone-900 hover:underline">Auditoria</a>
  </div>
</aside>
