/**
 * Ferramenta de Transcrição e Sumarização de Vídeos do YouTube
 * Conversor Bananal Pro
 */
document.addEventListener('DOMContentLoaded', () => {
  const formYt     = document.getElementById('form-youtube');
  const inputUrl   = document.getElementById('youtube-url-input');
  const btnSubmit  = document.getElementById('btn-youtube-submit');
  const resultCard = document.getElementById('youtube-results-card');

  if (formYt) {
    formYt.addEventListener('submit', async (e) => {
      e.preventDefault();
      const url = inputUrl.value.trim();
      if (!url) {
        BananalToast.warning('Cole uma URL válida do YouTube.');
        return;
      }

      btnSubmit.disabled = true;
      btnSubmit.innerHTML = '<span>Processando vídeo...</span> <span class="animate-spin">🍌</span>';

      try {
        const form = new FormData();
        form.append('action', 'youtube_transcribe');
        form.append('youtube_url', url);

        const res = await fetch('api.php', { method: 'POST', body: form });
        const data = await res.json();

        if (res.ok && data.success) {
          renderYouTubeResult(data.data, data.download_docx_url);
          BananalToast.success('🍌 Transcrição e Sumário gerados com sucesso!');
        } else {
          throw new Error(data.error || 'Falha ao processar vídeo.');
        }
      } catch (err) {
        BananalToast.error(err.message);
      } finally {
        btnSubmit.disabled = false;
        btnSubmit.innerHTML = '<span>Transcrever Vídeo</span> <span>🍌</span>';
      }
    });
  }

  function renderYouTubeResult(item, docxUrl) {
    if (!resultCard) return;

    resultCard.classList.remove('hidden');
    resultCard.scrollIntoView({ behavior: 'smooth' });

    document.getElementById('yt-thumb').src = item.thumbnail;
    document.getElementById('yt-title').textContent = item.title;
    document.getElementById('yt-author').textContent = item.author;
    document.getElementById('yt-transcript-text').value = item.transcript;
    document.getElementById('yt-srt-text').value = item.srt;

    // Renderiza sumário
    const sum = item.summary;
    const summaryBox = document.getElementById('yt-summary-content');
    if (summaryBox && sum) {
      summaryBox.innerHTML = `
        <div class="space-y-4 text-xs sm:text-sm text-stone-700">
          <p class="font-medium bg-yellow-50 p-4 rounded-xl border border-yellow-200">${sum.summary_text}</p>
          <div>
            <h5 class="font-bold text-stone-900 mb-2 uppercase text-xs tracking-wider">💡 Principais Pontos Abordados:</h5>
            <ul class="list-disc pl-5 space-y-1">
              ${(sum.key_takeaways || []).map(p => `<li>${p}</li>`).join('')}
            </ul>
          </div>
          <div>
            <h5 class="font-bold text-stone-900 mb-2 uppercase text-xs tracking-wider">🎯 Ações e Recomendações:</h5>
            <ul class="list-disc pl-5 space-y-1">
              ${(sum.action_items || []).map(a => `<li>${a}</li>`).join('')}
            </ul>
          </div>
        </div>
      `;
    }

    // Configura botões de download
    const btnDocx = document.getElementById('btn-download-yt-docx');
    if (btnDocx) btnDocx.href = docxUrl;

    const btnSrt = document.getElementById('btn-download-yt-srt');
    if (btnSrt) {
      const blob = new Blob([item.srt], { type: 'text/plain;charset=utf-8' });
      btnSrt.href = URL.createObjectURL(blob);
      btnSrt.download = 'legendas_' + item.video_id + '.srt';
    }

    const btnTxt = document.getElementById('btn-download-yt-txt');
    if (btnTxt) {
      const blob = new Blob([item.transcript], { type: 'text/plain;charset=utf-8' });
      btnTxt.href = URL.createObjectURL(blob);
      btnTxt.download = 'transcricao_' + item.video_id + '.txt';
    }
  }

  window.switchYtTab = function (tabId) {
    ['transcript', 'summary', 'srt'].forEach(t => {
      const content = document.getElementById('yt-tab-' + t);
      const btn = document.getElementById('btn-tab-' + t);
      if (content) content.classList.add('hidden');
      if (btn) btn.className = 'px-4 py-2 text-xs font-bold text-stone-600 hover:text-stone-950 transition';
    });

    const activeContent = document.getElementById('yt-tab-' + tabId);
    const activeBtn = document.getElementById('btn-tab-' + tabId);
    if (activeContent) activeContent.classList.remove('hidden');
    if (activeBtn) activeBtn.className = 'px-4 py-2 text-xs font-black text-stone-950 border-b-2 border-yellow-500 bg-yellow-100/50 rounded-t-lg transition';
  };
});
