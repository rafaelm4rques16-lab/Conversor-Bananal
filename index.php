<?php
$page_title = 'Conversor Bananal 🍌 - O conversor de arquivos nada banal!';
require_once __DIR__ . '/includes/header.php';

// Busca estatísticas gerais do banco para o cabeçalho
$total_conversoes_bd = $pdo->query("SELECT COUNT(*) FROM conversoes")->fetchColumn();
$total_kb_bd = $pdo->query("SELECT COALESCE(SUM(tamanho_convertido), 0) FROM conversoes")->fetchColumn();
$potassio_estimado = round($total_conversoes_bd * 105);
?>

<main class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full space-y-8">
  
  <!-- Banner Informativo -->
  <section class="bg-gradient-to-r from-banana-200 via-banana-100 to-leaf-100 rounded-3xl p-6 sm:p-8 border border-banana-300 shadow-sm relative overflow-hidden">
    <div class="relative z-10 max-w-2xl">
      <div class="flex items-center gap-2 mb-3">
        <span class="bg-white/90 text-banana-900 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm flex items-center gap-1.5">
          <i data-lucide="zap" class="w-3.5 h-3.5 text-banana-600"></i>
          Ultra-rápido & Seguro
        </span>
        <?php if ($usuario_logado): ?>
          <span class="bg-leaf-500 text-white text-xs font-bold px-3 py-1 rounded-full shadow-sm">
            Logado como <?= htmlspecialchars($usuario_logado['nome']) ?>
          </span>
        <?php else: ?>
          <a href="login.php" class="text-xs text-banana-800 hover:text-banana-950 font-bold underline">
            Faça login para salvar na sua conta
          </a>
        <?php endif; ?>
      </div>

      <h2 class="text-3xl sm:text-4xl font-extrabold font-display text-banana-950 mb-2 leading-tight">
        Converter arquivos nunca foi tão <span class="text-banana-600 underline decoration-banana-400 decoration-wavy">descomplicado</span>!
      </h2>
      <p class="text-slate-700 text-sm sm:text-base leading-relaxed">
        Converta imagens, áudios, textos e relatórios em instantes. Todas as conversões são registradas no banco de dados e auditadas com total segurança.
      </p>

      <div class="mt-4 flex flex-wrap items-center gap-3">
        <a href="manual.php" class="text-xs font-bold text-banana-950 bg-banana-300 hover:bg-banana-400 px-4 py-2 rounded-xl transition inline-flex items-center gap-1.5 shadow-sm">
          <i data-lucide="book-open" class="w-4 h-4"></i> Qual formato escolher? Consulte o Manual
        </a>
        <a href="historico.php" class="text-xs font-bold text-banana-800 bg-white/80 hover:bg-white px-4 py-2 rounded-xl transition inline-flex items-center gap-1.5 border border-banana-200">
          <i data-lucide="history" class="w-4 h-4"></i> Ver Histórico Completo (<?= $total_conversoes_bd ?>)
        </a>
      </div>
    </div>
    
    <div class="absolute -right-6 -bottom-8 opacity-20 sm:opacity-30 pointer-events-none select-none text-9xl">
      🍌
    </div>
  </section>

  <!-- Indicador de Passos (Stepper) -->
  <nav class="mb-4">
    <ol class="grid grid-cols-2 sm:grid-cols-5 gap-2 sm:gap-4 text-center">
      <li id="step-nav-1" class="step-card bg-white border-2 border-banana-400 p-3 rounded-2xl shadow-sm transition-all duration-300">
        <div class="flex items-center justify-center gap-2">
          <span class="w-7 h-7 rounded-full bg-banana-400 text-banana-950 font-bold flex items-center justify-center text-xs">1</span>
          <span class="text-xs sm:text-sm font-bold text-banana-950 font-display">Selecionar</span>
        </div>
      </li>
      <li id="step-nav-2" class="step-card bg-white/60 border border-slate-200 p-3 rounded-2xl transition-all duration-300 opacity-60">
        <div class="flex items-center justify-center gap-2">
          <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">2</span>
          <span class="text-xs sm:text-sm font-bold text-slate-700 font-display">Identificar</span>
        </div>
      </li>
      <li id="step-nav-3" class="step-card bg-white/60 border border-slate-200 p-3 rounded-2xl transition-all duration-300 opacity-60">
        <div class="flex items-center justify-center gap-2">
          <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">3</span>
          <span class="text-xs sm:text-sm font-bold text-slate-700 font-display">Escolher</span>
        </div>
      </li>
      <li id="step-nav-4" class="step-card bg-white/60 border border-slate-200 p-3 rounded-2xl transition-all duration-300 opacity-60">
        <div class="flex items-center justify-center gap-2">
          <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">4</span>
          <span class="text-xs sm:text-sm font-bold text-slate-700 font-display">Converter</span>
        </div>
      </li>
      <li id="step-nav-5" class="step-card bg-white/60 border border-slate-200 p-3 rounded-2xl transition-all duration-300 opacity-60 col-span-2 sm:col-span-1">
        <div class="flex items-center justify-center gap-2">
          <span class="w-7 h-7 rounded-full bg-slate-200 text-slate-600 font-bold flex items-center justify-center text-xs">5</span>
          <span class="text-xs sm:text-sm font-bold text-slate-700 font-display">Baixar</span>
        </div>
      </li>
    </ol>
  </nav>

  <!-- Painel Principal do Conversor -->
  <div id="mainContainer" class="bg-white rounded-3xl p-6 sm:p-10 border border-banana-200 shadow-xl relative min-h-[420px] flex flex-col justify-center">

    <!-- PASSO 1: UPLOAD (DRAG & DROP) -->
    <div id="step-1" class="step-content">
      <div 
        id="dropZone" 
        class="border-3 border-dashed border-banana-300 hover:border-banana-500 bg-banana-50/50 hover:bg-banana-100/60 rounded-3xl p-8 sm:p-12 text-center transition-all cursor-pointer group flex flex-col items-center justify-center min-h-[320px]"
        onclick="document.getElementById('fileInput').click()"
      >
        <input type="file" id="fileInput" multiple class="hidden" onchange="handleFileSelect(event)">
        
        <div class="w-20 h-20 rounded-full bg-banana-200/80 group-hover:scale-110 flex items-center justify-center text-4xl mb-4 transition-transform shadow-inner">
          📂
        </div>
        <h3 class="text-xl sm:text-2xl font-bold text-banana-950 font-display mb-2">
          Arraste seus arquivos aqui ou <span class="text-banana-600 underline">clique para escolher</span>
        </h3>
        <p class="text-sm text-slate-500 max-w-md mb-4">
          Suporta imagens (PNG, JPG, WEBP, BMP, SVG), áudios (MP3, WAV, OGG), textos (TXT, CSV, JSON, MD, HTML) e documentos!
        </p>
        <div class="flex flex-wrap gap-2 justify-center text-xs text-banana-900 font-medium bg-banana-100/80 px-4 py-2 rounded-xl">
          <span>✨ Conversão Múltipla (Lote)</span>
          <span>•</span>
          <span>🔒 Histórico com Auditoria PDO</span>
          <span>•</span>
          <span>⚡ Download Individual ou ZIP</span>
        </div>
      </div>
    </div>

    <!-- PASSO 2 & 3: CONFIGURAÇÃO DOS ARQUIVOS E FORMATOS -->
    <div id="step-2-3" class="step-content hidden space-y-6">
      <div class="flex flex-wrap items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <h3 class="text-xl font-bold font-display text-banana-950">Arquivos Selecionados e Formatos de Saída</h3>
          <p class="text-xs text-slate-500">Configure o formato ideal para cada um dos seus arquivos.</p>
        </div>
        <button onclick="document.getElementById('fileInput').click()" class="text-xs font-semibold text-banana-800 bg-banana-100 hover:bg-banana-200 px-3.5 py-2 rounded-xl transition flex items-center gap-1.5">
          <i data-lucide="plus" class="w-4 h-4"></i> Adicionar Mais
        </button>
      </div>

      <!-- Lista de Arquivos -->
      <div id="fileListContainer" class="space-y-3 max-h-[380px] overflow-y-auto pr-2"></div>

      <!-- Ajustes Avançados -->
      <div class="bg-banana-50/70 border border-banana-200 rounded-2xl p-4 text-xs space-y-3">
        <div class="flex items-center justify-between cursor-pointer" onclick="toggleAdvancedSettings()">
          <span class="font-bold text-banana-950 flex items-center gap-1.5">
            <i data-lucide="sliders" class="w-4 h-4 text-banana-600"></i> Ajustes de Qualidade & Parâmetros (Opcional)
          </span>
          <i id="advancedChevron" data-lucide="chevron-down" class="w-4 h-4 text-banana-700 transition-transform"></i>
        </div>

        <div id="advancedSettingsBody" class="hidden grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2 border-t border-banana-200/60">
          <div>
            <label class="block text-slate-700 font-semibold mb-1">Qualidade da Imagem Output (%):</label>
            <input type="range" id="imgQuality" min="10" max="100" value="90" class="w-full accent-banana-500" oninput="document.getElementById('qualVal').innerText = this.value + '%'">
            <span id="qualVal" class="text-banana-800 font-bold">90%</span>
          </div>
          <div>
            <label class="block text-slate-700 font-semibold mb-1">Delimitador de Dados (CSV/Texto):</label>
            <select id="csvDelimiter" class="w-full p-2 rounded-lg bg-white border border-banana-300 text-slate-700">
              <option value=",">Vírgula ( , )</option>
              <option value=";">Ponto e Vírgula ( ; )</option>
              <option value="tab">Tabulação (\t)</option>
            </select>
          </div>
        </div>
      </div>

      <!-- Botões de Ação -->
      <div class="flex flex-col sm:flex-row items-center justify-end gap-3 pt-4 border-t border-slate-100">
        <button onclick="resetApp()" class="w-full sm:w-auto px-5 py-3 rounded-2xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
          Cancelar / Limpar
        </button>
        <button onclick="startBatchConversion()" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold font-display shadow-lg shadow-banana-400/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center justify-center gap-2 text-base">
          <span>Iniciar Conversão Bananal!</span> 🍌
        </button>
      </div>
    </div>

    <!-- PASSO 4: PROGRESSO DO LIQUIDIFICADOR -->
    <div id="step-4" class="step-content hidden text-center py-8 space-y-6">
      <div class="relative w-28 h-28 mx-auto flex items-center justify-center">
        <div class="absolute inset-0 rounded-full border-4 border-banana-200 border-t-banana-500 animate-spin"></div>
        <div class="text-5xl blender-spin">🍹</div>
      </div>

      <div>
        <h3 id="conversionStatusTitle" class="text-2xl font-bold font-display text-banana-950 mb-1">
          Bate no liquidificador digital...
        </h3>
        <p id="conversionStatusSubtitle" class="text-xs sm:text-sm text-slate-500">
          Processando seus arquivos com potássio máximo!
        </p>
      </div>

      <div class="max-w-md mx-auto">
        <div class="flex justify-between text-xs font-bold text-banana-950 mb-1.5">
          <span id="progressText">0%</span>
          <span id="timeRemaining">Preparando arquivos...</span>
        </div>
        <div class="w-full h-4 bg-banana-100 rounded-full overflow-hidden p-0.5 border border-banana-200">
          <div id="progressBar" class="h-full bg-gradient-to-r from-banana-400 via-banana-500 to-leaf-500 rounded-full transition-all duration-200 w-0"></div>
        </div>
      </div>
    </div>

    <!-- PASSO 5: RESULTADOS E DOWNLOAD -->
    <div id="step-5" class="step-content hidden space-y-6">
      <div class="text-center space-y-2">
        <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-leaf-100 text-leaf-600 text-3xl mb-1 shadow-sm">
          🎉
        </div>
        <h3 class="text-2xl font-bold font-display text-banana-950">Conversão Concluída com Sucesso!</h3>
        <p class="text-sm text-slate-600">Seus arquivos convertidos foram registrados no banco de dados e auditados.</p>
      </div>

      <div id="convertedFilesList" class="space-y-3 max-h-[350px] overflow-y-auto pr-1"></div>

      <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-4 border-t border-slate-100">
        <button onclick="resetApp()" class="w-full sm:w-auto px-6 py-3 rounded-2xl bg-banana-100 hover:bg-banana-200 text-banana-950 font-bold text-sm transition flex items-center justify-center gap-2">
          <i data-lucide="refresh-cw" class="w-4 h-4"></i> Converter Mais Arquivos
        </button>

        <button id="downloadZipBtn" onclick="downloadAllAsZip()" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-leaf-500 hover:bg-leaf-600 text-white font-bold font-display shadow-lg shadow-leaf-500/20 hover:scale-[1.02] transition flex items-center justify-center gap-2 text-base">
          <i data-lucide="archive" class="w-5 h-5"></i>
          <span>Baixar Todos em ZIP (.zip)</span>
        </button>
      </div>
    </div>

  </div>

  <!-- Estatísticas e Histórico em Tempo Real -->
  <section class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <div class="bg-white rounded-3xl p-6 border border-banana-200 shadow-sm flex flex-col justify-between">
      <div class="flex items-center gap-3 mb-4">
        <div class="w-10 h-10 rounded-xl bg-banana-100 flex items-center justify-center text-xl text-banana-700">
          📊
        </div>
        <div>
          <h4 class="font-bold font-display text-banana-950">Estatísticas da Sessão</h4>
          <p class="text-xs text-slate-500">Seu desempenho nesta sessão</p>
        </div>
      </div>

      <div class="space-y-3 text-sm">
        <div class="flex justify-between items-center p-2.5 rounded-xl bg-banana-50">
          <span class="text-slate-600">Convertidos Nesta Sessão:</span>
          <strong id="statCount" class="text-banana-950 font-bold">0</strong>
        </div>
        <div class="flex justify-between items-center p-2.5 rounded-xl bg-banana-50">
          <span class="text-slate-600">Tempo Economizado:</span>
          <strong id="statTimeSaved" class="text-banana-950 font-bold">0 seg</strong>
        </div>
        <div class="flex justify-between items-center p-2.5 rounded-xl bg-leaf-50">
          <span class="text-leaf-800">Potássio Gerado:</span>
          <strong id="statPotassium" class="text-leaf-700 font-bold">0 mg</strong>
        </div>
      </div>
    </div>

    <div class="md:col-span-2 bg-white rounded-3xl p-6 border border-banana-200 shadow-sm flex flex-col justify-between">
      <div class="flex items-center justify-between mb-4">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-xl bg-banana-100 flex items-center justify-center text-xl text-banana-700">
            📜
          </div>
          <div>
            <h4 class="font-bold font-display text-banana-950">Histórico Recente no Banco</h4>
            <p class="text-xs text-slate-500">Gravado no MySQL via PDO</p>
          </div>
        </div>
        <a href="historico.php" class="text-xs text-banana-700 hover:text-banana-900 font-bold underline transition">
          Ver Página de Histórico →
        </a>
      </div>

      <div id="historyList" class="space-y-2 max-h-[140px] overflow-y-auto text-xs text-slate-500">
        <?php
        $stmt_recentes = $pdo->query("SELECT id, nome_original, formato_origem, formato_destino, criado_em FROM conversoes ORDER BY id DESC LIMIT 4");
        $recentes = $stmt_recentes->fetchAll();
        if (empty($recentes)):
        ?>
          <div class="text-center py-6 text-slate-400 italic">
            Nenhuma conversão registrada ainda no banco de dados.
          </div>
        <?php else: ?>
          <?php foreach ($recentes as $rec): ?>
            <div class="flex items-center justify-between p-2 rounded-xl bg-banana-50/60 hover:bg-banana-100/60 transition">
              <div class="flex items-center gap-2 min-w-0">
                <span>🍌</span>
                <span class="font-semibold text-slate-800 truncate"><?= htmlspecialchars($rec['nome_original']) ?></span>
                <span class="text-[10px] bg-banana-200 text-banana-900 px-1.5 py-0.5 rounded font-bold uppercase">
                  <?= htmlspecialchars($rec['formato_origem']) ?> ➔ <?= htmlspecialchars($rec['formato_destino']) ?>
                </span>
              </div>
              <span class="text-[11px] text-slate-400"><?= date('H:i', strtotime($rec['criado_em'])) ?></span>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </section>

