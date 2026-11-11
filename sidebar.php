<!-- Menu Lateral no estilo Convertio (Convertio-style Side Menu Drawer) -->
<!-- Atende ao requisito do CORE: "Além disso como no https://convertio.co/pt/ deve ter um menu do lado." -->

<!-- Backdrop / Overlay escurecido -->
<div 
  id="sidebarBackdrop" 
  onclick="toggleSidebar(false)" 
  class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm z-50 hidden opacity-0 transition-opacity duration-300"
></div>

<!-- Gaveta Lateral (Slide-over Sidebar) -->
<aside 
  id="convertioSidebar" 
  class="fixed top-0 left-0 bottom-0 w-80 sm:w-96 bg-white z-50 shadow-2xl transform -translate-x-full transition-transform duration-300 ease-in-out flex flex-col border-r border-banana-200"
>
  <!-- Topo do Menu Lateral -->
  <div class="p-5 border-b border-banana-200 bg-banana-50/70 flex items-center justify-between">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-2xl bg-banana-400 flex items-center justify-center text-xl shadow-md banana-bounce">
        🍌
      </div>
      <div>
        <h3 class="font-bold font-display text-banana-950 text-lg leading-tight">Menu Bananal</h3>
        <p class="text-[11px] text-banana-700 font-medium">Estilo Convertio • Todos os Conversores</p>
      </div>
    </div>
    <button 
      onclick="toggleSidebar(false)" 
      class="w-9 h-9 rounded-xl bg-white hover:bg-banana-200 border border-banana-300 text-banana-950 flex items-center justify-center transition shadow-sm font-bold"
      title="Fechar Menu"
    >
      ✕
    </button>
  </div>

  <!-- Campo de Busca de Conversores na Barra Lateral -->
  <div class="p-4 border-b border-slate-100 bg-white">
    <div class="relative">
      <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3"></i>
      <input 
        type="text" 
        id="sidebarSearchInput" 
        oninput="filtrarSidebarConversores(this.value)" 
        placeholder="Pesquisar conversor (ex: jpg, mp3)..." 
        class="w-full pl-10 pr-4 py-2 rounded-xl bg-banana-50/80 border border-banana-200 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-banana-400 transition"
      >
    </div>
  </div>

  <!-- Lista de Categorias de Conversores (Scrollável) -->
  <div class="flex-grow overflow-y-auto p-4 space-y-4" id="sidebarContent">
    
    <!-- Categorias de Conversores -->
    <div>
      <span class="text-[11px] font-bold uppercase tracking-wider text-banana-800 px-2 block mb-2">
        📂 Categorias de Arquivos
      </span>
      <div class="space-y-1.5" id="sidebarCategoriesList">

        <!-- Conversor de Imagem -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('image')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">🖼️</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Conversor de Imagem</h4>
                <p class="text-[10px] text-slate-400">PNG, JPG, WEBP, SVG, BMP, GIF, ICO</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

        <!-- Conversor de Áudio -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('audio')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">🎵</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Conversor de Áudio</h4>
                <p class="text-[10px] text-slate-400">MP3, WAV, OGG, AAC, FLAC, M4A</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

        <!-- Conversor de Documento -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('text')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">📄</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Conversor de Documento</h4>
                <p class="text-[10px] text-slate-400">PDF, TXT, JSON, CSV, MD, HTML</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

        <!-- Conversor de Vídeo -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('video')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">🎥</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Conversor de Vídeo</h4>
                <p class="text-[10px] text-slate-400">MP4, WEBM, AVI, MKV, MOV, GIF</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

        <!-- Conversor de Apresentação & Planilha -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('text')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">📊</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Apresentações & Planilhas</h4>
                <p class="text-[10px] text-slate-400">PPTX, XLSX, CSV, PDF, JSON</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

        <!-- Arquivos Compactados -->
        <div class="sidebar-item rounded-2xl p-2.5 hover:bg-banana-50 transition border border-transparent hover:border-banana-200 cursor-pointer" onclick="selecionarCategoriaSidebar('unknown')">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-3">
              <span class="w-8 h-8 rounded-xl bg-banana-100 text-banana-800 flex items-center justify-center text-base">🗜️</span>
              <div>
                <h4 class="font-bold text-xs text-banana-950">Compactados & ZIP</h4>
                <p class="text-[10px] text-slate-400">ZIP, RAR, 7Z, TAR, GZ</p>
              </div>
            </div>
            <i data-lucide="chevron-right" class="w-4 h-4 text-banana-500"></i>
          </div>
        </div>

      </div>
    </div>

    <!-- Atalhos Mais Populares (Estilo Convertio) -->
    <div class="pt-3 border-t border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-wider text-banana-800 px-2 block mb-2">
        ⚡ Conversões Populares
      </span>
      <div class="grid grid-cols-2 gap-2 text-xs">
        <button onclick="atalhoConversao('jpg', 'png')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>🍌</span> JPG para PNG
        </button>
        <button onclick="atalhoConversao('png', 'webp')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>⚡</span> PNG para WEBP
        </button>
        <button onclick="atalhoConversao('wav', 'mp3')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>🎵</span> WAV para MP3
        </button>
        <button onclick="atalhoConversao('txt', 'pdf')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>📄</span> TXT para PDF
        </button>
        <button onclick="atalhoConversao('csv', 'json')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>📊</span> CSV para JSON
        </button>
        <button onclick="atalhoConversao('mp4', 'mp3')" class="p-2 rounded-xl bg-banana-50 hover:bg-banana-100 text-banana-950 font-semibold border border-banana-200 text-left transition flex items-center gap-1.5">
          <span>🎬</span> MP4 para MP3
        </button>
      </div>
    </div>

    <!-- Navegação Global da Aplicação -->
    <div class="pt-3 border-t border-slate-100">
      <span class="text-[11px] font-bold uppercase tracking-wider text-banana-800 px-2 block mb-2">
        🧭 Navegação Geral
      </span>
      <nav class="space-y-1 text-xs font-bold text-slate-700">
        <a href="index.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
          <i data-lucide="refresh-cw" class="w-4 h-4 text-banana-600"></i> Conversor Principal
        </a>
        <a href="historico.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
          <i data-lucide="history" class="w-4 h-4 text-banana-600"></i> Histórico de Conversões
        </a>
        <a href="auditoria.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
          <i data-lucide="shield-check" class="w-4 h-4 text-leaf-600"></i> Auditoria & Segurança
        </a>
        <a href="manual.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
          <i data-lucide="book-open" class="w-4 h-4 text-banana-600"></i> Manual & Guia de Formatos
        </a>
        <?php if (usuario_esta_logado()): ?>
          <a href="perfil.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
            <i data-lucide="user" class="w-4 h-4 text-banana-600"></i> Meu Perfil
          </a>
        <?php else: ?>
          <a href="login.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
            <i data-lucide="log-in" class="w-4 h-4 text-banana-600"></i> Entrar na Conta
          </a>
          <a href="registro.php" class="flex items-center gap-2.5 p-2 rounded-xl hover:bg-banana-50 hover:text-banana-950 transition">
            <i data-lucide="user-plus" class="w-4 h-4 text-banana-600"></i> Criar Conta Gratuita
          </a>
        <?php endif; ?>
      </nav>
    </div>

  </div>

  <!-- Rodapé da Barra Lateral -->
  <div class="p-4 border-t border-banana-200 bg-banana-50/50 flex items-center justify-between text-xs text-banana-800">
    <div class="flex items-center gap-1.5 font-bold">
      <span>🍌</span> Conversor Bananal
    </div>
    <span class="text-[10px] bg-banana-200 px-2 py-0.5 rounded-full font-bold">v2.5</span>
  </div>
