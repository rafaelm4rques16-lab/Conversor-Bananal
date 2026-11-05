<?php
/**
 * Inclusão do Container de Modais Universais
 * Conversor Bananal
 */
?>
<div id="bananal-modal-root" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-stone-900/60 backdrop-blur-sm hidden transition-opacity duration-200">
  <div id="bananal-modal-box" class="modal-enter bg-white w-full max-w-md rounded-2xl shadow-2xl border-2 border-yellow-400 overflow-hidden text-stone-800">
    <div class="bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-400 p-4 flex items-center gap-3">
      <div class="w-10 h-10 rounded-full bg-white/80 flex items-center justify-center text-2xl shadow-sm select-none">🍌</div>
      <h3 id="bananal-modal-title" class="text-lg font-bold text-stone-900 flex-1 truncate">Aviso do Bananal</h3>
      <button id="bananal-modal-close-x" type="button" class="text-stone-800 hover:text-stone-950 font-black text-xl leading-none">&times;</button>
    </div>
    <div class="p-6">
      <p id="bananal-modal-message" class="text-sm sm:text-base text-stone-600 leading-relaxed break-words"></p>
    </div>
    <div class="bg-stone-50 px-6 py-4 border-t border-yellow-100 flex flex-col-reverse sm:flex-row justify-end gap-2.5">
      <button id="bananal-modal-cancel" type="button" class="px-4 py-2 text-sm font-semibold rounded-xl text-stone-600 hover:bg-stone-200 transition">Cancelar</button>
      <button id="bananal-modal-confirm" type="button" class="px-5 py-2 text-sm font-bold rounded-xl bg-yellow-400 hover:bg-yellow-500 text-stone-900 shadow-md transition transform active:scale-95">Entendido</button>
    </div>
  </div>
</div>
