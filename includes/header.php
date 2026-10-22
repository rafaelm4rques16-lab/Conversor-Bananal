<?php
/**
 * Conversor Bananal 🍌 - Cabeçalho Principal (Header)
 */
require_once __DIR__ . '/../config/bd.php';

$usuario_logado = usuario_autenticado();
$current_page = basename($_SERVER['PHP_SELF']);
$page_title = $page_title ?? 'Conversor Bananal 🍌 - Um conversor de arquivos nada banal!';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  
  <!-- Favicon Emoji -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220%22 0%22 100%22 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">

  <!-- Tailwind CSS CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <!-- JSZip para geração de ZIP em lote -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <!-- Lucide Icons -->
  <script src="https://unpkg.com/lucide@latest"></script>
  <!-- pdf-lib para criação/manipulação de PDF -->
  <script src="https://unpkg.com/pdf-lib@1.17.1/dist/pdf-lib.min.js"></script>

  <!-- Google Fonts: Fredoka & Inter -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fredoka:wght@400;500;600;700&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <script>
    tailwind.config = {
      darkMode: 'class',
      theme: {
        extend: {
          colors: {
            banana: {
              50: '#fffdf2',
              100: '#fef3c7',
              200: '#fde68a',
              300: '#fcd34d',
              400: '#fbbf24',
              500: '#f59e0b',
              600: '#d97706',
              700: '#b45309',
              800: '#78350f',
              900: '#451a03',
              950: '#2b0e01',
            },
            leaf: {
              400: '#4ade80',
              500: '#22c55e',
              600: '#16a34a',
              700: '#15803d',
            }
          },
          fontFamily: {
            display: ['Fredoka', 'cursive', 'sans-serif'],
            body: ['Inter', 'sans-serif']
          }
        }
      }
    }
  </script>

  <style>
    body {
      font-family: 'Inter', sans-serif;
      background-color: #fffdf5;
    }
    .font-display {
      font-family: 'Fredoka', cursive, sans-serif;
    }
    .banana-shadow {
      box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.25), 0 8px 10px -6px rgba(245, 158, 11, 0.1);
    }
    .banana-bounce {
      animation: bounce-gentle 2.5s infinite ease-in-out;
    }
    @keyframes bounce-gentle {
      0%, 100% { transform: translateY(0) rotate(0deg); }
      50% { transform: translateY(-6px) rotate(4deg); }
    }
    .blender-spin {
      animation: spin-shake 0.35s infinite ease-in-out;
    }
    @keyframes spin-shake {
      0% { transform: rotate(0deg) scale(1); }
      25% { transform: rotate(-4deg) scale(1.02); }
      50% { transform: rotate(4deg) scale(0.98); }
      75% { transform: rotate(-3deg) scale(1.01); }
      100% { transform: rotate(0deg) scale(1); }
    }
    ::-webkit-scrollbar {
      width: 8px;
      height: 8px;
    }
    ::-webkit-scrollbar-track {
      background: #fef3c7;
    }
    ::-webkit-scrollbar-thumb {
      background: #fbbf24;
      border-radius: 9999px;
    }
    ::-webkit-scrollbar-thumb:hover {
      background: #f59e0b;
    }
  </style>
