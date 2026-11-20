<?php
/**
 * View - Conversor de Word, PowerPoint, Excel e PDF
 * Conversor Bananal Pro
 */
?>
<section class="max-w-4xl mx-auto">
  <div class="text-center mb-10">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-200 text-yellow-950 text-xs font-bold mb-3">
      <span>📝</span> Pacote Office & Documentos
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Conversor Word, PPT & Excel para PDF</h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2">
      Converta arquivos DOCX, PPTX e XLSX para PDF, HTML ou TXT com preservação de estrutura e alta fidelidade.
    </p>
  </div>

  <!-- Ações Rápidas -->
  <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-8">
    <div class="p-3 bg-white rounded-2xl border-2 border-yellow-200 text-center shadow-xs">
      <span class="text-xl block mb-1">📝</span>
      <span class="text-xs font-bold text-stone-800">Word ➔ PDF</span>
    </div>
    <div class="p-3 bg-white rounded-2xl border-2 border-yellow-200 text-center shadow-xs">
      <span class="text-xl block mb-1">📊</span>
      <span class="text-xs font-bold text-stone-800">PPT ➔ PDF</span>
    </div>
    <div class="p-3 bg-white rounded-2xl border-2 border-yellow-200 text-center shadow-xs">
      <span class="text-xl block mb-1">📈</span>
      <span class="text-xs font-bold text-stone-800">Excel ➔ PDF/CSV</span>
    </div>
    <div class="p-3 bg-white rounded-2xl border-2 border-yellow-200 text-center shadow-xs">
      <span class="text-xl block mb-1">📕</span>
      <span class="text-xs font-bold text-stone-800">PDF ➔ Word</span>
    </div>
  </div>

  <!-- Área de Upload Dedicada -->
  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-10 text-center">
    <div id="dropzone" class="border-3 border-dashed border-yellow-400 hover:border-yellow-500 rounded-2xl p-8 sm:p-12 cursor-pointer transition bg-yellow-50/40 hover:bg-yellow-100/40">
      <input type="file" id="file-input" accept=".docx,.doc,.pptx,.ppt,.xlsx,.xls,.pdf" class="hidden">
      
      <div class="w-16 h-16 mx-auto rounded-2xl bg-yellow-300 flex items-center justify-center text-3xl mb-3 shadow-inner hover-bounce">
        📄
      </div>
      <h3 class="text-lg font-black text-stone-900">Arraste seu arquivo Word, PPT ou Excel</h3>
      <p class="text-xs text-stone-500 mt-1 mb-4">Suporta formatos .docx, .pptx, .xlsx e .pdf</p>
      
      <button type="button" onclick="document.getElementById('file-input').click()" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs sm:text-sm shadow-md transition transform active:scale-95">
        Escolher Arquivo do Computador
      </button>
    </div>

    <!-- Fila Compartilhada -->
    <div id="queue-section" class="mt-8 text-left space-y-4 hidden">
      <div id="queue-list" class="space-y-3"></div>
      <div class="flex justify-end gap-3 pt-4 border-t border-yellow-100">
        <button id="btn-convert-all" class="px-6 py-3 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-black rounded-xl text-xs sm:text-sm shadow-md transition">
          🍌 Iniciar Conversão para PDF
        </button>
      </div>
    </div>
  </div>
</section>
