<?php
$page_title = 'Manual de Conversão & Guia de Formatos - Conversor Bananal 🍌';
require_once __DIR__ . '/includes/header.php';
?>

<main class="max-w-6xl mx-auto px-4 py-8 flex-grow w-full space-y-8">
  
  <!-- Cabeçalho do Manual -->
  <div class="bg-gradient-to-r from-banana-300 via-banana-200 to-banana-100 rounded-3xl p-6 sm:p-8 border border-banana-300 shadow-sm">
    <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
      <div class="max-w-2xl">
        <div class="flex items-center gap-2 mb-2">
          <span class="bg-white text-banana-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider shadow-sm">
            📖 Guia Definitivo
          </span>
          <span class="text-xs text-banana-800 font-medium">Aprenda a escolher o formato certo</span>
        </div>
        <h2 class="text-3xl font-extrabold font-display text-banana-950 mb-2">
          Manual do Conversor Bananal 🍌
        </h2>
        <p class="text-sm text-banana-900 leading-relaxed">
          Dúvidas sobre quando usar PNG, JPG, WEBP, WAV, MP3 ou JSON? Explore abaixo a explicação detalhada de cada formato, vantagens, limitações e respostas para perguntas frequentes!
        </p>
      </div>

      <!-- Barra de Busca Rápida no Manual -->
      <div class="w-full md:w-72 relative">
        <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5"></i>
        <input 
          type="text" 
          id="manualSearchInput" 
          oninput="filterManualCards()" 
          placeholder="Buscar formato ou extensão..." 
          class="w-full pl-10 pr-4 py-2.5 rounded-2xl bg-white border border-banana-300 text-xs text-slate-800 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-banana-500 shadow-sm"
        >
      </div>
    </div>
  </div>

  <!-- Abas de Navegação do Manual -->
  <div class="flex flex-wrap items-center gap-2 border-b border-banana-200 pb-3">
    <button onclick="setManualTab('all')" id="tab-manual-all" class="px-4 py-2 rounded-xl text-xs font-bold bg-banana-400 text-banana-950 shadow-sm transition">
      Todos os Formatos
    </button>
    <button onclick="setManualTab('image')" id="tab-manual-image" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition">
      🖼️ Imagens
    </button>
    <button onclick="setManualTab('audio')" id="tab-manual-audio" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition">
      🎵 Áudio & Som
    </button>
    <button onclick="setManualTab('text')" id="tab-manual-text" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition">
      📄 Documentos & Texto
    </button>
    <button onclick="setManualTab('faq')" id="tab-manual-faq" class="px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition">
      ❓ Dúvidas Frequentes (FAQ)
    </button>
  </div>

  <!-- Grid de Formatos -->
  <div id="manualCardsGrid" class="grid grid-cols-1 md:grid-cols-2 gap-6">

    <!-- JPG -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="image" data-keywords="jpg jpeg imagem foto lossy compressao">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            JPG
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">JPEG / JPG</h3>
            <p class="text-xs text-slate-400">Joint Photographic Experts Group</p>
          </div>
        </div>
        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-full uppercase">Imagens / Fotos</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        O formato mais popular do mundo para fotografias digitais. Utiliza compressão com perdas (lossy) para reduzir dramaticamente o tamanho dos arquivos.
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Fotos reais, imagens de fundo pesadas, envio rápido por e-mail ou redes sociais. Arquivos muito leves!
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Não suporta fundo transparente. Perde qualidade se reconvertido muitas vezes.
        </div>
      </div>
    </div>

    <!-- PNG -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="image" data-keywords="png transparencia logos icones lossless sem perda">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            PNG
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">PNG</h3>
            <p class="text-xs text-slate-400">Portable Network Graphics</p>
          </div>
        </div>
        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-full uppercase">Imagens / Transparência</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Padrão ouro para imagens digitais nítidas e com suporte total a canal alfa (fundo transparente). Utiliza compressão sem perdas (lossless).
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Logotipos, ícones, ilustrações, capturas de tela e qualquer imagem que precise de fundo transparente.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Pode gerar arquivos muito pesados se usado em fotos complexas com milhões de cores.
        </div>
      </div>
    </div>

    <!-- WEBP -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="image" data-keywords="webp google moderna web otimizada">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            WEBP
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">WEBP</h3>
            <p class="text-xs text-slate-400">Desenvolvido pelo Google</p>
          </div>
        </div>
        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-full uppercase">Imagens Modernas</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Formato de imagem moderno de alta eficiência. É cerca de 30% mais leve que o JPEG e suporta transparência como o PNG!
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Sites, lojas virtuais e aplicativos que precisam de velocidade de carregamento ultrarrápida.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Pode não abrir nativamente em editores gráficos muito antigos.
        </div>
      </div>
    </div>

    <!-- WAV -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="audio" data-keywords="wav audio som estudo pcm alta qualidade">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-purple-100 text-purple-800 font-bold flex items-center justify-center font-display text-sm">
            WAV
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">Waveform Audio (WAV)</h3>
            <p class="text-xs text-slate-400">Áudio Não Comprimido (PCM)</p>
          </div>
        </div>
        <span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-2.5 py-1 rounded-full uppercase">Áudio Puro</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Formato de estúdio que preserva cada detalhe da onda sonora original sem nenhum algoritmo destrutivo de compressão.
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Edição de podcasts, gravações de estúdio, efeitos sonoros para jogos e alta fidelidade acústica.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Arquivos enormes (cerca de 10 MB por minuto de áudio).
        </div>
      </div>
    </div>

    <!-- MP3 -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="audio" data-keywords="mp3 musica audio som leve podcast">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-purple-100 text-purple-800 font-bold flex items-center justify-center font-display text-sm">
            MP3
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">MPEG-1 Audio Layer III</h3>
            <p class="text-xs text-slate-400">Padrão Universal de Música</p>
          </div>
        </div>
        <span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-2.5 py-1 rounded-full uppercase">Música & Fala</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        O formato de som mais compatível do planeta, capaz de rodar em qualquer smartphone, som de carro ou tocador portátil.
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Distribuição de músicas, podcasts no Spotify, envio por WhatsApp e economia de armazenamento.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Descarta frequências inaudíveis para humanos (compressão psicoacústica).
        </div>
      </div>
    </div>

    <!-- PDF -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="text" data-keywords="pdf documento relatorio impressao adobe">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-800 font-bold flex items-center justify-center font-display text-sm">
            PDF
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">Portable Document Format</h3>
            <p class="text-xs text-slate-400">Criado pela Adobe</p>
          </div>
        </div>
        <span class="text-[10px] bg-sky-100 text-sky-800 font-bold px-2.5 py-1 rounded-full uppercase">Documentos Oficiais</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Garante que seu documento será impresso e visualizado exatamente da mesma forma em qualquer dispositivo ou sistema operacional.
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Contratos, relatórios, currículos, apostilas e trabalhos acadêmicos para impressão.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Mais difícil de editar textos posteriormente que um arquivo TXT ou DOCX.
        </div>
      </div>
    </div>

    <!-- CSV & JSON -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="text" data-keywords="csv json dados planilha tabela api programacao">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-sky-100 text-sky-800 font-bold flex items-center justify-center font-display text-sm">
            DATA
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">JSON / CSV</h3>
            <p class="text-xs text-slate-400">Intercâmbio de Dados Estruturados</p>
          </div>
        </div>
        <span class="text-[10px] bg-sky-100 text-sky-800 font-bold px-2.5 py-1 rounded-full uppercase">Bancos & Planilhas</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Formatos ideais para importar em planilhas (Excel, Google Sheets) ou consumir em APIs REST e bancos de dados SQL/NoSQL.
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Exportações de tabelas, relatórios analíticos, cadastros e migração de sistemas.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Não formatam estilos visuais como cores, fontes ou imagens decorativas.
        </div>
      </div>
    </div>

  </div>

  <!-- Seção FAQ (Dúvidas Frequentes) -->
  <div id="manualFaqSection" class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-sm space-y-4">
    <div class="flex items-center gap-2 mb-4">
      <span class="text-2xl">🍌</span>
      <h3 class="font-bold font-display text-xl text-banana-950">Perguntas Frequentes (FAQ)</h3>
    </div>

    <div class="space-y-3 text-xs">
      <details class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200 group">
        <summary class="font-bold text-banana-950 cursor-pointer flex justify-between items-center">
          <span>O Conversor Bananal armazena minhas imagens e arquivos pessoais?</span>
          <span class="text-banana-700 transition group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Não! A grande maioria das conversões de mídia é processada diretamente nos recursos nativos do seu navegador (Canvas e Web Audio API). Apenas os metadados e o registro de auditoria são salvos no MySQL via PDO para o seu histórico seguro.
        </p>
      </details>

      <details class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200 group">
        <summary class="font-bold text-banana-950 cursor-pointer flex justify-between items-center">
          <span>Como funciona a segurança das senhas com Bcrypt?</span>
          <span class="text-banana-700 transition group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-slate-600 leading-relaxed">
          Todas as senhas passam pela função nativa do PHP <code class="bg-banana-200 px-1 rounded font-mono">password_hash($senha, PASSWORD_BCRYPT)</code>. Isso gera um hash criptográfico irreversível e com salt individual, protegendo 100% contra ataques de dicionário ou vazamentos.
        </p>
      </details>

      <details class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200 group">
        <summary class="font-bold text-banana-950 cursor-pointer flex justify-between items-center">
          <span>Como baixar vários arquivos convertidos de uma só vez?</span>
          <span class="text-banana-700 transition group-open:rotate-180">▼</span>
        </summary>
        <p class="mt-2 text-slate-600 leading-relaxed">
          No Passo 5 (Baixar), clique no botão verde <strong>"Baixar Todos em ZIP (.zip)"</strong>. O sistema empacotará todos os arquivos convertidos instantaneamente usando a biblioteca JSZip.
        </p>
      </details>
    </div>
  </div>

