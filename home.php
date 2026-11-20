<?php
/**
 * View Principal - Conversor Universal Bananal Pro
 */
?>
<section class="text-center mb-10">
  <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-yellow-200 text-yellow-950 text-xs font-black mb-4 shadow-2xs">
    <span>🍌</span>
    <span>Conversor &amp; Transcritor Bananal Pro</span>
  </div>

  <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-stone-900 tracking-tight leading-tight">
    Transforme arquivos com a agilidade do <span class="text-yellow-600 underline">Bananal</span>
  </h1>
  <p class="text-xs sm:text-base text-stone-600 max-w-2xl mx-auto mt-3">
    Arraste documentos do Word, apresentações PowerPoint, planilhas Excel, imagens, áudios e vídeos. Detecção instantânea e conversão em segundos!
  </p>
</section>

<!-- Dropzone Universal Estilo Convertio -->
<section class="mb-12">
  <div id="dropzone" class="bg-white hover:bg-yellow-50/70 border-2 border-dashed border-yellow-400 hover:border-yellow-500 rounded-3xl p-8 sm:p-14 text-center cursor-pointer transition shadow-sm hover:shadow-md">
    <input type="file" id="file-input" multiple class="hidden">

    <div class="w-20 h-20 mx-auto rounded-3xl bg-yellow-100 flex items-center justify-center text-5xl shadow-inner mb-4 select-none hover-bounce">
      🍌
    </div>

    <h3 class="text-lg sm:text-xl font-black text-stone-900">
      Arraste e solte qualquer arquivo aqui
    </h3>
    <p class="text-xs text-stone-500 mt-1 mb-5">
      ou utilize o botão abaixo para escolher do seu dispositivo
    </p>

    <button type="button" id="btn-select-files" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black px-7 py-3.5 rounded-2xl shadow-md transition transform active:scale-95 text-sm sm:text-base">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path>
      </svg>
      <span>Selecionar Arquivos</span>
    </button>

    <p class="text-[11px] text-stone-400 mt-5">
      Suporta Word (DOCX), PowerPoint (PPTX), Excel (XLSX), PDF, MP3, WAV, MP4, PNG, JPG, WEBP e mais.
    </p>
  </div>
</section>

<!-- Fila de Arquivos Selecionados -->
<section id="queue-section" class="space-y-4 mb-14 hidden">
  <div class="flex items-center justify-between">
    <div class="flex items-center gap-2">
      <span class="text-xl">🍌</span>
      <h3 class="text-lg font-black text-stone-900">Arquivos Prontos para Descascar</h3>
    </div>
    <button type="button" id="btn-clear-queue" class="text-xs font-bold text-stone-500 hover:text-red-600 transition">
      Limpar Fila
    </button>
  </div>

  <div id="queue-list" class="space-y-3"></div>

  <div class="bg-yellow-100/70 border border-yellow-300 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
    <div class="text-xs text-stone-700 font-medium">
      💡 Escolha o formato desejado para cada arquivo e clique em converter.
    </div>
    <button type="button" id="btn-convert-all" class="w-full sm:w-auto px-8 py-3.5 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-black rounded-xl shadow-md transition transform active:scale-95 text-sm flex items-center justify-center gap-2">
      <span>🍌 Converter Todos</span>
    </button>
  </div>
</section>

<!-- Grade de Acesso Rápido às Ferramentas Especiais -->
<section class="my-16">
  <div class="text-center mb-8">
    <h2 class="text-2xl font-black text-stone-900">Mais Ferramentas Especiais 🍌</h2>
    <p class="text-xs text-stone-500 mt-1">Soluções rápidas de transcrição, conversão e legendas</p>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
    <a href="index.php?page=office" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">📝</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Word &amp; PowerPoint ➔ PDF</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Converta documentos DOCX e apresentações PPTX diretamente para PDF com formatação preservada.
      </p>
    </a>

    <a href="index.php?page=audio_texto" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">🎧</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Áudio &amp; Vídeo para Texto</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Envie MP3, MP4, WAV ou MOV e baixe a transcrição em DOCX (Word), TXT, SRT ou PDF.
      </p>
    </a>

    <a href="index.php?page=youtube" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">▶️</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Transcrever Vídeos do YouTube</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Basta colar o link do YouTube para extrair transcrição com minutagens, sumário e legendas SRT.
      </p>
    </a>

    <a href="index.php?page=vivo" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">🎙️</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Transcrição ao Vivo</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Fale no microfone e acompanhe o texto sendo gerado em tempo real com exportação para Word e PDF.
      </p>
    </a>

    <a href="index.php?page=legendas" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">💬</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Gerador de Legendas (SRT / VTT)</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Crie e formate arquivos de legenda profissionais com códigos de tempo exatos para vídeos.
      </p>
    </a>

    <a href="standalone.html" class="bg-white p-6 rounded-3xl border-2 border-yellow-200 hover:border-yellow-400 shadow-xs hover:shadow-md transition group">
      <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition">⚡</div>
      <h3 class="font-black text-stone-900 text-base mb-1">Modo Offline 100% Client-Side</h3>
      <p class="text-xs text-stone-500 leading-relaxed">
        Execute conversões sem depender de conexão com internet ou servidores locais com dois cliques.
      </p>
    </a>
  </div>
</section>