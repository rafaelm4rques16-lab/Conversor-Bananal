<?php
/**
 * View - Transcrição ao Vivo com Microfone (Foto 2)
 * Conversor Bananal Pro
 */
?>
<section class="max-w-4xl mx-auto space-y-6">
  <div class="text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-200 text-yellow-950 text-xs font-bold mb-3">
      <span>🎙️</span> Reconhecimento de Voz em Tempo Real
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Transcrição ao Vivo no Navegador</h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2 max-w-lg mx-auto">
      Fale diretamente no seu microfone. O Bananal captura sua voz, digita o texto em tempo real e permite exportar em <strong>Word (.docx)</strong>, <strong>PDF</strong>, <strong>Legendas (.srt)</strong> ou <strong>TXT</strong>.
    </p>
  </div>

  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-8 space-y-6">
    
    <!-- Barra de Controle do Microfone -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4 p-4 rounded-2xl bg-yellow-50 border border-yellow-200">
      
      <div class="flex items-center gap-3">
        <button id="btn-speech-start" type="button" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs sm:text-sm shadow-sm transition transform active:scale-95 flex items-center gap-2">
          <span>Iniciar Gravação</span>
          <span>🎙️</span>
        </button>

        <button id="btn-speech-stop" type="button" class="hidden px-5 py-2.5 bg-red-500 hover:bg-red-600 text-white font-black rounded-xl text-xs sm:text-sm shadow-sm transition flex items-center gap-2 animate-pulse">
          <span>Pausar Gravação</span>
          <span>⏹️</span>
        </button>

        <button id="btn-speech-clear" type="button" class="px-4 py-2.5 bg-stone-100 hover:bg-stone-200 text-stone-700 font-bold rounded-xl text-xs transition">
          Limpar
        </button>
      </div>

      <!-- Status e Animação de Onda Sonora -->
      <div class="flex items-center gap-4">
        <div id="speech-wave-animation" class="hidden flex items-end gap-1 h-8 select-none">
          <span class="w-1.5 bg-yellow-500 rounded-full audio-bar"></span>
          <span class="w-1.5 bg-yellow-500 rounded-full audio-bar"></span>
          <span class="w-1.5 bg-yellow-500 rounded-full audio-bar"></span>
          <span class="w-1.5 bg-yellow-500 rounded-full audio-bar"></span>
          <span class="w-1.5 bg-yellow-500 rounded-full audio-bar"></span>
        </div>

        <div id="speech-status-badge" class="flex items-center gap-2 text-xs font-semibold text-stone-500">
          <span class="w-2 h-2 rounded-full bg-stone-400"></span>
          <span>Microfone inativo</span>
        </div>

        <!-- Seletor de Idioma -->
        <select id="speech-lang" class="text-xs font-bold bg-white border border-yellow-300 rounded-xl px-3 py-1.5 text-stone-800">
          <option value="pt-BR">🇧🇷 Português (Brasil)</option>
          <option value="en-US">🇺🇸 Inglês (EUA)</option>
          <option value="es-ES">🇪🇸 Espanhol</option>
        </select>
      </div>
    </div>

    <!-- Caixa de Texto da Transcrição ao Vivo -->
    <div>
      <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Texto Transcrito em Tempo Real:</label>
      <textarea id="speech-transcript-box" rows="10" placeholder="Clique em 'Iniciar Gravação' acima e comece a falar. Seu texto aparecerá aqui com as marcações de tempo..."
                class="w-full p-4 rounded-2xl bg-stone-50 border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition text-sm font-medium leading-relaxed text-stone-800 resize-y"></textarea>
    </div>

    <!-- Botões de Exportação -->
    <div class="pt-4 border-t border-yellow-100 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="text-xs text-stone-500 font-medium">
        🍌 Exporte sua fala instantaneamente no formato desejado:
      </div>

      <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto justify-end">
        <button onclick="exportLiveTranscript('docx')" type="button" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <span>Exportar DOCX (Word)</span> ⬇
        </button>
        <button onclick="exportLiveTranscript('pdf')" type="button" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-bold rounded-xl text-xs shadow-xs transition flex items-center gap-1.5">
          <span>PDF</span> ⬇
        </button>
        <button onclick="exportLiveTranscript('srt')" type="button" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-xl text-xs transition">
          <span>Legendas .SRT</span> ⬇
        </button>
        <button onclick="exportLiveTranscript('txt')" type="button" class="px-3.5 py-2 bg-stone-100 hover:bg-stone-200 text-stone-800 font-bold rounded-xl text-xs transition">
          <span>TXT</span> ⬇
        </button>
      </div>
    </div>

  </div>
</section>

<script src="public/assets/js/live_speech.js"></script>
