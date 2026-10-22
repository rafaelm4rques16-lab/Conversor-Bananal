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
        <span class="text-[10px] bg-amber-100 text-amber-800 font-bold px-2.5 py-1 rounded-full uppercase">Imagens / Logos</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Formato com compressão sem perdas (lossless) ideal para gráficos que exigem máxima nitidez e suporte a fundo transparente (canal Alpha).
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Logotipos, ícones, capturas de tela com texto legível e qualquer imagem que precise de transparência.
        </div>
        <div class="bg-rose-50 text-rose-900 p-2.5 rounded-xl border border-rose-100">
          <strong class="text-rose-800 block mb-0.5">❌ Contras / Quando Evitar:</strong>
          Geralmente muito mais pesado que JPG para fotos de alta resolução.
        </div>
      </div>
    </div>

    <!-- WEBP -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="image" data-keywords="webp google moderna web leve">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            WEBP
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">WebP</h3>
            <p class="text-xs text-slate-400">Padrão Moderno do Google para a Web</p>
          </div>
        </div>
        <span class="text-[10px] bg-leaf-100 text-leaf-800 font-bold px-2.5 py-1 rounded-full uppercase">Recomendado Web</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        O melhor dos dois mundos: combina a compressão potente do JPG com o suporte a transparência do PNG, sendo até 35% mais leve!
      </p>
      <div class="space-y-2 text-xs">
        <div class="bg-emerald-50 text-emerald-900 p-2.5 rounded-xl border border-emerald-100">
          <strong class="text-emerald-800 block mb-0.5">✅ Prós / Quando Usar:</strong>
          Perfeito para sites modernos, carregamento ultra-rápido e economia de banda.
        </div>
      </div>
    </div>

    <!-- SVG -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="image" data-keywords="svg vetor vetorial infinito ilustracao">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            SVG
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">SVG Vetorial</h3>
            <p class="text-xs text-slate-400">Scalable Vector Graphics</p>
          </div>
        </div>
        <span class="text-[10px] bg-purple-100 text-purple-800 font-bold px-2.5 py-1 rounded-full uppercase">Vetores</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        Gráficos baseados em equações matemáticas e XML. Permite zoom infinito sem perder qualidade ou pixelar.
      </p>
    </div>

    <!-- MP3 / WAV -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="audio" data-keywords="mp3 wav audio som musica podcast">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            🎵
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">WAV & MP3</h3>
            <p class="text-xs text-slate-400">Áudios e Trilhas Sonoras</p>
          </div>
        </div>
        <span class="text-[10px] bg-sky-100 text-sky-800 font-bold px-2.5 py-1 rounded-full uppercase">Áudio</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        WAV é áudio puro sem compressão para máxima fidelidade acústica. MP3 é comprimido para compartilhamento rápido.
      </p>
    </div>

    <!-- JSON / CSV -->
    <div class="manual-card bg-white rounded-3xl p-6 border border-banana-200 shadow-sm space-y-4" data-category="text" data-keywords="json csv dados texto planilha excel">
      <div class="flex items-center justify-between border-b border-slate-100 pb-3">
        <div class="flex items-center gap-3">
          <span class="w-10 h-10 rounded-xl bg-banana-100 text-banana-800 font-bold flex items-center justify-center font-display text-sm">
            📊
          </span>
          <div>
            <h3 class="font-bold font-display text-banana-950 text-base">JSON & CSV</h3>
            <p class="text-xs text-slate-400">Intercâmbio de Dados Estruturados</p>
          </div>
        </div>
        <span class="text-[10px] bg-emerald-100 text-emerald-800 font-bold px-2.5 py-1 rounded-full uppercase">Dados</span>
      </div>
      <p class="text-xs text-slate-600 leading-relaxed">
        CSV é o padrão universal para planilhas como Excel e Google Sheets. JSON é a estrutura nativa da web e de APIs REST.
      </p>
    </div>

  </div>

  <!-- FAQ / Perguntas Frequentes -->
  <section id="manualFaqSection" class="bg-white rounded-3xl p-6 sm:p-8 border border-banana-200 shadow-sm space-y-4">
    <div class="flex items-center gap-2 mb-4">
      <span class="text-2xl">❓</span>
      <h3 class="font-bold font-display text-xl text-banana-950">Dúvidas Frequentes (FAQ)</h3>
    </div>

    <div class="space-y-3 text-xs sm:text-sm">
      <div class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200">
        <strong class="text-banana-950 block text-sm mb-1">Como funciona a auditoria de alterações?</strong>
        <p class="text-slate-600 leading-relaxed">
          Toda alteração feita por qualquer usuário (cadastros, logins, atualizações de perfil, trocas de senha e conversões ou exclusões de arquivos) é gravada na tabela <code>logs_auditoria</code> no MySQL, registrando data/hora, IP e navegador.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200">
        <strong class="text-banana-950 block text-sm mb-1">As senhas são seguras?</strong>
        <p class="text-slate-600 leading-relaxed">
          Sim! O sistema atende estritamente à regra de negócio do CORE com hash <strong>Bcrypt</strong> (`password_hash`), garantindo proteção robusta contra vazamentos ou ataques de força bruta.
        </p>
      </div>

      <div class="p-4 rounded-2xl bg-banana-50/60 border border-banana-200">
        <strong class="text-banana-950 block text-sm mb-1">Meus arquivos são enviados para servidores externos?</strong>
        <p class="text-slate-600 leading-relaxed">
          Não! O Conversor Bananal utiliza processamento moderno direto no navegador (Canvas, Web Audio, JSZip, pdf-lib) e servidor local PHP/MySQL seguro, sem vazamento para serviços de terceiros.
        </p>
      </div>
    </div>
  </section>

</main>

<script>
  function setManualTab(category) {
    const buttons = [
      { id: 'tab-manual-all', cat: 'all' },
      { id: 'tab-manual-image', cat: 'image' },
      { id: 'tab-manual-audio', cat: 'audio' },
      { id: 'tab-manual-text', cat: 'text' },
      { id: 'tab-manual-faq', cat: 'faq' }
    ];

    buttons.forEach(b => {
      const btn = document.getElementById(b.id);
      if (b.cat === category) {
        btn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-banana-400 text-banana-950 shadow-sm transition";
      } else {
        btn.className = "px-4 py-2 rounded-xl text-xs font-bold bg-white text-slate-600 hover:bg-banana-100 transition";
      }
    });

    const cards = document.querySelectorAll('.manual-card');
    const faqSec = document.getElementById('manualFaqSection');

    if (category === 'faq') {
      cards.forEach(c => c.classList.add('hidden'));
      faqSec.classList.remove('hidden');
      return;
    }

    faqSec.classList.remove('hidden');

    cards.forEach(card => {
      if (category === 'all' || card.dataset.category === category) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  }

  function filterManualCards() {
    const query = document.getElementById('manualSearchInput').value.toLowerCase();
    const cards = document.querySelectorAll('.manual-card');

    cards.forEach(card => {
      const keywords = (card.dataset.keywords || '').toLowerCase();
      const text = card.innerText.toLowerCase();
      if (keywords.includes(query) || text.includes(query)) {
        card.classList.remove('hidden');
      } else {
        card.classList.add('hidden');
      }
    });
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