</head>
<body class="min-h-screen text-slate-800 bg-[#fffdf2] flex flex-col justify-between selection:bg-banana-300 selection:text-banana-950">

  <!-- Header / Barra de Navegação -->
  <header class="bg-white/95 backdrop-blur-md sticky top-0 z-40 border-b border-banana-200 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
      
      <!-- Logotipo -->
      <a href="index.php" class="flex items-center space-x-3 group cursor-pointer">
        <div class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-banana-400 to-banana-300 flex items-center justify-center text-2xl shadow-md banana-bounce">
          🍌
        </div>
        <div>
          <div class="flex items-center gap-2">
            <span class="text-2xl font-bold font-display tracking-wide text-banana-900 group-hover:text-banana-600 transition">
              Conversor Bananal
            </span>
            <span class="text-[10px] bg-banana-200 text-banana-900 font-bold px-2 py-0.5 rounded-full border border-banana-300 uppercase tracking-wider">
              PRO
            </span>
          </div>
          <p class="text-xs text-banana-700 font-medium">Um conversor nada banal!</p>
        </div>
      </a>

      <!-- Menu Principal (Desktop) -->
      <nav class="hidden md:flex items-center bg-banana-100/70 p-1.5 rounded-2xl border border-banana-200 text-xs sm:text-sm font-bold">
        <a href="index.php" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 <?= $current_page === 'index.php' ? 'bg-white text-banana-950 shadow-sm' : 'text-banana-800 hover:text-banana-950' ?>">
          <i data-lucide="refresh-cw" class="w-4 h-4 text-banana-600"></i> Conversor
        </a>
        <a href="historico.php" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 <?= $current_page === 'historico.php' ? 'bg-white text-banana-950 shadow-sm' : 'text-banana-800 hover:text-banana-950' ?>">
          <i data-lucide="history" class="w-4 h-4 text-banana-600"></i> Histórico
        </a>
        <a href="auditoria.php" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 <?= $current_page === 'auditoria.php' ? 'bg-white text-banana-950 shadow-sm' : 'text-banana-800 hover:text-banana-950' ?>">
          <i data-lucide="shield-check" class="w-4 h-4 text-leaf-600"></i> Auditoria
        </a>
        <a href="manual.php" class="px-4 py-2 rounded-xl transition flex items-center gap-1.5 <?= $current_page === 'manual.php' ? 'bg-white text-banana-950 shadow-sm' : 'text-banana-800 hover:text-banana-950' ?>">
          <i data-lucide="book-open" class="w-4 h-4 text-banana-600"></i> Manual
        </a>
      </nav>

      <!-- Ações do Usuário (Desktop) -->
      <div class="hidden md:flex items-center gap-3">
        <?php if ($usuario_logado): ?>
          <a href="perfil.php" class="flex items-center gap-2.5 bg-banana-100/80 hover:bg-banana-200/80 px-3.5 py-1.5 rounded-2xl border border-banana-300 transition text-xs">
            <div class="w-7 h-7 rounded-full bg-banana-400 text-banana-950 font-bold flex items-center justify-center text-xs shadow-inner">
              <?= mb_substr($usuario_logado['nome'], 0, 1, 'UTF-8') ?>
            </div>
            <div class="text-left">
              <span class="block font-bold text-banana-950 truncate max-w-[120px]"><?= htmlspecialchars($usuario_logado['nome']) ?></span>
              <span class="block text-[10px] text-banana-700">Meu Perfil</span>
            </div>
          </a>
          <button onclick="confirmarLogout()" class="p-2 rounded-xl text-slate-500 hover:text-rose-600 hover:bg-rose-50 transition" title="Sair da Conta">
            <i data-lucide="log-out" class="w-5 h-5"></i>
          </button>
        <?php else: ?>
          <a href="login.php" class="px-4 py-2 text-xs font-bold text-banana-900 hover:bg-banana-100 rounded-xl transition">
            Entrar
          </a>
          <a href="registro.php" class="px-4 py-2 text-xs font-bold text-banana-950 bg-banana-400 hover:bg-banana-500 rounded-xl shadow-sm transition flex items-center gap-1">
            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Cadastrar
          </a>
        <?php endif; ?>
      </div>

      <!-- Botão Hambúrguer Mobile -->
      <div class="flex md:hidden items-center gap-2">
        <?php if ($usuario_logado): ?>
          <a href="perfil.php" class="w-8 h-8 rounded-full bg-banana-300 text-banana-900 font-bold flex items-center justify-center text-xs">
            <?= mb_substr($usuario_logado['nome'], 0, 1, 'UTF-8') ?>
          </a>
        <?php endif; ?>
        <button onclick="toggleMobileMenu()" class="p-2 rounded-xl text-banana-900 bg-banana-100 hover:bg-banana-200 transition">
          <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
      </div>

    </div>

    <!-- Menu Dropdown Mobile -->
    <div id="mobileMenu" class="hidden md:hidden border-t border-banana-200 bg-white px-4 py-4 space-y-2 shadow-lg">
      <a href="index.php" class="flex items-center gap-3 p-2.5 rounded-xl text-sm font-bold <?= $current_page === 'index.php' ? 'bg-banana-100 text-banana-900' : 'text-slate-700' ?>">
        <i data-lucide="refresh-cw" class="w-4 h-4 text-banana-600"></i> Conversor
      </a>
      <a href="historico.php" class="flex items-center gap-3 p-2.5 rounded-xl text-sm font-bold <?= $current_page === 'historico.php' ? 'bg-banana-100 text-banana-900' : 'text-slate-700' ?>">
        <i data-lucide="history" class="w-4 h-4 text-banana-600"></i> Histórico
      </a>
      <a href="auditoria.php" class="flex items-center gap-3 p-2.5 rounded-xl text-sm font-bold <?= $current_page === 'auditoria.php' ? 'bg-banana-100 text-banana-900' : 'text-slate-700' ?>">
        <i data-lucide="shield-check" class="w-4 h-4 text-leaf-600"></i> Auditoria
      </a>
      <a href="manual.php" class="flex items-center gap-3 p-2.5 rounded-xl text-sm font-bold <?= $current_page === 'manual.php' ? 'bg-banana-100 text-banana-900' : 'text-slate-700' ?>">
        <i data-lucide="book-open" class="w-4 h-4 text-banana-600"></i> Manual & Formatos
      </a>

      <div class="pt-3 border-t border-banana-100 flex flex-col gap-2">
        <?php if ($usuario_logado): ?>
          <a href="perfil.php" class="flex items-center gap-2 p-2 rounded-xl text-sm font-bold text-banana-900 bg-banana-50">
            <i data-lucide="user" class="w-4 h-4"></i> Meu Perfil (<?= htmlspecialchars($usuario_logado['nome']) ?>)
          </a>
          <button onclick="confirmarLogout()" class="w-full text-left flex items-center gap-2 p-2 rounded-xl text-sm font-bold text-rose-600 hover:bg-rose-50">
            <i data-lucide="log-out" class="w-4 h-4"></i> Sair da Conta
          </button>
        <?php else: ?>
          <a href="login.php" class="text-center p-2.5 rounded-xl text-sm font-bold bg-banana-100 text-banana-900">
            Entrar
          </a>
          <a href="registro.php" class="text-center p-2.5 rounded-xl text-sm font-bold bg-banana-400 text-banana-950">
            Criar Minha Conta
          </a>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <!-- Inclui os sistemas obrigatórios de Toasts e Modais -->
  <?php require_once __DIR__ . '/toasts.php'; ?>
  <?php require_once __DIR__ . '/modals.php'; ?>

  <script>
    function toggleMobileMenu() {
      const menu = document.getElementById('mobileMenu');
      menu.classList.toggle('hidden');
    }

    async function confirmarLogout() {
      const confirmou = await bananalConfirm(
        'Deseja sair da conta?',
        'Você será desconectado da sua sessão no Conversor Bananal.',
        'Sim, Desconectar',
        'Continuar Conectado'
      );
      if (confirmou) {
        window.location.href = 'backend/auth_logout.php';
      }
    }
  </script>
