<?php
/**
 * Ponto de Entrada Principal - Conversor Bananal Pro
 * Versão Expandida com Word, PowerPoint, Excel, Áudio/Vídeo para Texto, YouTube e Transcrição ao Vivo
 */

declare(strict_types=1);

require_once __DIR__ . '/app/config/database.php';

$page = $_GET['page'] ?? 'home';
$allowedPages = [
    'home', 'office', 'audio_texto', 'youtube', 'vivo',
    'legendas', 'historico', 'auditoria'
];

if (!in_array($page, $allowedPages, true)) {
    $page = 'home';
}
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conversor Bananal Pro 🍌 • Word, PPT, Áudio, YouTube & Mais</title>
  
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="public/assets/css/app.css">
</head>
<body class="bg-yellow-50/40 text-stone-900 font-sans antialiased min-h-screen flex flex-col selection:bg-yellow-300 selection:text-stone-900">

  <!-- Sidebar Estilo Convertio -->
  <?php include __DIR__ . '/app/views/layout/sidebar.php'; ?>

  <!-- Conteúdo Principal -->
  <div class="lg:pl-72 flex-1 flex flex-col min-w-0 transition-all duration-300">
    
    <!-- Topbar / Header -->
    <?php include __DIR__ . '/app/views/layout/header.php'; ?>

    <!-- Conteúdo Dinâmico da Página -->
    <main class="flex-1 max-w-6xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      <?php include __DIR__ . "/app/views/{$page}.php"; ?>
    </main>

    <!-- Rodapé -->
    <?php include __DIR__ . '/app/views/layout/footer.php'; ?>
  </div>

  <!-- Componentes de Toast e Modal -->
  <?php include __DIR__ . '/app/views/layout/modals_toasts.php'; ?>

  <!-- Scripts Globais -->
  <script src="public/assets/js/toast.js"></script>
  <script src="public/assets/js/modal.js"></script>
  <script src="public/assets/js/converter.js?v=2026"></script>

  <script>
    // Gerenciador de Sidebar (Mobile)
    const sb = document.getElementById('bananal-sidebar');
    const ov = document.getElementById('sidebar-overlay');
    const btnToggle = document.getElementById('btn-toggle-sidebar');
    const btnClose = document.getElementById('btn-close-sidebar');

    if (btnToggle && sb) {
      btnToggle.onclick = () => {
        sb.classList.remove('-translate-x-full');
        if (ov) ov.classList.remove('hidden');
      };
    }
    function closeSb() {
      if (sb) sb.classList.add('-translate-x-full');
      if (ov) ov.classList.add('hidden');
    }
    if (btnClose) btnClose.onclick = closeSb;
    if (ov) ov.onclick = closeSb;

    // Limpar Histórico
    async function handleClearHistory() {
      if (await BananalModal.confirm('Deseja apagar todo o histórico recente?', 'Limpar Histórico')) {
        const form = new FormData();
        form.append('action', 'clear_history');
        const res = await fetch('api.php', { method: 'POST', body: form });
        const data = await res.json();
        if (data.success) {
          BananalToast.success(data.message);
          if (window.refreshSidebarHistory) window.refreshSidebarHistory();
          if (location.search.includes('historico')) setTimeout(() => location.reload(), 500);
        }
      }
    }
  </script>
</body>
</html>