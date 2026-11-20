<?php
/**
 * View - Conversor de Áudio e Vídeo para Texto (Foto 1 & 2)
 * Conversor Bananal Pro
 */
?>
<section class="max-w-4xl mx-auto">
  <div class="text-center mb-10">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-200 text-yellow-950 text-xs font-bold mb-3">
      <span>🎧</span> Áudio & Vídeo ➔ Texto
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Converter Arquivos de Áudio em Texto</h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2">
      Envie MP3, MP4, WAV ou MOV. Baixe o resultado em <strong>DOCX (Word)</strong>, <strong>PDF</strong>, <strong>SRT (Legendas)</strong> ou <strong>TXT</strong>.
    </p>
  </div>

  <!-- Cards dos Recursos (Conforme Imagem 1) -->
  <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-white p-5 rounded-2xl border-2 border-yellow-200 shadow-xs flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl flex-shrink-0">
        🎵
      </div>
      <div>
        <h4 class="font-black text-stone-900 text-xs uppercase">Áudio em Texto</h4>
        <p class="text-[11px] text-stone-500">MP3, WAV ➔ DOCX, PDF</p>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border-2 border-yellow-200 shadow-xs flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl flex-shrink-0">
        🎬
      </div>
      <div>
        <h4 class="font-black text-stone-900 text-xs uppercase">Vídeo para Texto</h4>
        <p class="text-[11px] text-stone-500">MP4, MOV ➔ DOCX, SRT</p>
      </div>
    </div>

    <div class="bg-white p-5 rounded-2xl border-2 border-yellow-200 shadow-xs flex items-center gap-3">
      <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center text-2xl flex-shrink-0">
        💬
      </div>
      <div>
        <h4 class="font-black text-stone-900 text-xs uppercase">Legendas com Minutagem</h4>
        <p class="text-[11px] text-stone-500">Geração de .SRT e .VTT</p>
      </div>
    </div>
  </div>

  <!-- Área de Envio -->
  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-10 text-center">
    <div id="dropzone" class="border-3 border-dashed border-yellow-400 hover:border-yellow-500 rounded-2xl p-8 sm:p-12 cursor-pointer transition bg-yellow-50/40 hover:bg-yellow-100/40">
      <input type="file" id="file-input" accept="audio/*,video/*,.mp3,.wav,.mp4,.mov,.ogg,.m4a" class="hidden">
      
      <div class="w-16 h-16 mx-auto rounded-2xl bg-yellow-300 flex items-center justify-center text-3xl mb-3 shadow-inner hover-bounce">
        🎙️
      </div>
      <h3 class="text-lg font-black text-stone-900">Arraste seu arquivo de áudio ou vídeo</h3>
      <p class="text-xs text-stone-500 mt-1 mb-4">Suporta MP3, WAV, MP4, MOV, OGG, WEBM</p>
      
      <button type="button" onclick="document.getElementById('file-input').click()" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs sm:text-sm shadow-md transition transform active:scale-95">
        Selecionar Arquivo Multimídia
      </button>
    </div>

    <!-- Fila de Arquivos -->
    <div id="queue-section" class="mt-8 text-left space-y-4 hidden">
      <div id="queue-list" class="space-y-3"></div>
      <div class="flex justify-end gap-3 pt-4 border-t border-yellow-100">
        <button id="btn-convert-all" class="px-6 py-3 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-black rounded-xl text-xs sm:text-sm shadow-md transition">
          🍌 Iniciar Transcrição
        </button>
      </div>
    </div>
  </div>
</section>
