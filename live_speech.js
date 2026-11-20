/**
 * Módulo de Transcrição ao Vivo com Microfone e Reconhecimento de Voz
 * Conversor Bananal Pro
 */
document.addEventListener('DOMContentLoaded', () => {
  const btnStart    = document.getElementById('btn-speech-start');
  const btnStop     = document.getElementById('btn-speech-stop');
  const btnClear    = document.getElementById('btn-speech-clear');
  const transcriptEl= document.getElementById('speech-transcript-box');
  const statusBadge = document.getElementById('speech-status-badge');
  const waveEl      = document.getElementById('speech-wave-animation');
  const langSelect  = document.getElementById('speech-lang');

  let recognition = null;
  let isRecording = false;

  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;

  if (SpeechRecognition) {
    recognition = new SpeechRecognition();
    recognition.continuous = true;
    recognition.interimResults = true;
    recognition.lang = 'pt-BR';

    recognition.onresult = (event) => {
      let interim = '';
      for (let i = event.resultIndex; i < event.results.length; ++i) {
        if (event.results[i].isFinal) {
          const timestamp = new Date().toLocaleTimeString().slice(0, 5);
          transcriptEl.value += `[${timestamp}] ` + event.results[i][0].transcript.trim() + "\n";
        } else {
          interim += event.results[i][0].transcript;
        }
      }
      transcriptEl.scrollTop = transcriptEl.scrollHeight;
    };

    recognition.onerror = (e) => {
      BananalToast.error('Erro no microfone ou permissão negada: ' + e.error);
      stopRecording();
    };

    recognition.onend = () => {
      if (isRecording) {
        // Tenta reiniciar se ainda estiver marcado como ativo
        try { recognition.start(); } catch (err) {}
      }
    };
  }

  function startRecording() {
    if (!recognition) {
      BananalModal.alert('Seu navegador não possui suporte à API de Reconhecimento de Voz. Experimente utilizar o Google Chrome ou Microsoft Edge.', 'Navegador Não Suportado');
      return;
    }

    if (langSelect) recognition.lang = langSelect.value;
    isRecording = true;
    try {
      recognition.start();
    } catch (e) {}

    btnStart.classList.add('hidden');
    btnStop.classList.remove('hidden');
    waveEl.classList.remove('hidden');
    statusBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span> <span class="text-red-700 font-bold">Ouvindo Microfone em Tempo Real...</span>';
    BananalToast.info('Microfone ativado! Fale normalmente para transcrever.');
  }

  function stopRecording() {
    isRecording = false;
    if (recognition) {
      try { recognition.stop(); } catch (e) {}
    }

    btnStart.classList.remove('hidden');
    btnStop.classList.add('hidden');
    waveEl.classList.add('hidden');
    statusBadge.innerHTML = '<span class="w-2 h-2 rounded-full bg-stone-400"></span> <span class="text-stone-500 font-medium">Microfone em pausa</span>';
  }

  if (btnStart) btnStart.addEventListener('click', startRecording);
  if (btnStop) btnStop.addEventListener('click', stopRecording);

  if (btnClear) {
    btnClear.addEventListener('click', async () => {
      if (!transcriptEl.value.trim()) return;
      if (await BananalModal.confirm('Deseja limpar todo o texto da transcrição ao vivo?', 'Limpar')) {
        transcriptEl.value = '';
        BananalToast.info('Área de texto limpa.');
      }
    });
  }

  // Exportação em formatos DOCX, PDF, SRT, TXT
  window.exportLiveTranscript = async function (format) {
    const text = transcriptEl ? transcriptEl.value.trim() : '';
    if (!text) {
      BananalToast.warning('Nenhum texto transcrito para exportar. Fale no microfone primeiro!');
      return;
    }

    try {
      const form = new FormData();
      form.append('action', 'live_transcribe_save');
      form.append('titulo', 'Transcricao_Voz_' + new Date().toISOString().slice(0, 10));
      form.append('conteudo', text);
      form.append('formato', format);

      const res = await fetch('api.php', { method: 'POST', body: form });
      const data = await res.json();

      if (res.ok && data.success) {
        BananalToast.success(`🍌 Arquivo .${format.toUpperCase()} gerado com sucesso!`);
        window.location.href = data.download_url;
      } else {
        throw new Error(data.error || 'Erro ao gerar arquivo.');
      }
    } catch (e) {
      BananalToast.error(e.message);
    }
  };
});
