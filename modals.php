<!-- Sistema Universal de Modais Bananal (Substitui caixas de mensagens nativas alert e confirm) -->

<!-- 1. Modal Universal de Alerta e Confirmação -->
<div id="bananalModalWrapper" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-opacity duration-300">
  <div id="bananalModalCard" class="bg-white rounded-3xl p-6 sm:p-8 max-w-md w-full shadow-2xl border-2 border-banana-200 transform scale-95 opacity-0 transition-all duration-300">
    <div class="flex items-center gap-3 mb-4">
      <div id="bananalModalIcon" class="w-12 h-12 rounded-2xl bg-banana-100 flex items-center justify-center text-2xl shrink-0">
        🍌
      </div>
      <div>
        <h3 id="bananalModalTitle" class="text-xl font-bold font-display text-banana-950">
          Mensagem Bananal
        </h3>
        <p id="bananalModalSubtitle" class="text-xs text-slate-400">Notificação do sistema</p>
      </div>
    </div>

    <div id="bananalModalMessage" class="text-slate-600 text-sm leading-relaxed mb-6 whitespace-pre-line"></div>

    <div id="bananalModalButtons" class="flex items-center justify-end gap-3 pt-2">
      <button id="bananalModalCancelBtn" class="hidden px-5 py-2.5 rounded-xl text-slate-600 hover:bg-slate-100 text-sm font-semibold transition">
        Cancelar
      </button>
      <button id="bananalModalConfirmBtn" class="px-6 py-2.5 rounded-xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-sm shadow-md transition">
        OK, Entendi
      </button>
    </div>
  </div>
</div>

<!-- 2. Modal de Pré-visualização de Arquivos -->
<div id="previewModal" class="fixed inset-0 z-50 hidden flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-sm">
  <div class="bg-white rounded-3xl p-6 max-w-2xl w-full max-h-[90vh] flex flex-col shadow-2xl border-2 border-banana-200">
    <div class="flex items-center justify-between border-b border-banana-100 pb-3 mb-4">
      <div class="flex items-center gap-2">
        <span class="text-2xl">👀</span>
        <h3 id="previewTitle" class="font-bold text-lg font-display text-banana-950 truncate max-w-md">Pré-visualização</h3>
      </div>
      <button onclick="closePreviewModal()" class="text-slate-400 hover:text-slate-700 p-2 rounded-xl hover:bg-slate-100 text-xl font-bold">
        ✕
      </button>
    </div>

    <div id="previewContent" class="flex-grow overflow-auto flex items-center justify-center p-2 min-h-[220px] bg-banana-50/40 rounded-2xl border border-banana-100">
      <!-- Conteúdo inserido dinamicamente via JS -->
    </div>

    <div class="flex justify-end gap-3 mt-4 pt-3 border-t border-slate-100">
      <button onclick="closePreviewModal()" class="px-5 py-2.5 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-bold transition">
        Fechar
      </button>
    </div>
  </div>
</div>

<script>
/**
 * Gerenciador universal dos modais de alerta e confirmação
 */
let bananalModalResolver = null;

function showModalMessage({ title, message, icon = '🍌', isConfirm = false, confirmText = 'OK, Entendi', cancelText = 'Cancelar', type = 'info' }) {
  return new Promise((resolve) => {
    bananalModalResolver = resolve;

    const wrapper = document.getElementById('bananalModalWrapper');
    const card = document.getElementById('bananalModalCard');
    const titleEl = document.getElementById('bananalModalTitle');
    const msgEl = document.getElementById('bananalModalMessage');
    const iconEl = document.getElementById('bananalModalIcon');
    const confirmBtn = document.getElementById('bananalModalConfirmBtn');
    const cancelBtn = document.getElementById('bananalModalCancelBtn');

    titleEl.innerText = title;
    msgEl.innerHTML = message;
    iconEl.innerText = icon;

    confirmBtn.innerText = confirmText;
    cancelBtn.innerText = cancelText;

    if (type === 'danger') {
      confirmBtn.className = "px-6 py-2.5 rounded-xl bg-rose-500 hover:bg-rose-600 text-white font-bold text-sm shadow-md transition";
    } else {
      confirmBtn.className = "px-6 py-2.5 rounded-xl bg-banana-400 hover:bg-banana-500 text-banana-950 font-bold text-sm shadow-md transition";
    }

    if (isConfirm) {
      cancelBtn.classList.remove('hidden');
    } else {
      cancelBtn.classList.add('hidden');
    }

    // Configura botões
    confirmBtn.onclick = () => {
      closeBananalModal();
      if (bananalModalResolver) bananalModalResolver(true);
    };

    cancelBtn.onclick = () => {
      closeBananalModal();
      if (bananalModalResolver) bananalModalResolver(false);
    };

    // Abre modal com transição suave
    wrapper.classList.remove('hidden');
    setTimeout(() => {
      card.classList.remove('scale-95', 'opacity-0');
      card.classList.add('scale-100', 'opacity-100');
    }, 10);
  });
}

function closeBananalModal() {
  const wrapper = document.getElementById('bananalModalWrapper');
  const card = document.getElementById('bananalModalCard');
  if (!wrapper || !card) return;

  card.classList.remove('scale-100', 'opacity-100');
  card.classList.add('scale-95', 'opacity-0');
  setTimeout(() => {
    wrapper.classList.add('hidden');
  }, 200);
}

/**
 * Substitutos modernos e elegantes para alert() e confirm()
 */
window.bananalAlert = function(title, message, icon = '🍌') {
  return showModalMessage({ title, message, icon, isConfirm: false });
};

window.bananalConfirm = function(title, message, confirmText = 'Confirmar', cancelText = 'Cancelar') {
  return showModalMessage({ title, message, icon: '🤔', isConfirm: true, confirmText, cancelText });
};

// Modal de Pré-visualização
function openPreviewModal(title, elementHTML) {
  const modal = document.getElementById('previewModal');
  const titleEl = document.getElementById('previewTitle');
  const contentEl = document.getElementById('previewContent');

  titleEl.innerText = title;
  contentEl.innerHTML = elementHTML;
  modal.classList.remove('hidden');
}

function closePreviewModal() {
  const modal = document.getElementById('previewModal');
  if (modal) modal.classList.add('hidden');
}
</script>
