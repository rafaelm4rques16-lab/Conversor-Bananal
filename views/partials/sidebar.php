<?php
/**
 * Menu Lateral (Sidebar) Estilo Convertio - Conversor Bananal
 */
if (!defined('BANANAL_ACCESS')) exit;
?>
<!-- Overlay para fechar no Mobile -->
<div id="sidebar-overlay" class="fixed inset-0 bg-stone-900/50 backdrop-blur-xs z-40 hidden lg:hidden transition-opacity"></div>

<!-- Barra Lateral -->
<aside id="bananal-sidebar" class="fixed top-0 bottom-0 left-0 w-72 sm:w-80 bg-yellow-50 border-r-2 border-yellow-300 z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out flex flex-col shadow-xl lg:shadow-none">
  <!-- Topo da Sidebar -->
  <div class="p-5 border-b border-yellow-200 flex items-center justify-between bg-yellow-100/60">
    <div class="flex items-center gap-2.5">
      <img src="assets/img/banana-mascot.svg" alt="Mascote Bananal" class="w-9 h-9 hover-wiggle">
      <div>
        <h2 class="text-sm font-black text-stone-900 tracking-tight leading-none">MENU BANANAL</h2>
        <span class="text-[10px] font-bold text-yellow-800 uppercase tracking-wider">Ferramentas & Histórico</span>
      </div>
    </div>
    <!-- Botão fechar (Mobile) -->
    <button id="btn-close-sidebar" type="button" class="lg:hidden p-1.5 text-stone-600 hover:text-stone-900 rounded-lg hover:bg-yellow-200" aria-label="Fechar menu">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
    </button>
  </div>

  <!-- Links de Navegação / Categorias de Conversão -->
  <div class="p-4 border-b border-yellow-200 space-y-1">
    <p class="text-[11px] font-black uppercase text-yellow-900 tracking-wider px-2 mb-2">Conversores Rápidos</p>
    
    <a href="index.php" class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-bold text-xs sm:text-sm text-stone-900 bg-yellow-300/80 hover:bg-yellow-300 transition shadow-xs">
      <span class="text-base">🚀</span>
      <span>Todos os Formatos</span>
    </a>

    <a href="#ferramentas" onclick="document.getElementById('bananal-file-input').click();" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs text-stone-700 hover:text-stone-900 hover:bg-yellow-200/60 transition">
      <span class="text-base">🖼️</span>
      <span>Conversor de Imagens</span>
      <span class="ml-auto text-[10px] bg-yellow-200 text-yellow-900 font-bold px-1.5 py-0.5 rounded">PNG, JPG, WEBP</span>
    </a>

    <a href="#ferramentas" onclick="document.getElementById('bananal-file-input').click();" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs text-stone-700 hover:text-stone-900 hover:bg-yellow-200/60 transition">
      <span class="text-base">📊</span>
      <span>Planilhas & Dados</span>
      <span class="ml-auto text-[10px] bg-yellow-200 text-yellow-900 font-bold px-1.5 py-0.5 rounded">CSV, JSON</span>
    </a>

    <a href="#ferramentas" onclick="document.getElementById('bananal-file-input').click();" class="flex items-center gap-3 px-3 py-2 rounded-xl font-semibold text-xs text-stone-700 hover:text-stone-900 hover:bg-yellow-200/60 transition">
      <span class="text-base">📄</span>
      <span>Documentos & Markdown</span>
      <span class="ml-auto text-[10px] bg-yellow-200 text-yellow-900 font-bold px-1.5 py-0.5 rounded">TXT, MD, PDF</span>
    </a>
  </div>

  <!-- Seção de Histórico da Sessão -->
  <div class="flex-1 overflow-y-auto p-4 flex flex-col">
    <div class="flex items-center justify-between mb-3 px-1">
      <div class="flex items-center gap-1.5">
        <span class="text-sm">🕒</span>
        <h3 class="text-xs font-black uppercase text-stone-800 tracking-wider">Histórico Recente</h3>
      </div>
      <button id="btn-clear-history" type="button" class="text-[11px] font-bold text-red-600 hover:text-red-700 hover:underline" title="Apagar histórico desta sessão">
        Limpar
      </button>
    </div>

    <!-- Container dos Itens do Histórico -->
    <div id="history-items-container" class="space-y-2 flex-1">
      <div class="text-center py-6 px-2">
        <span class="text-2xl block mb-1 animate-pulse">🍌</span>
        <p class="text-xs text-stone-500 font-medium">Carregando conversões...</p>
      </div>
    </div>
  </div>

  <!-- Rodapé do Menu Lateral -->
  <div class="p-4 border-t border-yellow-200 bg-yellow-100/40 text-center">
    <div class="flex items-center justify-center gap-1.5 text-xs text-stone-600 font-medium">
      <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
      <span>Arquivos temporários são excluídos a cada 2h</span>
    </div>
    <div class="text-[10px] text-stone-400 mt-1 font-mono">Sessão ID: <?= substr(getSessionUserId(), 0, 10); ?>...</div>
  </div>
</aside>
