/**
 * Sistema de Modais Universal - Bananal Pro
 */
class BananalModal {
  static confirm(message, title = 'Aviso Bananal') {
    return new Promise((resolve) => {
      const modal = document.getElementById('bananal-custom-modal');
      const titleEl = document.getElementById('modal-title');
      const textEl = document.getElementById('modal-text');
      const btnConfirm = document.getElementById('modal-btn-confirm');
      const btnCancel = document.getElementById('modal-btn-cancel');

      if (!modal) {
        resolve(confirm(message)); // Fallback nativo caso o elemento não exista
        return;
      }

      titleEl.textContent = title;
      textEl.textContent = message;
      modal.classList.remove('hidden');

      // Limpa eventos anteriores clonando os botões
      const newConfirm = btnConfirm.cloneNode(true);
      const newCancel = btnCancel.cloneNode(true);
      btnConfirm.parentNode.replaceChild(newConfirm, btnConfirm);
      btnCancel.parentNode.replaceChild(newCancel, btnCancel);

      newConfirm.onclick = () => {
        modal.classList.add('hidden');
        resolve(true);
      };

      newCancel.onclick = () => {
        modal.classList.add('hidden');
        resolve(false);
      };
    });
  }
}
window.BananalModal = BananalModal;