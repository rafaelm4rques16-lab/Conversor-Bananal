<?php
/**
 * Componentes Globais de Modal e Toasts - Bananal Pro
 */
?>
<!-- Toast Container Flutuante -->
<div id="toast-container" class="fixed bottom-5 right-5 z-50 flex flex-col gap-3 pointer-events-none"></div>

<!-- Modal Universal Personalizado (Corrigido para não travar) -->
<div id="bananal-custom-modal" class="fixed inset-0 z-50 flex items-center justify-center bg-stone-900/60 backdrop-blur-xs p-4 hidden">
  <div class="bg-white rounded-3xl border-2 border-yellow-300 max-w-md w-full p-6 shadow-2xl transform transition-all">
    <div class="flex items-center gap-3 mb-3">
      <span class="text-3xl">🍌</span>
      <h3 id="modal-title" class="text-lg font-black text-stone-900">Aviso Bananal</h3>
    </div>
    <p id="modal-text" class="text-xs sm:text-sm text-stone-600 mb-6 leading-relaxed">Mensagem do sistema...</p>
    <div class="flex justify-end gap-3">
      <button id="modal-btn-cancel" type="button" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 font-bold text-xs rounded-xl text-stone-700 transition">Cancelar</button>
      <button id="modal-btn-confirm" type="button" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 font-black text-xs rounded-xl text-stone-950 shadow-md transition">Confirmar</button>
    </div>
  </div>
</div>