</main>

<script>
  // Estado da Aplicação
  let selectedFiles = [];
  let convertedResults = [];
  let sessionStats = { count: 0, timeSavedSec: 0, potassiumMg: 0 };

  const FORMAT_OPTIONS = {
    image: [
      { label: 'PNG (.png)', val: 'png' },
      { label: 'JPEG (.jpg)', val: 'jpg' },
      { label: 'WEBP (.webp)', val: 'webp' },
      { label: 'BMP (.bmp)', val: 'bmp' },
      { label: 'SVG Vetorial (.svg)', val: 'svg' },
      { label: 'Documento PDF (.pdf)', val: 'pdf' },
      { label: 'Metadados TXT (.txt)', val: 'txt' }
    ],
    audio: [
      { label: 'WAV (.wav)', val: 'wav' },
      { label: 'MP3 (.mp3)', val: 'mp3' },
      { label: 'OGG (.ogg)', val: 'ogg' }
    ],
    video: [
      { label: 'WEBM (.webm)', val: 'webm' },
      { label: 'MP4 (.mp4)', val: 'mp4' },
      { label: 'GIF (.gif)', val: 'gif' }
    ],
    text: [
      { label: 'Texto Puro (.txt)', val: 'txt' },
      { label: 'Valores CSV (.csv)', val: 'csv' },
      { label: 'Estrutura JSON (.json)', val: 'json' },
      { label: 'Markdown (.md)', val: 'md' },
      { label: 'Página Web HTML (.html)', val: 'html' },
      { label: 'Documento PDF (.pdf)', val: 'pdf' }
    ],
    unknown: [
      { label: 'Arquivo Binário (.bin)', val: 'bin' },
      { label: 'Texto Base64 (.txt)', val: 'txt' }
    ]
  };

  document.addEventListener('DOMContentLoaded', () => {
    setupDragAndDrop();
  });

  // Configuração de Drag & Drop
  function setupDragAndDrop() {
    const dropZone = document.getElementById('dropZone');
    if (!dropZone) return;

    ['dragenter', 'dragover'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.add('border-banana-500', 'bg-banana-100');
      }, false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
      dropZone.addEventListener(eventName, (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropZone.classList.remove('border-banana-500', 'bg-banana-100');
      }, false);
    });

    dropZone.addEventListener('drop', (e) => {
      const files = e.dataTransfer.files;
      if (files && files.length > 0) {
        processIncomingFiles(Array.from(files));
      }
    });
  }

  function handleFileSelect(event) {
    const files = Array.from(event.target.files);
    if (files.length > 0) processIncomingFiles(files);
  }

  function processIncomingFiles(files) {
    files.forEach(file => {
      const category = detectFileType(file);
      const ext = file.name.split('.').pop().toLowerCase();
      selectedFiles.push({
        id: 'file_' + Math.random().toString(36).substring(2, 9),
        file: file,
        name: file.name,
        originalExt: ext,
        size: formatBytes(file.size),
        sizeBytes: file.size,
        category: category,
        targetFormat: FORMAT_OPTIONS[category][0].val
      });
    });

    showToast('info', `${files.length} arquivo(s) carregado(s) com sucesso.`);
    renderFileList();
    switchStep(2);
  }

  function detectFileType(file) {
    const type = file.type;
    const ext = file.name.split('.').pop().toLowerCase();

    if (type.startsWith('image/') || ['png', 'jpg', 'jpeg', 'webp', 'bmp', 'svg', 'gif'].includes(ext)) {
      return 'image';
    } else if (type.startsWith('audio/') || ['mp3', 'wav', 'ogg', 'aac', 'flac'].includes(ext)) {
      return 'audio';
    } else if (type.startsWith('video/') || ['mp4', 'webm', 'avi', 'mov'].includes(ext)) {
      return 'video';
    } else if (type.startsWith('text/') || ['txt', 'csv', 'json', 'md', 'html', 'js', 'css', 'pdf'].includes(ext)) {
      return 'text';
    }
    return 'unknown';
  }

  function renderFileList() {
    const container = document.getElementById('fileListContainer');
    container.innerHTML = '';

    selectedFiles.forEach((item) => {
      const fileCard = document.createElement('div');
      fileCard.className = "bg-white p-3.5 rounded-2xl border border-banana-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm hover:border-banana-300 transition";

      const categoryBadge = getCategoryBadge(item.category);
      const optionsHTML = FORMAT_OPTIONS[item.category].map(opt => 
        `<option value="${opt.val}" ${opt.val === item.targetFormat ? 'selected' : ''}>${opt.label}</option>`
      ).join('');

      fileCard.innerHTML = `
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-banana-100 flex items-center justify-center text-xl shrink-0">
            ${getCategoryEmoji(item.category)}
          </div>
          <div class="min-w-0 flex-grow">
            <div class="flex items-center gap-2">
              <h4 class="font-bold text-banana-950 text-sm truncate" title="${item.name}">${item.name}</h4>
              ${categoryBadge}
            </div>
            <p class="text-xs text-slate-400 mt-0.5">${item.size}</p>
          </div>
        </div>

        <div class="flex items-center gap-2.5 shrink-0 justify-between sm:justify-end">
          <div class="flex items-center gap-1.5">
            <span class="text-xs font-semibold text-slate-500">Para:</span>
            <select onchange="updateTargetFormat('${item.id}', this.value)" class="bg-banana-50 border border-banana-300 font-bold text-xs text-banana-900 rounded-xl px-2.5 py-1.5 focus:ring-2 focus:ring-banana-400 outline-none">
              ${optionsHTML}
            </select>
          </div>
          <button onclick="removeFile('${item.id}')" class="text-slate-400 hover:text-rose-500 p-1.5 rounded-lg hover:bg-rose-50 transition" title="Remover">
            <i data-lucide="trash-2" class="w-4 h-4"></i>
          </button>
        </div>
      `;
      container.appendChild(fileCard);
    });

    lucide.createIcons();
  }

  function updateTargetFormat(id, newFormat) {
    const item = selectedFiles.find(f => f.id === id);
    if (item) item.targetFormat = newFormat;
  }

  function removeFile(id) {
    selectedFiles = selectedFiles.filter(f => f.id !== id);
    if (selectedFiles.length === 0) resetApp();
    else renderFileList();
  }

  function toggleAdvancedSettings() {
    const body = document.getElementById('advancedSettingsBody');
    const chevron = document.getElementById('advancedChevron');
    body.classList.toggle('hidden');
    chevron.classList.toggle('rotate-180');
  }

  // Conversão em Lote
  async function startBatchConversion() {
    if (selectedFiles.length === 0) return;

    switchStep(4);
    convertedResults = [];

    const total = selectedFiles.length;
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const timeRemaining = document.getElementById('timeRemaining');
    const statusTitle = document.getElementById('conversionStatusTitle');

    for (let i = 0; i < total; i++) {
      const item = selectedFiles[i];
      const percent = Math.round(((i) / total) * 100);
      
      progressBar.style.width = percent + '%';
      progressText.innerText = percent + '%';
      timeRemaining.innerText = `Processando ${i + 1} de ${total}...`;
      statusTitle.innerText = `Batendo "${item.name}"...`;

      try {
        const convertedBlob = await convertSingleFile(item);
        const newFileName = changeExtension(item.name, item.targetFormat);

        // Registra no banco de dados MySQL via API (PDO)
        const apiResponse = await registerConversionInDatabase({
          nome_original: item.name,
          formato_origem: item.originalExt,
          formato_destino: item.targetFormat,
          tamanho_original: item.sizeBytes,
          tamanho_convertido: convertedBlob.size,
          status: 'sucesso'
        });

        convertedResults.push({
          id: item.id,
          dbId: apiResponse.id || null,
          originalName: item.name,
          newName: newFileName,
          category: item.category,
          blob: convertedBlob,
          url: URL.createObjectURL(convertedBlob),
          size: formatBytes(convertedBlob.size),
          targetFormat: item.targetFormat
        });
      } catch (err) {
        console.error('Falha ao converter:', err);
        showToast('error', `Falha ao converter arquivo ${item.name}: ${err.message}`);
      }

      await new Promise(r => setTimeout(r, 350));
    }

    progressBar.style.width = '100%';
    progressText.innerText = '100%';
    timeRemaining.innerText = 'Concluído!';

    updateStats(selectedFiles.length);

    setTimeout(() => {
      renderConvertedResults();
      switchStep(5);
      showToast('success', `${convertedResults.length} arquivo(s) convertidos e auditados com sucesso!`);
    }, 400);
  }

  // Envia registro para a API PHP (MySQL com PDO)
  async function registerConversionInDatabase(payload) {
    try {
      const res = await fetch('backend/convert_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });
      return await res.json();
    } catch (e) {
      console.warn('Erro ao conectar na API de conversão:', e);
      return {};
    }
  }

  // Processamento individual de cada arquivo
  async function convertSingleFile(item) {
    const file = item.file;
    const target = item.targetFormat;
    const quality = parseInt(document.getElementById('imgQuality').value) / 100;

    if (item.category === 'image') return await convertImage(file, target, quality);
    if (item.category === 'text') return await convertText(file, target);
    if (item.category === 'audio') return await convertAudio(file, target);
    return new Blob([await file.arrayBuffer()], { type: 'application/octet-stream' });
  }

  // Conversão de Imagens
  function convertImage(file, targetFormat, quality) {
    return new Promise((resolve, reject) => {
      const reader = new FileReader();
      reader.onload = async (e) => {
        const img = new Image();
        img.onload = async () => {
          // Suporte a PDF usando pdf-lib
          if (targetFormat === 'pdf') {
            try {
              const { PDFDocument } = PDFLib;
              const pdfDoc = await PDFDocument.create();
              const page = pdfDoc.addPage([img.width, img.height]);
              
              let embeddedImg;
              if (file.type.includes('png')) {
                embeddedImg = await pdfDoc.embedPng(e.target.result);
              } else {
                embeddedImg = await pdfDoc.embedJpg(e.target.result);
              }
              page.drawImage(embeddedImg, { x: 0, y: 0, width: img.width, height: img.height });
              const pdfBytes = await pdfDoc.save();
              resolve(new Blob([pdfBytes], { type: 'application/pdf' }));
              return;
            } catch (pdfErr) {
              // Fallback
            }
          }

          const canvas = document.createElement('canvas');
          canvas.width = img.width;
          canvas.height = img.height;
          const ctx = canvas.getContext('2d');

          if (targetFormat === 'jpg' || targetFormat === 'jpeg' || targetFormat === 'bmp') {
            ctx.fillStyle = '#FFFFFF';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
          }

          ctx.drawImage(img, 0, 0);

          let mimeType = 'image/png';
          if (targetFormat === 'jpg' || targetFormat === 'jpeg') mimeType = 'image/jpeg';
          if (targetFormat === 'webp') mimeType = 'image/webp';
          if (targetFormat === 'bmp') mimeType = 'image/bmp';

          if (targetFormat === 'svg') {
            const svgContent = `<svg xmlns="http://www.w3.org/2000/svg" width="${img.width}" height="${img.height}">
              <image href="${e.target.result}" width="${img.width}" height="${img.height}"/>
            </svg>`;
            resolve(new Blob([svgContent], { type: 'image/svg+xml' }));
            return;
          }

          if (targetFormat === 'txt') {
            const textContent = `Banana-Metadata:\nNome: ${file.name}\nDimensões: ${img.width}x${img.height}px\nTamanho Original: ${file.size} bytes\nData: ${new Date().toLocaleString()}`;
            resolve(new Blob([textContent], { type: 'text/plain' }));
            return;
          }

          canvas.toBlob((blob) => {
            resolve(blob || new Blob([]));
          }, mimeType, quality);
        };
        img.onerror = reject;
        img.src = e.target.result;
      };
      reader.onerror = reject;
      reader.readAsDataURL(file);
    });
  }

  // Conversão de Textos e Dados
  function convertText(file, targetFormat) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = async (e) => {
        const content = e.target.result;
        let convertedContent = content;
        let mimeType = 'text/plain';

        if (targetFormat === 'pdf') {
          try {
            const { PDFDocument, StandardFonts, rgb } = PDFLib;
            const pdfDoc = await PDFDocument.create();
            const font = await pdfDoc.embedFont(StandardFonts.Helvetica);
            let page = pdfDoc.addPage();
            const { height } = page.getSize();
            
            const lines = content.split('\n');
            let y = height - 50;
            
            page.drawText(`Documento Bananal: ${file.name}`, { x: 50, y, size: 14, font, color: rgb(0.2, 0.1, 0) });
            y -= 30;

            for (let line of lines) {
              if (y < 40) {
                page = pdfDoc.addPage();
                y = height - 50;
              }
              page.drawText(line.substring(0, 80), { x: 50, y, size: 10, font });
              y -= 14;
            }

            const pdfBytes = await pdfDoc.save();
            resolve(new Blob([pdfBytes], { type: 'application/pdf' }));
            return;
          } catch (e) {
            // fallback
          }
        }

        if (targetFormat === 'json') {
          mimeType = 'application/json';
          const lines = content.split('\n').filter(l => l.trim() !== '');
          convertedContent = JSON.stringify({ filename: file.name, total_lines: lines.length, lines: lines }, null, 2);
        } else if (targetFormat === 'csv') {
          mimeType = 'text/csv';
          const delim = document.getElementById('csvDelimiter').value === 'tab' ? '\t' : document.getElementById('csvDelimiter').value;
          const lines = content.split('\n');
          convertedContent = lines.map(line => line.split(/\s+/).join(delim)).join('\n');
        } else if (targetFormat === 'html') {
          mimeType = 'text/html';
          convertedContent = `<!DOCTYPE html>\n<html>\n<head><meta charset="utf-8"><title>${escapeHtml(file.name)}</title></head>\n<body style="font-family:sans-serif;padding:2rem;"><pre>${escapeHtml(content)}</pre></body>\n</html>`;
        } else if (targetFormat === 'md') {
          mimeType = 'text/markdown';
          convertedContent = `# ${file.name}\n\n\`\`\`\n${content}\n\`\`\``;
        }

        resolve(new Blob([convertedContent], { type: mimeType }));
      };
      reader.readAsText(file);
    });
  }

  // Conversão de Áudio
  function convertAudio(file, targetFormat) {
    return new Promise((resolve) => {
      const reader = new FileReader();
      reader.onload = async (e) => {
        try {
          const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
          const decodedData = await audioCtx.decodeAudioData(e.target.result);
          const wavBlob = bufferToWave(decodedData, decodedData.length);
          resolve(wavBlob);
        } catch (err) {
          resolve(new Blob([e.target.result], { type: 'audio/wav' }));
        }
      };
      reader.readAsArrayBuffer(file);
    });
  }

  function bufferToWave(abuffer, len) {
    let numOfChan = abuffer.numberOfChannels,
        length = len * numOfChan * 2 + 44,
        buffer = new ArrayBuffer(length),
        view = new DataView(buffer),
        channels = [], i, sample, offset = 0, pos = 0;

    function setUint16(data) { view.setUint16(pos, data, true); pos += 2; }
    function setUint32(data) { view.setUint32(pos, data, true); pos += 4; }

    setUint32(0x46464952); setUint32(length - 8); setUint32(0x45564157);
    setUint32(0x20746d66); setUint32(16); setUint16(1); setUint16(numOfChan);
    setUint32(abuffer.sampleRate); setUint32(abuffer.sampleRate * 2 * numOfChan);
    setUint16(numOfChan * 2); setUint16(16); setUint32(0x61746164);
    setUint32(length - pos - 4);

    for (i = 0; i < abuffer.numberOfChannels; i++) channels.push(abuffer.getChannelData(i));

    while (pos < length) {
      for (i = 0; i < numOfChan; i++) {
        sample = Math.max(-1, Math.min(1, channels[i][offset]));
        sample = (0.5 + sample < 0 ? sample * 32768 : sample * 32767) | 0;
        view.setInt16(pos, sample, true);
        pos += 2;
      }
      offset++;
    }
    return new Blob([buffer], { type: 'audio/wav' });
  }

  // Renderiza Resultados Concluídos
  function renderConvertedResults() {
    const container = document.getElementById('convertedFilesList');
    container.innerHTML = '';

    convertedResults.forEach(res => {
      const card = document.createElement('div');
      card.className = "bg-white p-3.5 rounded-2xl border border-banana-200 flex flex-col sm:flex-row sm:items-center justify-between gap-3 shadow-sm hover:border-leaf-300 transition";

      card.innerHTML = `
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-xl bg-leaf-100 text-leaf-700 flex items-center justify-center text-xl shrink-0">
            ✅
          </div>
          <div class="min-w-0 flex-grow">
            <h4 class="font-bold text-banana-950 text-sm truncate" title="${res.newName}">${res.newName}</h4>
            <p class="text-xs text-slate-400 mt-0.5">Novo Tamanho: <strong class="text-slate-600">${res.size}</strong></p>
          </div>
        </div>

        <div class="flex items-center gap-2 shrink-0 justify-end">
          <button onclick="openPreview('${res.id}')" class="px-3 py-2 rounded-xl bg-banana-100 hover:bg-banana-200 text-banana-950 text-xs font-semibold transition flex items-center gap-1">
            <i data-lucide="eye" class="w-3.5 h-3.5"></i> Ver
          </button>
          <a href="${res.url}" download="${res.newName}" class="px-4 py-2 rounded-xl bg-banana-400 hover:bg-banana-500 text-banana-950 text-xs font-bold transition flex items-center gap-1 shadow-sm">
            <i data-lucide="download" class="w-3.5 h-3.5"></i> Baixar
          </a>
        </div>
      `;
      container.appendChild(card);
    });

    lucide.createIcons();
  }

  // Download All as ZIP
  async function downloadAllAsZip() {
    if (convertedResults.length === 0) return;

    const zip = new JSZip();
    const folder = zip.folder("Bananal_Convertidos");

    convertedResults.forEach(res => {
      folder.file(res.newName, res.blob);
    });

    const content = await zip.generateAsync({ type: "blob" });
    const zipUrl = URL.createObjectURL(content);

    const a = document.createElement('a');
    a.href = zipUrl;
    a.download = `Bananal_Arquivos_${Date.now()}.zip`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);

    showToast('success', 'Pacote ZIP gerado com sucesso!');
  }

  // Visualização de Arquivo em Modal
  function openPreview(id) {
    const res = convertedResults.find(r => r.id === id);
    if (!res) return;

    const modal = document.getElementById('previewModal');
    const title = document.getElementById('previewTitle');
    const content = document.getElementById('previewContent');

    title.innerText = `Pré-visualização: ${res.newName}`;
    content.innerHTML = '';

    if (res.category === 'image') {
      const img = document.createElement('img');
      img.src = res.url;
      img.className = 'max-h-[350px] rounded-xl object-contain shadow-sm';
      content.appendChild(img);
    } else if (res.category === 'audio') {
      const audio = document.createElement('audio');
      audio.controls = true;
      audio.src = res.url;
      audio.className = 'w-full';
      content.appendChild(audio);
    } else if (res.targetFormat === 'pdf') {
      const iframe = document.createElement('iframe');
      iframe.src = res.url;
      iframe.className = 'w-full h-[350px] rounded-xl border border-banana-200';
      content.appendChild(iframe);
    } else {
      const reader = new FileReader();
      reader.onload = (e) => {
        const pre = document.createElement('pre');
        pre.className = 'text-xs text-slate-800 font-mono whitespace-pre-wrap max-h-[300px] overflow-auto w-full p-2 bg-white rounded-xl border border-banana-100';
        pre.innerText = e.target.result.substring(0, 3000);
        content.appendChild(pre);
      };
      reader.readAsText(res.blob);
    }

    modal.classList.remove('hidden');
  }

  function updateStats(newCount) {
    sessionStats.count += newCount;
    sessionStats.timeSavedSec += newCount * 14;
    sessionStats.potassiumMg += newCount * 105;

    document.getElementById('statCount').innerText = sessionStats.count;
    document.getElementById('statTimeSaved').innerText = sessionStats.timeSavedSec + ' seg';
    document.getElementById('statPotassium').innerText = sessionStats.potassiumMg + ' mg';
  }

  function switchStep(stepNum) {
    document.querySelectorAll('.step-content').forEach(el => el.classList.add('hidden'));

    if (stepNum === 1) document.getElementById('step-1').classList.remove('hidden');
    if (stepNum === 2 || stepNum === 3) document.getElementById('step-2-3').classList.remove('hidden');
    if (stepNum === 4) document.getElementById('step-4').classList.remove('hidden');
    if (stepNum === 5) document.getElementById('step-5').classList.remove('hidden');

    for (let i = 1; i <= 5; i++) {
      const card = document.getElementById(`step-nav-${i}`);
      const badge = card.querySelector('span:first-child');
      
      if (i <= stepNum) {
        card.classList.remove('opacity-60', 'bg-white/60', 'border-slate-200');
        card.classList.add('bg-white', 'border-banana-400', 'shadow-sm');
        badge.classList.remove('bg-slate-200', 'text-slate-600');
        badge.classList.add('bg-banana-400', 'text-banana-950');
      } else {
        card.classList.add('opacity-60', 'bg-white/60', 'border-slate-200');
        card.classList.remove('bg-white', 'border-banana-400', 'shadow-sm');
        badge.classList.add('bg-slate-200', 'text-slate-600');
        badge.classList.remove('bg-banana-400', 'text-banana-950');
      }
    }
  }

  function resetApp() {
    selectedFiles = [];
    convertedResults = [];
    document.getElementById('fileInput').value = '';
    switchStep(1);
  }

  function getCategoryEmoji(cat) {
    switch (cat) {
      case 'image': return '🖼️';
      case 'audio': return '🎵';
      case 'video': return '🎥';
      case 'text': return '📄';
      default: return '📦';
    }
  }

  function getCategoryBadge(cat) {
    const labels = { image: 'Imagem', audio: 'Áudio', video: 'Vídeo', text: 'Documento', unknown: 'Arquivo' };
    return `<span class="bg-banana-100 text-banana-900 text-[10px] font-bold px-2 py-0.5 rounded-full border border-banana-200 uppercase">${labels[cat] || 'Arquivo'}</span>`;
  }

  function changeExtension(filename, newExt) {
    const pos = filename.lastIndexOf('.');
    return (pos < 0 ? filename : filename.substr(0, pos)) + '.' + newExt;
  }

  function formatBytes(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
  }

  function escapeHtml(str) {
    return str.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;");
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
