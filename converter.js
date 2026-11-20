document.addEventListener('DOMContentLoaded', () => {
  const dropzone     = document.getElementById('dropzone');
  const fileInput    = document.getElementById('file-input');
  const btnSelect    = document.getElementById('btn-select-files');
  const queueSection = document.getElementById('queue-section');
  const queueList    = document.getElementById('queue-list');
  const btnConvert   = document.getElementById('btn-convert-all');
  const btnClear     = document.getElementById('btn-clear-queue');

  const FORMATS_MAP = {
    jpg: ['webp', 'png', 'pdf', 'txt'],
    jpeg: ['webp', 'png', 'pdf', 'txt'],
    png: ['webp', 'jpg', 'pdf', 'txt'],
    webp: ['png', 'jpg', 'pdf', 'txt'],
    gif: ['png', 'jpg', 'webp', 'pdf'],
    docx: ['pdf', 'txt', 'html'],
    pptx: ['pdf', 'txt', 'html'],
    xlsx: ['csv', 'json', 'html', 'pdf'],
    pdf: ['txt', 'docx', 'html'],
    txt: ['pdf', 'docx', 'html'],
    md: ['html', 'pdf', 'docx', 'txt'],
    html: ['txt', 'pdf', 'docx'],
    csv: ['json', 'html', 'txt'],
    json: ['csv', 'txt', 'html'],
    mp3: ['txt', 'wav', 'mp4'],
    wav: ['txt', 'mp3', 'mp4'],
    mp4: ['txt', 'mp3', 'wav'],
    mov: ['txt', 'mp3', 'mp4']
  };

  let queue = [];

  // 1. Clique direto no botão "+ Selecionar Arquivos"
  if (btnSelect && fileInput) {
    btnSelect.addEventListener('click', (e) => {
      e.preventDefault();
      e.stopPropagation();
      fileInput.click();
    });
  }

  // 2. Clique em qualquer outra área da caixa pontilhada
  if (dropzone && fileInput) {
    dropzone.addEventListener('click', (e) => {
      if (e.target === fileInput || e.target.closest('button') || e.target.closest('a')) return;
      fileInput.click();
    });

    ['dragenter', 'dragover'].forEach(evt => {
      dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropzone.classList.add('bg-yellow-50');
      });
    });

    ['dragleave', 'drop'].forEach(evt => {
      dropzone.addEventListener(evt, (e) => {
        e.preventDefault();
        dropzone.classList.remove('bg-yellow-50');
      });
    });

    dropzone.addEventListener('drop', (e) => {
      e.preventDefault();
      if (e.dataTransfer && e.dataTransfer.files.length > 0) {
        handleFiles(Array.from(e.dataTransfer.files));
      }
    });
  }

  // 3. Seleção única pelo input
  if (fileInput) {
    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files.length > 0) {
        const files = Array.from(fileInput.files);
        fileInput.value = '';
        handleFiles(files);
      }
    });
  }

  function handleFiles(files) {
    for (const file of files) {
      const ext = (file.name.split('.').pop() || '').toLowerCase();
      const options = FORMATS_MAP[ext] || ['pdf', 'docx', 'txt', 'html'];

      const item = {
        id: 'q_' + Math.random().toString(36).slice(2, 10),
        rawFile: file,
        name: file.name,
        size: formatSize(file.size),
        ext: ext,
        suggestions: options,
        target: options[0],
        status: 'ready',
        downloadUrl: null,
        errorMessage: null,
        progress: 0
      };

      queue.push(item);
      renderCard(item);
    }
    updateState();
  }

  function formatSize(bytes) {
    if (!bytes) return '0 B';
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(1024));
    return (bytes / Math.pow(1024, i)).toFixed(1) + ' ' + sizes[i];
  }

  function renderCard(item) {
    if (!queueList) return;
    const el = document.createElement('div');
    el.id = item.id;
    el.className = 'bg-white rounded-2xl border-2 border-yellow-200 p-4 sm:p-5 shadow-xs';
    el.innerHTML = `
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div class="flex items-center gap-3.5 min-w-0 flex-1">
          <div class="w-12 h-12 rounded-2xl bg-yellow-100 flex items-center justify-center text-2xl flex-shrink-0">📄</div>
          <div class="truncate">
            <h4 class="font-bold text-stone-900 text-sm truncate">${item.name}</h4>
            <div class="flex items-center gap-2 text-xs text-stone-500 mt-0.5">
              <span class="px-2 py-0.5 rounded bg-yellow-200 text-yellow-900 font-bold uppercase">${item.ext}</span>
              <span>•</span>
              <span>${item.size}</span>
            </div>
          </div>
        </div>
        <div class="action-zone flex items-center gap-3 justify-end">
          <div class="flex items-center gap-1.5 text-xs font-bold text-stone-500">
            <span>para</span>
            <select class="target-select text-xs font-bold bg-yellow-50 border-2 border-yellow-300 rounded-xl px-3 py-2 text-stone-900 cursor-pointer">
              ${item.suggestions.map(s => `<option value="${s}" ${s === item.target ? 'selected' : ''}>➔ ${s.toUpperCase()}</option>`).join('')}
            </select>
          </div>
          <button type="button" class="btn-remove p-2 text-stone-400 hover:text-red-500 rounded-xl">✕</button>
        </div>
      </div>
      <div class="progress-bar-zone mt-3 hidden">
        <div class="w-full bg-stone-100 rounded-full h-2.5 overflow-hidden border border-yellow-200">
          <div class="progress-fill bg-yellow-400 h-full transition-all duration-200" style="width: 0%"></div>
        </div>
        <div class="flex justify-between text-[11px] text-stone-500 font-medium mt-1">
          <span class="status-lbl">Convertendo...</span>
          <span class="percent-lbl">0%</span>
        </div>
      </div>
    `;

    el.querySelector('.target-select').onchange = (e) => { item.target = e.target.value; };
    el.querySelector('.btn-remove').onclick = () => {
      queue = queue.filter(q => q.id !== item.id);
      el.remove();
      updateState();
    };
    queueList.appendChild(el);
  }

  function updateCard(item) {
    const card = document.getElementById(item.id);
    if (!card) return;
    const pZone = card.querySelector('.progress-bar-zone');
    const pFill = card.querySelector('.progress-fill');
    const sLbl  = card.querySelector('.status-lbl');
    const pLbl  = card.querySelector('.percent-lbl');
    const aZone = card.querySelector('.action-zone');

    if (item.status === 'converting') {
      pZone.classList.remove('hidden');
      pFill.style.width = item.progress + '%';
      pLbl.textContent = item.progress + '%';
      sLbl.textContent = 'Convertendo...';
    } else if (item.status === 'done') {
      pZone.classList.remove('hidden');
      pFill.style.width = '100%';
      pFill.className = 'bg-emerald-500 h-full';
      sLbl.innerHTML = '<span class="text-emerald-700 font-bold">✓ Concluído!</span>';
      pLbl.textContent = '100%';
      aZone.innerHTML = `<a href="${item.downloadUrl}" class="px-5 py-2.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl text-xs shadow-md" download>Baixar .${item.target.toUpperCase()} ⬇</a>`;
    } else if (item.status === 'error') {
      pZone.classList.remove('hidden');
      pFill.style.width = '100%';
      pFill.className = 'bg-red-500 h-full';
      sLbl.innerHTML = `<span class="text-red-600 font-bold">${item.errorMessage}</span>`;
      pLbl.textContent = 'Erro';
    }
  }

  function updateState() {
    if (!queueSection) return;
    queueSection.classList.toggle('hidden', queue.length === 0);
    if (btnConvert) btnConvert.disabled = !queue.some(q => q.status === 'ready');
  }

  if (btnConvert) {
    btnConvert.addEventListener('click', async () => {
      const pending = queue.filter(q => q.status === 'ready');
      if (!pending.length) return;
      btnConvert.disabled = true;
      for (const item of pending) {
        await processConversion(item);
      }
      btnConvert.disabled = false;
    });
  }

  function processConversion(item) {
    return new Promise((resolve) => {
      item.status = 'converting';
      item.progress = 25;
      updateCard(item);

      const form = new FormData();
      form.append('action', 'convert');
      form.append('file', item.rawFile);
      form.append('target_format', item.target);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'api.php', true);

      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          item.progress = Math.min(Math.round((e.loaded / e.total) * 80), 85);
          updateCard(item);
        }
      };

      xhr.onload = () => {
        try {
          const res = JSON.parse(xhr.responseText);
          if (xhr.status >= 200 && xhr.status < 300 && res.success) {
            item.status = 'done';
            item.progress = 100;
            item.downloadUrl = res.download_url;
            updateCard(item);
            if (window.BananalToast) BananalToast.success(res.message);
          } else {
            throw new Error(res.error || 'Erro ao converter.');
          }
        } catch (e) {
          item.status = 'error';
          item.errorMessage = e.message || 'Erro no servidor.';
          updateCard(item);
          if (window.BananalToast) BananalToast.error(item.errorMessage);
        }
        resolve();
      };

      xhr.onerror = () => {
        item.status = 'error';
        item.errorMessage = 'Falha de conexão com api.php';
        updateCard(item);
        resolve();
      };

      xhr.send(form);
    });
  }

 // Botão Limpar Fila
  if (btnClear) {
    btnClear.addEventListener('click', () => {
      queue = [];
      if (queueList) queueList.innerHTML = '';
      if (fileInput) fileInput.value = '';
      updateState();
    });
  }

  // Garante atualização inicial do estado da fila
  updateState();
});