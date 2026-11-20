/**
 * Sistema de Notificações Toast - Conversor Bananal Pro
 */
const BananalToast = (function () {
  function getContainer() {
    let c = document.getElementById('bananal-toast-container');
    if (!c) {
      c = document.createElement('div');
      c.id = 'bananal-toast-container';
      c.className = 'fixed top-5 right-5 z-50 flex flex-col gap-3 max-w-sm w-full pointer-events-none px-4 sm:px-0';
      document.body.appendChild(c);
    }
    return c;
  }

  function show(msg, type = 'info', duration = 4500) {
    const c = getContainer();
    const t = document.createElement('div');
    t.className = 'toast-anim pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-lg border text-sm font-semibold transition-all duration-300';

    let bg = 'bg-yellow-50', border = 'border-yellow-300', text = 'text-yellow-950', icon = '🍌';
    if (type === 'error') {
      bg = 'bg-red-50'; border = 'border-red-300'; text = 'text-red-950'; icon = '⚠️';
    } else if (type === 'success') {
      bg = 'bg-yellow-100'; border = 'border-yellow-400'; text = 'text-yellow-950'; icon = '🍌';
    } else if (type === 'warning') {
      bg = 'bg-amber-50'; border = 'border-amber-300'; text = 'text-amber-950'; icon = '⚡';
    }

    t.classList.add(bg, border, text);
    t.innerHTML = `
      <span class="text-xl flex-shrink-0 select-none">${icon}</span>
      <div class="flex-1 leading-snug">${msg}</div>
      <button type="button" class="text-stone-400 hover:text-stone-800 text-lg leading-none">&times;</button>
    `;

    const close = () => {
      t.style.opacity = '0';
      t.style.transform = 'translateY(-10px)';
      setTimeout(() => t.remove(), 250);
    };

    t.querySelector('button').onclick = close;
    c.appendChild(t);
    if (duration > 0) setTimeout(close, duration);
  }

  return {
    show,
    success: (m) => show(m, 'success', 4500),
    error: (m) => show(m, 'error', 6000),
    warning: (m) => show(m, 'warning', 5000),
    info: (m) => show(m, 'info', 4000)
  };
})();

window.BananalToast = BananalToast;
