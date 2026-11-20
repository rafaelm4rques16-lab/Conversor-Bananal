<?php
/**
 * View - Gerador de Legendas Profissionais (SRT / VTT)
 * Conversor Bananal Pro
 */
?>
<section class="max-w-4xl mx-auto space-y-6">
  <div class="text-center">
    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-200 text-yellow-950 text-xs font-bold mb-3">
      <span>💬</span> Gerador de Legendas SRT & VTT
    </div>
    <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Gerador de Legendas Profissionais</h1>
    <p class="text-xs sm:text-sm text-stone-600 mt-2 max-w-lg mx-auto">
      Cole qualquer texto, roteiro ou transcrição. O Bananal divide as frases em blocos cronometrados e gera arquivos de legenda prontos para seus vídeos.
    </p>
  </div>

  <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-md p-6 sm:p-8 space-y-6">
    <div>
      <label class="block text-xs font-bold text-stone-700 uppercase tracking-wider mb-2">Cole o Texto do seu Vídeo ou Roteiro:</label>
      <textarea id="sub-input-text" rows="8" placeholder="Cole aqui seu texto ou parágrafos para transformar em legendas com minutagem automática..."
                class="w-full p-4 rounded-2xl bg-stone-50 border-2 border-yellow-200 focus:border-yellow-500 focus:outline-none transition text-sm font-medium leading-relaxed text-stone-800"></textarea>
    </div>

    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2 text-xs font-bold text-stone-600">
        <span>Duração estimada por frase:</span>
        <select id="sub-duration-per-line" class="bg-yellow-50 border border-yellow-300 rounded-xl px-3 py-1.5 text-stone-800 font-bold">
          <option value="3">3 segundos</option>
          <option value="4" selected>4 segundos (Padrão)</option>
          <option value="5">5 segundos</option>
        </select>
      </div>

      <button type="button" onclick="generateSubtitles()" class="px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs sm:text-sm shadow-md transition transform active:scale-95 flex items-center gap-2">
        <span>Gerar Legendas</span>
        <span>🍌</span>
      </button>
    </div>

    <!-- Pré-visualização do Arquivo SRT / VTT -->
    <div id="sub-result-area" class="space-y-4 pt-4 border-t border-yellow-100 hidden">
      <div class="flex items-center justify-between">
        <h4 class="text-xs font-black uppercase text-stone-800 tracking-wider">Código de Legenda Gerado:</h4>
        <div class="flex items-center gap-2">
          <button type="button" onclick="downloadSubtitle('srt')" class="px-4 py-2 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-bold rounded-xl text-xs shadow-xs transition">
            Baixar .SRT ⬇
          </button>
          <button type="button" onclick="downloadSubtitle('vtt')" class="px-4 py-2 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs shadow-xs transition">
            Baixar .VTT ⬇
          </button>
        </div>
      </div>

      <textarea id="sub-output-code" readonly rows="8" class="w-full p-4 rounded-2xl bg-stone-900 text-yellow-300 font-mono text-xs leading-relaxed border border-stone-800 focus:outline-none resize-none"></textarea>
    </div>
  </div>
</section>

<script>
  let currentSrt = '';
  let currentVtt = '';

  function generateSubtitles() {
    const raw = document.getElementById('sub-input-text').value.trim();
    if (!raw) {
      BananalToast.warning('Digite ou cole algum texto primeiro!');
      return;
    }

    const dur = parseFloat(document.getElementById('sub-duration-per-line').value) || 4.0;
    // Divide por frases ou quebras de linha
    const sentences = raw.split(/(?<=[.?!])\s+|\n+/).filter(s => s.trim().length > 0);

    let srt = '';
    let vtt = "WEBVTT - Gerado por Conversor Bananal 🍌\n\n";

    sentences.forEach((text, i) => {
      const startSec = i * dur;
      const endSec = startSec + (dur - 0.2);

      const srtStart = formatTimeSrt(startSec);
      const srtEnd = formatTimeSrt(endSec);
      srt += `${i + 1}\r\n${srtStart} --> ${srtEnd}\r\n${text.trim()}\r\n\r\n`;

      const vttStart = formatTimeVtt(startSec);
      const vttEnd = formatTimeVtt(endSec);
      vtt += `${vttStart} --> ${vttEnd}\n${text.trim()}\n\n`;
    });

    currentSrt = srt.trim();
    currentVtt = vtt.trim();

    document.getElementById('sub-output-code').value = currentSrt;
    document.getElementById('sub-result-area').classList.remove('hidden');
    BananalToast.success('Legendas geradas com sucesso!');
  }

  function formatTimeSrt(sec) {
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = Math.floor(sec % 60);
    const ms = Math.round((sec - Math.floor(sec)) * 1000);
    return `${pad(h)}:${pad(m)}:${pad(s)},${pad3(ms)}`;
  }

  function formatTimeVtt(sec) {
    const h = Math.floor(sec / 3600);
    const m = Math.floor((sec % 3600) / 60);
    const s = Math.floor(sec % 60);
    const ms = Math.round((sec - Math.floor(sec)) * 1000);
    return `${pad(h)}:${pad(m)}:${pad(s)}.${pad3(ms)}`;
  }

  function pad(n) { return String(n).padStart(2, '0'); }
  function pad3(n) { return String(n).padStart(3, '0'); }

  function downloadSubtitle(format) {
    const content = format === 'vtt' ? currentVtt : currentSrt;
    const blob = new Blob([content], { type: 'text/plain;charset=utf-8' });
    const a = document.createElement('a');
    a.href = URL.createObjectURL(blob);
    a.download = 'legendas_bananal.' + format;
    a.click();
    BananalToast.success(`Arquivo .${format.toUpperCase()} baixado!`);
  }
</script>
