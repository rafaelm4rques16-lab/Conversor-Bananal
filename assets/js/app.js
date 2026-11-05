/**
 * Controlador Principal - Conversor Bananal
 * Gerenciamento de Drag & Drop, Detecção de Formato, Sugestões e Conversões
 */

document.addEventListener('DOMContentLoaded', () => {
  // Elementos do DOM
  const dropZone       = document.getElementById('bananal-dropzone');
  const fileInput      = document.getElementById('bananal-file-input');
  const fileListArea   = document.getElementById('bananal-file-list');
  const filesContainer = document.getElementById('files-container');
  const convertBtn     = document.getElementById('btn-start-convert');
  const resetBtn       = document.getElementById('btn-reset-files');
  const historyListEl  = document.getElementById('history-items-container');
  const clearHistBtn   = document.getElementById('btn-clear-history');
  const sidebarEl      = document.getElementById('bananal-sidebar');
  const sidebarOverlay = document.getElementById('sidebar-overlay');
  const toggleSideBtn  = document.getElementById('btn-toggle-sidebar');
  const closeSideBtn   = document.getElementById('btn-close-sidebar');

  // Estado dos arquivos selecionados
  let queuedFiles = [];

  // =========================================================================
  // 1. Gerenciamento do Menu Lateral (Sidebar)
  // =========================================================================
  function openSidebar() {
    if (!sidebarEl) return;
    sidebarEl.classList.remove('-translate-x-full');
    if (sidebarOverlay) sidebarOverlay.classList.remove('hidden');
  }

  function closeSidebar() {
    if (!sidebarEl) return;
    sidebarEl.classList.add('-translate-x-full');
    if (sidebarOverlay) sidebarOverlay.classList.add('hidden');
  }

  if (toggleSideBtn) toggleSideBtn.addEventListener('click', openSidebar);
  if (closeSideBtn) closeSideBtn.addEventListener('click', closeSidebar);
  if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

  // =========================================================================
  // 2. Drag & Drop e Seleção de Arquivo (Estilo Convertio)
  // =========================================================================
  if (dropZone && fileInput) {
    // Clique na dropzone abre seletor nativo
    dropZone.addEventListener('click', (e) => {
      // Evita disparar se clicou diretamente em algum link interno
      if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'A') {
        fileInput.click();
      }
    });

    // Eventos de arrastar e soltar
    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('dropzone-active');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('dropzone-active');
      }, false);
    });

    dropZone.addEventListener('drop', (e) => {
      const dt = e.dataTransfer;
      const files = dt.files;
      if (files && files.length > 0) {
        handleFilesSelected(Array.from(files));
      }
    });

    fileInput.addEventListener('change', () => {
      if (fileInput.files && fileInput.files.length > 0) {
        handleFilesSelected(Array.from(fileInput.files));
      }
    });
  }

  /**
   * Processa a seleção de novos arquivos
   */
  async function handleFilesSelected(files) {
    if (!files || files.length === 0) return;

    for (const file of files) {
      // Cria ID único local para acompanhamento do item
      const fileId = 'file_' + Math.random().toString(36).substring(2, 9);
      const ext = getFileExtension(file.name);

      const fileItem = {
        id: fileId,
        rawFile: file,
        name: file.name,
        size: file.size,
        ext: ext,
        targetFormat: null,
        suggestions: [],
        status: 'detecting', // detecting, ready, converting, done, error
        downloadUrl: null,
        errorMessage: null,
        progress: 0
      };

      queuedFiles.push(fileItem);
      renderFileCard(fileItem);

      // Dispara detecção e busca de sugestões no backend
      detectFormatAndSuggestions(fileItem);
    }

    updateUIState();
  }

  function getFileExtension(filename) {
    const parts = filename.split('.');
    return parts.length > 1 ? parts.pop().toLowerCase() : '';
  }

  function formatBytes(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  // =========================================================================
  // 3. Detecção no Backend e Obtenção de Sugestões de Conversão
  // =========================================================================
  async function detectFormatAndSuggestions(fileItem) {
    const card = document.getElementById(fileItem.id);

    try {
      const response = await fetch(`backend/detect_format.php?filename=${encodeURIComponent(fileItem.name)}`);
      const data = await response.json();

      if (!response.ok || !data.success) {
        throw new Error(data.error || 'Falha ao detectar formato do arquivo.');
      }

      fileItem.suggestions = data.supported_targets || [];
      fileItem.category    = data.category;
      fileItem.readable    = data.readable_name;

      if (fileItem.suggestions.length > 0) {
        // Seleciona a primeira sugestão por padrão
        fileItem.targetFormat = fileItem.suggestions[0];
        fileItem.status = 'ready';
      } else {
        fileItem.status = 'error';
        fileItem.errorMessage = 'Nenhum formato de conversão disponível para este tipo de arquivo.';
        BananalToast.warning(`Arquivo ${fileItem.name}: formato .${fileItem.ext} não possui conversões disponíveis.`);
      }

      updateFileCard(fileItem);

    } catch (err) {
      fileItem.status = 'error';
      fileItem.errorMessage = err.message || 'Erro na detecção.';
      updateFileCard(fileItem);
      BananalToast.error(`Erro ao analisar ${fileItem.name}: ${err.message}`);
    }

    updateUIState();
  }

  // =========================================================================
  // 4. Renderização dos Cards de Arquivo (Estilo Convertio)
  // =========================================================================
  function renderFileCard(item) {
    if (!filesContainer) return;

    const card = document.createElement('div');
    card.id = item.id;
    card.className = 'bg-white rounded-2xl border-2 border-yellow-200 p-4 sm:p-5 shadow-sm transition hover:shadow-md';
    
    card.innerHTML = `
      <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
        <!-- Detalhes do Arquivo -->
        <div class="flex items-center gap-3.5 flex-1 min-w-0">
          <div class="w-12 h-12 rounded-xl bg-yellow-100 border border-yellow-300 flex items-center justify-center flex-shrink-0 text-2xl select-none">
            ${getCategoryIcon(item.ext)}
          </div>
          <div class="min-w-0 flex-1">
            <h4 class="font-bold text-stone-900 truncate text-sm sm:text-base" title="${item.name}">${item.name}</h4>
            <div class="flex items-center gap-2 mt-0.5 text-xs text-stone-500">
              <span class="font-semibold px-2 py-0.5 rounded bg-yellow-200/70 text-yellow-900 uppercase tracking-wider">${item.ext || '???'}</span>
              <span>•</span>
              <span>${formatBytes(item.size)}</span>
            </div>
          </div>
        </div>

        <!-- Seletor de Destino e Ação -->
        <div class="card-action-zone flex flex-wrap items-center gap-3 w-full md:w-auto justify-end">
          <div class="target-selector-wrapper flex items-center gap-2">
            <span class="text-xs font-bold text-stone-400">para</span>
            <div class="relative">
              <select class="target-select appearance-none bg-stone-50 hover:bg-yellow-50 border-2 border-yellow-300 rounded-xl px-4 py-2 pr-9 text-sm font-bold text-stone-800 cursor-pointer focus:outline-none focus:border-yellow-500 transition">
                <option value="">Detectando...</option>
              </select>
              <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-stone-700">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
              </div>
            </div>
          </div>

          <!-- Botão de Remover Item -->
          <button type="button" class="btn-remove-card p-2 text-stone-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition" title="Remover da lista">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
          </button>
        </div>
      </div>

      <!-- Barra de Progresso e Mensagens -->
      <div class="progress-zone mt-4 hidden">
        <div class="w-full bg-stone-100 rounded-full h-3 overflow-hidden border border-yellow-200">
          <div class="progress-bar banana-progress-striped bg-yellow-400 h-full transition-all duration-200" style="width: 0%"></div>
        </div>
        <div class="progress-text flex justify-between items-center text-xs text-stone-500 mt-1.5 font-medium">
          <span class="status-label">Enviando e descascando...</span>
          <span class="percent-label">0%</span>
        </div>
      </div>
    `;

    // Handler para botão remover
    const removeBtn = card.querySelector('.btn-remove-card');
    removeBtn.addEventListener('click', () => {
      queuedFiles = queuedFiles.filter(f => f.id !== item.id);
      card.remove();
      updateUIState();
    });

    filesContainer.appendChild(card);
  }

  function updateFileCard(item) {
    const card = document.getElementById(item.id);
    if (!card) return;

    const select = card.querySelector('.target-select');
    const actionZone = card.querySelector('.card-action-zone');
    const progressZone = card.querySelector('.progress-zone');
    const progressBar = card.querySelector('.progress-bar');
    const statusLabel = card.querySelector('.status-label');
    const percentLabel = card.querySelector('.percent-label');

    // Atualiza opções do select
    if (select) {
      select.innerHTML = '';
      if (item.suggestions && item.suggestions.length > 0) {
        item.suggestions.forEach(sug => {
          const opt = document.createElement('option');
          opt.value = sug;
          opt.textContent = sug.toUpperCase();
          if (sug === item.targetFormat) opt.selected = true;
          select.appendChild(opt);
        });

        select.disabled = (item.status === 'converting' || item.status === 'done');
        select.onchange = (e) => {
          item.targetFormat = e.target.value;
        };
      } else {
        const opt = document.createElement('option');
        opt.value = '';
        opt.textContent = 'Indisponível';
        select.appendChild(opt);
        select.disabled = true;
      }
    }

    // Tratamento de estados
    if (item.status === 'converting') {
      progressZone.classList.remove('hidden');
      if (statusLabel) statusLabel.textContent = 'Convertendo arquivo no Bananal...';
      if (percentLabel) percentLabel.textContent = `${item.progress}%`;
      if (progressBar) progressBar.style.width = `${item.progress}%`;
    } else if (item.status === 'done') {
      progressZone.classList.remove('hidden');
      if (progressBar) {
        progressBar.style.width = '100%';
        progressBar.classList.remove('banana-progress-striped');
        progressBar.classList.add('bg-emerald-500');
      }
      if (statusLabel) statusLabel.innerHTML = '<span class="text-emerald-700 font-bold">✓ Pronto para baixar!</span>';
      if (percentLabel) percentLabel.textContent = '100%';

      // Substitui ação por botão de download
      actionZone.innerHTML = `
        <a href="${item.downloadUrl}" class="inline-flex items-center gap-2 bg-yellow-400 hover:bg-yellow-500 text-stone-900 font-black px-5 py-2.5 rounded-xl shadow-md transition transform active:scale-95" download>
          <span>Baixar .${item.targetFormat.toUpperCase()}</span>
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
        </a>
      `;
    } else if (item.status === 'error') {
      progressZone.classList.remove('hidden');
      if (progressBar) {
        progressBar.classList.remove('banana-progress-striped', 'bg-yellow-400');
        progressBar.classList.add('bg-red-500');
        progressBar.style.width = '100%';
      }
      if (statusLabel) statusLabel.innerHTML = `<span class="text-red-600 font-semibold">${item.errorMessage || 'Falha na conversão'}</span>`;
      if (percentLabel) percentLabel.textContent = 'Erro';
    }
  }

  function getCategoryIcon(ext) {
    if (['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp', 'svg'].includes(ext)) return '🖼️';
    if (['csv', 'json', 'xml'].includes(ext)) return '📊';
    if (['txt', 'md', 'html', 'pdf'].includes(ext)) return '📄';
    if (['mp3', 'wav', 'ogg'].includes(ext)) return '🎵';
    return '📦';
  }

  function updateUIState() {
    if (!fileListArea) return;

    if (queuedFiles.length > 0) {
      fileListArea.classList.remove('hidden');
    } else {
      fileListArea.classList.add('hidden');
    }

    const hasReady = queuedFiles.some(f => f.status === 'ready');
    if (convertBtn) {
      convertBtn.disabled = !hasReady;
      if (hasReady) {
        convertBtn.classList.remove('opacity-50', 'cursor-not-allowed');
      } else {
        convertBtn.classList.add('opacity-50', 'cursor-not-allowed');
      }
    }
  }

  // =========================================================================
  // 5. Execução da Conversão (AJAX com Progresso Real)
  // =========================================================================
  if (convertBtn) {
    convertBtn.addEventListener('click', async () => {
      const pendingFiles = queuedFiles.filter(f => f.status === 'ready');
      if (pendingFiles.length === 0) {
        BananalToast.info('Selecione ou aguarde o processamento dos arquivos antes de converter.');
        return;
      }

      convertBtn.disabled = true;
      convertBtn.classList.add('opacity-50', 'cursor-not-allowed');

      for (const item of pendingFiles) {
        await processSingleConversion(item);
      }

      convertBtn.disabled = false;
      convertBtn.classList.remove('opacity-50', 'cursor-not-allowed');

      // Atualiza histórico na barra lateral
      loadHistory();
    });
  }

  function processSingleConversion(item) {
    return new Promise((resolve) => {
      item.status = 'converting';
      item.progress = 10;
      updateFileCard(item);

      const formData = new FormData();
      formData.append('file', item.rawFile);
      formData.append('target_format', item.targetFormat);

      const xhr = new XMLHttpRequest();
      xhr.open('POST', 'backend/convert_file.php', true);

      // Progresso do upload
      xhr.upload.onprogress = (e) => {
        if (e.lengthComputable) {
          const percent = Math.round((e.loaded / e.total) * 75); // até 75% é upload
          item.progress = Math.max(percent, 15);
          updateFileCard(item);
        }
      };

      xhr.onload = () => {
        item.progress = 95;
        updateFileCard(item);

        try {
          const res = JSON.parse(xhr.responseText);
          if (xhr.status >= 200 && xhr.status < 300 && res.success) {
            item.status = 'done';
            item.progress = 100;
            item.downloadUrl = res.download_url;
            updateFileCard(item);
            BananalToast.success(`🍌 ${item.name} convertido para .${item.targetFormat.toUpperCase()} com sucesso!`);
          } else {
            throw new Error(res.error || 'Falha ao processar conversão.');
          }
        } catch (err) {
          item.status = 'error';
          item.errorMessage = err.message || 'Erro durante a conversão.';
          updateFileCard(item);
          BananalToast.error(`Erro ao converter ${item.name}: ${item.errorMessage}`);
        }
        resolve();
      };

      xhr.onerror = () => {
        item.status = 'error';
        item.errorMessage = 'Erro de rede ou falha de conexão com o servidor.';
        updateFileCard(item);
        BananalToast.error(`Falha de rede ao tentar converter ${item.name}.`);
        resolve();
      };

      xhr.send(formData);
    });
  }

  // =========================================================================
  // 6. Reset da Lista de Arquivos
  // =========================================================================
  if (resetBtn) {
    resetBtn.addEventListener('click', async () => {
      if (queuedFiles.length === 0) return;

      const confirmed = await BananalModal.confirm(
        'Deseja realmente limpar todos os arquivos da lista de conversão atual?',
        'Limpar Lista de Arquivos'
      );

      if (confirmed) {
        queuedFiles = [];
        if (filesContainer) filesContainer.innerHTML = '';
        if (fileInput) fileInput.value = '';
        updateUIState();
        BananalToast.info('Lista de arquivos esvaziada.');
      }
    });
  }

  // =========================================================================
  // 7. Histórico de Conversões do Usuário
  // =========================================================================
  async function loadHistory() {
    if (!historyListEl) return;

    try {
      const response = await fetch('backend/list_history.php');
      const data = await response.json();

      if (!response.ok || !data.success) {
        historyListEl.innerHTML = `<p class="text-xs text-stone-400 p-3 text-center">Nenhuma conversão recente</p>`;
        return;
      }

      if (data.items.length === 0) {
        historyListEl.innerHTML = `
          <div class="text-center py-6 px-4">
            <span class="text-3xl block mb-2 opacity-60">🍌</span>
            <p class="text-xs text-stone-500">Seu histórico de bananas convertidas aparecerá aqui.</p>
          </div>
        `;
        return;
      }

      historyListEl.innerHTML = data.items.map(item => `
        <div class="p-3 bg-white/80 hover:bg-white rounded-xl border border-yellow-200 shadow-xs transition flex items-center justify-between gap-2 text-xs">
          <div class="min-w-0 flex-1">
            <div class="font-bold text-stone-800 truncate" title="${item.nome_original}">${item.nome_original}</div>
            <div class="text-stone-500 flex items-center gap-1.5 mt-0.5">
              <span class="font-semibold text-yellow-900 bg-yellow-200/60 px-1.5 py-0.2 rounded">${item.formato_origem}</span>
              <span>→</span>
              <span class="font-semibold text-emerald-900 bg-emerald-100 px-1.5 py-0.2 rounded">${item.formato_destino}</span>
              <span class="text-[10px] text-stone-400">• ${item.data_criacao}</span>
            </div>
          </div>
          ${item.download_url ? `
            <a href="${item.download_url}" class="p-2 bg-yellow-300 hover:bg-yellow-400 text-stone-900 font-bold rounded-lg transition" title="Baixar novamente" download>
              <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
            </a>
          ` : ''}
        </div>
      `).join('');

    } catch (e) {
      // Ignora erro silenciosamente ou informa discretamente
      historyListEl.innerHTML = `<p class="text-xs text-stone-400 p-3 text-center">Banco de dados ainda não sincronizado.</p>`;
    }
  }

  if (clearHistBtn) {
    clearHistBtn.addEventListener('click', async () => {
      const confirmed = await BananalModal.confirm(
        'Tem certeza que deseja apagar todo o seu histórico de conversões desta sessão?',
        'Limpar Histórico'
      );

      if (confirmed) {
        try {
          const res = await fetch('backend/clear_history.php', { method: 'POST' });
          const json = await res.json();
          if (json.success) {
            BananalToast.success('Histórico limpo com sucesso!');
            loadHistory();
          } else {
            BananalToast.error(json.error || 'Erro ao limpar histórico.');
          }
        } catch (e) {
          BananalToast.error('Falha de conexão com o servidor.');
        }
      }
    });
  }

  // Carrega histórico inicial
  loadHistory();
});
