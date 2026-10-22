<!-- Sistema de Toasts Bananal (Notificações Flutuantes Elegantes) -->
<div id="toastContainer" class="fixed top-5 right-5 z-50 flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4 sm:px-0"></div>

<script>
/**
 * Exibe um Toast na tela do usuário
 * @param {'success'|'error'|'warning'|'info'} type - Tipo do toast
 * @param {string} message - Mensagem clara do erro ou sucesso
 * @param {string} [title] - Título opcional
 * @param {number} [duration=4500] - Tempo em milissegundos
 */
function showToast(type, message, title = '', duration = 4500) {
  const container = document.getElementById('toastContainer');
  if (!container) return;

  const toastId = 'toast_' + Math.random().toString(36).substring(2, 9);
  
  const styles = {
    success: {
      bg: 'bg-emerald-50 border-emerald-400 text-emerald-900',
      icon: '✅',
      bar: 'bg-emerald-500',
      defaultTitle: 'Sucesso!'
    },
    error: {
      bg: 'bg-rose-50 border-rose-400 text-rose-900',
      icon: '❌',
      bar: 'bg-rose-500',
      defaultTitle: 'Atenção / Erro'
    },
    warning: {
      bg: 'bg-amber-50 border-banana-400 text-amber-900',
      icon: '⚠️',
      bar: 'bg-banana-500',
      defaultTitle: 'Aviso'
    },
    info: {
      bg: 'bg-sky-50 border-sky-400 text-sky-900',
      icon: '🍌',
      bar: 'bg-sky-500',
      defaultTitle: 'Bananal Informa'
    }
  };

  const current = styles[type] || styles.info;
  const displayTitle = title || current.defaultTitle;

  const toastEl = document.createElement('div');
  toastEl.id = toastId;
  toastEl.className = `pointer-events-auto transform translate-y-[-20px] opacity-0 transition-all duration-300 ease-out flex flex-col rounded-2xl border-2 shadow-xl p-4 ${current.bg}`;
  
  toastEl.innerHTML = `
    <div class="flex items-start gap-3">
      <div class="text-xl select-none shrink-0">${current.icon}</div>
      <div class="flex-grow min-w-0 pr-2">
        <h5 class="font-bold text-sm leading-tight">${displayTitle}</h5>
        <p class="text-xs mt-1 leading-relaxed opacity-90 break-words">${message}</p>
      </div>
      <button onclick="dismissToast('${toastId}')" class="text-slate-400 hover:text-slate-700 transition p-1 rounded-lg">
        ✕
      </button>
    </div>
    <div class="w-full bg-black/10 h-1 rounded-full mt-3 overflow-hidden">
      <div id="${toastId}_bar" class="h-full ${current.bar} transition-all linear" style="width: 100%;"></div>
    </div>
  `;

  container.appendChild(toastEl);

  // Animação de entrada
  setTimeout(() => {
    toastEl.classList.remove('translate-y-[-20px]', 'opacity-0');
    toastEl.classList.add('translate-y-0', 'opacity-100');
    
    // Animação da barra de tempo
    const bar = document.getElementById(`${toastId}_bar`);
    if (bar) {
      bar.style.transition = `width ${duration}ms linear`;
      bar.style.width = '0%';
    }
  }, 10);

  // Auto-remoção
  const timer = setTimeout(() => {
    dismissToast(toastId);
  }, duration);

  toastEl._dismissTimer = timer;
}

function dismissToast(id) {
  const el = document.getElementById(id);
  if (!el) return;
  if (el._dismissTimer) clearTimeout(el._dismissTimer);

  el.classList.remove('translate-y-0', 'opacity-100');
  el.classList.add('translate-y-[-10px]', 'opacity-0');
  setTimeout(() => {
    if (el.parentNode) el.parentNode.removeChild(el);
  }, 300);
}
</script>

<?php
// Exibe Toast Flash do PHP caso exista na sessão
if (isset($_SESSION['toast'])) {
    $toast = $_SESSION['toast'];
    $tipo = htmlspecialchars($toast['tipo'] ?? 'info', ENT_QUOTES, 'UTF-8');
    $mensagem = htmlspecialchars($toast['mensagem'] ?? '', ENT_QUOTES, 'UTF-8');
    unset($_SESSION['toast']);
    echo "<script>
      document.addEventListener('DOMContentLoaded', function() {
        showToast('{$tipo}', '{$mensagem}');
      });
    </script>";
}
?>
