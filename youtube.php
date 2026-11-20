<?php
/**
 * View - Transcritor e Sumarizador de YouTube (Foto 1 & 2)
 * Conversor Bananal Pro
 */
?>
<section class="max-w-4xl mx-auto space-y-8">
  <div class="text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-100 text-red-950 text-xs font-bold mb-3 border border-red-300">
      <span>▶️</span> Transcritor do YouTube & Sumário IA
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Transcrever Vídeos do YouTube</h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2 max-w-lg mx-auto">
      Basta colar o link do vídeo. O Bananal extrai a transcrição completa com minutagens, gera o sumário executivo e permite baixar em <strong>DOCX (Word)</strong>, <strong>SRT (Legendas)</strong> ou <strong>TXT</strong>.
    </p>
  </div>

  <!-- Formulário do YouTube -->
  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-8">
    <form id="form-youtube" class="flex flex-col sm:flex-row gap-3">
      <div class="relative flex-1">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-red-500 text-xl font-bold">
          ▶
        </div>
        <input type="url" id="youtube-url-input" required placeholder="https://www.youtube.com/watch?v=..."
               class="w-full pl-11 pr-4 py-3.5 rounded-2xl border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition text-sm font-medium bg-stone-50">
      </div>
      <button type="submit" id="btn-youtube-submit" class="px-7 py-3.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-2xl shadow-md transition transform active:scale-95 text-sm flex items-center justify-center gap-2 whitespace-nowrap">
        <span>Transcrever Vídeo</span>
        <span>🍌</span>
      </button>
    </form>
    <div class="text-[11px] text-stone-400 mt-2 text-center sm:text-left">
      Exemplo: Cole links como <code>https://www.youtube.com/watch?v=dQw4w9WgXcQ</code> ou <code>https://youtu.be/...</code>
    </div>
  </div>

  <!-- Card de Resultados Dinâmicos -->
  <div id="youtube-results-card" class="bg-white rounded-3xl border-2 border-yellow-300 shadow-lg overflow-hidden hidden">
    <!-- Cabeçalho do Vídeo -->
    <div class="bg-yellow-100/60 p-6 border-b border-yellow-200 flex flex-col sm:flex-row items-center gap-5">
      <img id="yt-thumb" src="" alt="Thumbnail" class="w-full sm:w-44 h-28 object-cover rounded-2xl border-2 border-yellow-300 shadow-sm flex-shrink-0">
      <div class="flex-1 min-w-0 text-center sm:text-left">
        <span class="text-[10px] font-black uppercase tracking-wider text-red-600 bg-red-100 px-2 py-0.5 rounded-md inline-block mb-1">Vídeo Processado</span>
        <h3 id="yt-title" class="text-base sm:text-lg font-black text-stone-900 leading-snug truncate"></h3>
        <p id="yt-author" class="text-xs text-stone-500 mt-0.5 font-bold"></p>
        
        <!-- Ações de Download -->
        <div class="flex flex-wrap items-center gap-2 mt-4 justify-center sm:justify-start">
          <a id="btn-download-yt-docx" href="#" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs shadow-xs transition flex items-center gap-1.5" download>
            <span>Baixar DOCX (Word)</span> ⬇
          </a>
          <a id="btn-download-yt-srt" href="#" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5" download>
            <span>Baixar Legendas (.SRT)</span> ⬇
          </a>
          <a id="btn-download-yt-txt" href="#" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-xl text-xs transition" download>
            <span>TXT</span> ⬇
          </a>
        </div>
      </div>
    </div>

    <!-- Navegação de Abas -->
    <div class="border-b border-yellow-200 px-6 pt-3 flex gap-2 text-xs font-bold">
      <button id="btn-tab-transcript" onclick="switchYtTab('transcript')" class="px-4 py-2 text-xs font-black text-stone-950 border-b-2 border-yellow-500 bg-yellow-100/50 rounded-t-lg transition">
        📝 Transcrição Completa
      </button>
      <button id="btn-tab-summary" onclick="switchYtTab('summary')" class="px-4 py-2 text-xs font-bold text-stone-600 hover:text-stone-950 transition">
        💡 Sumário & Insights (IA)
      </button>
      <button id="btn-tab-srt" onclick="switchYtTab('srt')" class="px-4 py-2 text-xs font-bold text-stone-600 hover:text-stone-950 transition">
        💬 Código de Legendas (SRT)
      </button>
    </div>

    <!-- Conteúdo das Abas -->
    <div class="p-6">
      <div id="yt-tab-transcript" class="space-y-3">
        <textarea id="yt-transcript-text" readonly rows="12" class="w-full p-4 rounded-2xl bg-stone-50 border border-yellow-200 text-xs font-mono leading-relaxed text-stone-800 focus:outline-none resize-none"></textarea>
      </div>

      <div id="yt-tab-summary" class="hidden">
        <div id="yt-summary-content"></div>
      </div>

      <div id="yt-tab-srt" class="hidden space-y-3">
        <textarea id="yt-srt-text" readonly rows="12" class="w-full p-4 rounded-2xl bg-stone-900 border border-stone-800 text-xs font-mono leading-relaxed text-yellow-300 focus:outline-none resize-none"></textarea>
      </div>
    </div>
  </div>
</section>

<script src="public/assets/js/youtube_tool.js"></script>
