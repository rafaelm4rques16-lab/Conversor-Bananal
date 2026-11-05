/**
 * Sistema de Notificações Toast - Conversor Bananal
 * Exibe mensagens claras, animadas e com estética amarelada
 */

const BananalToast = (function () {
  const containerId = 'bananal-toast-container';

  function getOrCreateContainer() {
    let container = document.getElementById(containerId);
    if (!container) {
      container = document.createElement('div');
      container.id = containerId;
      container.className = 'fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0';
      document.body.appendChild(container);
    }
    return container;
  }

  function show(message, type = 'info', duration = 5000) {
    const container = getOrCreateContainer();
    const toast = document.createElement('div');
    toast.className = 'toast-enter pointer-events-auto flex items-start gap-3 p-4 rounded-xl shadow-lg border text-sm font-medium transition-all duration-300';

    let config = {
      bg: 'bg-white',
      border: 'border-yellow-300',
      text: 'text-stone-800',
      icon: '🍌',
      barColor: 'bg-yellow-400'
    };

    switch (type) {
      case 'error':
        config = {
          bg: 'bg-red-50',
          border: 'border-red-300',
          text: 'text-red-900',
          icon: '⚠️',
          barColor: 'bg-red-500'
        };
        break;
      case 'success':
        config = {
          bg: 'bg-yellow-50',
          border: 'border-yellow-400',
          text: 'text-yellow-950',
          icon: '🍌',
          barColor: 'bg-yellow-500'
        };
        break;
      case 'warning':
        config = {
          bg: 'bg-amber-50',
          border: 'border-amber-400',
          text: 'text-amber-950',
          icon: '⚡',
          barColor: 'bg-amber-500'
        };
        break;
      case 'info':
      default:
        config = {
          bg: 'bg-stone-50',
          border: 'border-yellow-200',
          text: 'text-stone-800',
          icon: 'ℹ️',
          barColor: 'bg-yellow-400'
        };
        break;
    }

    toast.classList.add(config.bg, config.border, config.text);

    toast.innerHTML = `
      <div class="text-xl flex-shrink-0 select-none">${config.icon}</div>
      <div class="flex-1 leading-snug break-words">${message}</div>
      <button type="button" class="text-stone-400 hover:text-stone-700 transition flex-shrink-0 font-bold text-lg leading-none select-none" aria-label="Fechar">&times;</button>
    `;

    const closeBtn = toast.querySelector('button');
    const removeToast = () => {
      toast.style.opacity = '0';
      toast.style.transform = 'translateY(-10px)';
      setTimeout(() => {
        if (toast.parentNode) {
          toast.parentNode.removeChild(toast);
        }
      }, 300);
    };

    closeBtn.addEventListener('click', removeToast);

    container.appendChild(toast);

    if (duration > 0) {
      setTimeout(removeToast, duration);
    }
  }

  return {
    show: show,
    error: (msg, dur) => show(msg, 'error', dur || 6000),
    success: (msg, dur) => show(msg, 'success', dur || 4500),
    warning: (msg, dur) => show(msg, 'warning', dur || 5000),
    info: (msg, dur) => show(msg, 'info', dur || 4000)
  };
})();

// Disponibiliza globalmente
window.BananalToast = BananalToast;