</aside>

<script>
/**
 * Abre ou fecha o menu lateral estilo Convertio
 */
function toggleSidebar(open = null) {
  const sidebar = document.getElementById('convertioSidebar');
  const backdrop = document.getElementById('sidebarBackdrop');
  if (!sidebar || !backdrop) return;

  const isCurrentlyOpen = !sidebar.classList.contains('-translate-x-full');
  const shouldOpen = open !== null ? open : !isCurrentlyOpen;

  if (shouldOpen) {
    backdrop.classList.remove('hidden');
    setTimeout(() => backdrop.classList.remove('opacity-0'), 10);
    sidebar.classList.remove('-translate-x-full');
    document.body.classList.add('overflow-hidden');
  } else {
    backdrop.classList.add('opacity-0');
    sidebar.classList.add('-translate-x-full');
    setTimeout(() => backdrop.classList.add('hidden'), 300);
    document.body.classList.remove('overflow-hidden');
  }
}

/**
 * Filtra as opções no menu lateral
 */
function filtrarSidebarConversores(termo) {
  const query = termo.toLowerCase().trim();
  const items = document.querySelectorAll('#sidebarCategoriesList .sidebar-item');
  items.forEach(el => {
    const text = el.innerText.toLowerCase();
    if (text.includes(query)) {
      el.style.display = 'block';
    } else {
      el.style.display = 'none';
    }
  });
}

/**
 * Ação ao clicar em uma categoria no menu lateral
 */
function selecionarCategoriaSidebar(cat) {
  toggleSidebar(false);
  // Se estiver na index, dispara o seletor de arquivos
  const fileInput = document.getElementById('fileInput');
  if (fileInput) {
    if (cat === 'image') fileInput.accept = 'image/*';
    else if (cat === 'audio') fileInput.accept = 'audio/*';
    else if (cat === 'video') fileInput.accept = 'video/*';
    else if (cat === 'text') fileInput.accept = '.txt,.csv,.json,.md,.html,.pdf';
    else fileInput.accept = '';

    fileInput.click();
  } else {
    window.location.href = 'index.php?cat=' + cat;
  }
}

/**
 * Atalho de conversão rápida
 */
function atalhoConversao(origem, destino) {
  toggleSidebar(false);
  const fileInput = document.getElementById('fileInput');
  if (fileInput) {
    fileInput.accept = '.' + origem;
    window._presetTargetFormat = destino;
    fileInput.click();
    showToast('info', `Selecione um arquivo .${origem.toUpperCase()} para converter para .${destino.toUpperCase()}`);
  } else {
    window.location.href = `index.php?from=${origem}&to=${destino}`;
  }
}
</script>