</main>

<script>
  function setManualTab(tab) {
    const tabs = ['all', 'image', 'audio', 'text', 'faq'];
    tabs.forEach(t => {
      const btn = document.getElementById(`tab-manual-${t}`);
      if (btn) {
        if (t === tab) {
          btn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-banana-400 text-banana-950 shadow-sm transition";
        } else {
          btn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition";
        }
      }
    });

    const cards = document.querySelectorAll('.manual-card');
    const faq = document.getElementById('manualFaqSection');

    if (tab === 'faq') {
      cards.forEach(c => c.style.display = 'none');
      faq.style.display = 'block';
    } else {
      faq.style.display = (tab === 'all') ? 'block' : 'none';
      cards.forEach(c => {
        if (tab === 'all' || c.getAttribute('data-category') === tab) {
          c.style.display = 'block';
        } else {
          c.style.display = 'none';
        }
      });
    }
  }

  function filterManualCards() {
    const query = document.getElementById('manualSearchInput').value.toLowerCase().trim();
    const cards = document.querySelectorAll('.manual-card');

    cards.forEach(c => {
      const keywords = (c.getAttribute('data-keywords') || '').toLowerCase();
      const text = c.innerText.toLowerCase();
      if (text.includes(query) || keywords.includes(query)) {
        c.style.display = 'block';
      } else {
        c.style.display = 'none';
      }
    });
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
