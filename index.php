<?php
/**
 * Página Principal - Conversor Bananal
 * Conversor de Arquivos Multi-Formato com Estética Amarelada e Menu Lateral
 */

declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';
?>
<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Conversor Bananal - Transforme seus arquivos com facilidade 🍌</title>
  
  <!-- Meta tags e Favicon -->
  <meta name="description" content="Converta imagens, planilhas, documentos e dados em segundos. Simples, rápido e com o melhor sabor de banana.">
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <script>
    tailwind.config = {
      theme: {
        extend: {
          colors: {
            banana: {
              50: '#fefce8',
              100: '#fef9c3',
              200: '#fef08a',
              300: '#fde047',
              400: '#facc15',
              500: '#eab308',
              600: '#ca8a04',
              700: '#a16207',
              800: '#854d0e',
              900: '#713f12',
            }
          }
        }
      }
    }
  </script>

  <!-- Estilos customizados e animações da banana -->
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/40 text-stone-900 font-sans antialiased min-h-screen flex flex-col selection:bg-yellow-300 selection:text-stone-900">

  <!-- Menu Lateral (Sidebar Estilo Convertio) -->
  <?php include __DIR__ . '/views/partials/sidebar.php'; ?>

  <!-- Conteúdo Principal deslocado no desktop para acomodar a sidebar fixa -->
  <div class="lg:pl-72 sm:lg:pl-80 flex-1 flex flex-col min-w-0 transition-all duration-300">
    
    <!-- Topbar / Header -->
    <?php include __DIR__ . '/views/partials/header.php'; ?>

    <main class="flex-1 max-w-5xl w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
      
      <!-- =================================================================== -->
      <!-- Hero Section com Identidade BananaJobs / Convertio                 -->
      <!-- =================================================================== -->
      <section class="text-center mb-10 sm:mb-14">
        <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-yellow-200/80 border border-yellow-400 text-yellow-950 text-xs sm:text-sm font-black mb-4 shadow-xs">
          <span>🍌</span>
          <span>Descascador Universal de Arquivos Online</span>
        </div>

        <h1 class="text-3xl sm:text-5xl lg:text-6xl font-black text-stone-900 tracking-tight leading-tight mb-4">
          Converta seus arquivos com o sabor do <span class="bg-gradient-to-r from-yellow-400 to-amber-500 bg-clip-text text-transparent underline decoration-yellow-400/80">Bananal</span>
        </h1>

        <p class="text-sm sm:text-lg text-stone-600 max-w-2xl mx-auto leading-relaxed">
          Arraste qualquer imagem, documento ou planilha para o cacho. O sistema detecta o formato automaticamente e sugere as melhores conversões instantaneamente.
        </p>
      </section>

      <!-- =================================================================== -->
      <!-- Zona de Upload Drag & Drop Estilo Convertio                         -->
      <!-- =================================================================== -->
      <section class="mb-12">
        <div id="bananal-dropzone" class="relative group cursor-pointer bg-white hover:bg-yellow-50/60 border-3 border-dashed border-yellow-400 hover:border-yellow-500 rounded-3xl p-8 sm:p-14 text-center transition-all duration-300 shadow-sm hover:shadow-xl">
          
          <!-- Input nativo oculto acionado por clique ou drop -->
          <input id="bananal-file-input" type="file" multiple class="hidden" />

          <!-- Elementos gráficos decorativos de fundo -->
          <div class="absolute -top-4 -right-4 w-12 h-12 bg-yellow-300 rounded-2xl flex items-center justify-center text-2xl shadow-md rotate-12 group-hover:rotate-45 transition transform pointer-events-none select-none">
            🍌
          </div>

          <div class="max-w-md mx-auto space-y-4 pointer-events-none">
            <!-- Ícone de Upload estilizado -->
            <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto rounded-3xl bg-yellow-100 group-hover:bg-yellow-200 border-2 border-yellow-300 flex items-center justify-center text-4xl sm:text-5xl shadow-inner transition transform group-hover:scale-105 select-none">
              <img src="assets/img/banana-mascot.svg" alt="Banana Mascote" class="w-16 h-16 pointer-events-none" />
            </div>

            <div class="space-y-1">
              <h3 class="text-lg sm:text-xl font-black text-stone-900">
                Arraste e solte seus arquivos aqui
              </h3>
              <p class="text-xs sm:text-sm text-stone-500">
                ou utilize o botão abaixo para navegar no seu computador
              </p>
            </div>

            <!-- Botão de Ação Primária -->
            <div class="pt-2 pointer-events-auto">
              <button type="button" onclick="document.getElementById('bananal-file-input').click();" class="inline-flex items-center gap-2.5 bg-yellow-400 hover:bg-yellow-500 text-stone-900 text-sm sm:text-base font-black px-7 py-3.5 rounded-2xl shadow-md hover:shadow-lg transition transform active:scale-95">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                <span>Selecionar Arquivos</span>
              </button>
            </div>

            <!-- Dicas de Formatos Suportados -->
            <div class="text-[11px] text-stone-400 font-medium pt-2">
              Suporta PNG, JPG, WEBP, GIF, BMP, CSV, JSON, XML, MD, TXT e mais.
            </div>
          </div>
        </div>
      </section>

      <!-- =================================================================== -->
      <!-- Fila de Arquivos Selecionados / Detecção de Formato                 -->
      <!-- =================================================================== -->
      <section id="bananal-file-list" class="mb-14 hidden">
        <div class="flex items-center justify-between mb-4">
          <div class="flex items-center gap-2">
            <span class="text-xl">🍌</span>
            <h3 class="text-lg font-black text-stone-900">Arquivos no Cacho</h3>
          </div>
          <button id="btn-reset-files" type="button" class="text-xs font-bold text-stone-500 hover:text-red-600 transition flex items-center gap-1">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
            <span>Limpar Todos</span>
          </button>
        </div>

        <!-- Lista dinâmica onde o app.js renderiza os cards -->
        <div id="files-container" class="space-y-3 mb-6"></div>

        <!-- Ação Global de Conversão -->
        <div class="bg-yellow-200/50 border border-yellow-300 rounded-2xl p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
          <div class="text-xs sm:text-sm text-stone-700 font-medium flex items-center gap-2">
            <span class="text-lg">💡</span>
            <span>Você pode alterar o formato de destino de cada arquivo antes de converter.</span>
          </div>

          <button id="btn-start-convert" type="button" class="w-full sm:w-auto inline-flex items-center justify-center gap-2 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-black px-8 py-3.5 rounded-xl shadow-md transition transform active:scale-95 text-sm sm:text-base">
            <span>🍌 Descascar & Converter</span>
            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
          </button>
        </div>
      </section>

      <!-- =================================================================== -->
      <!-- Seção Informativa: Como Funciona (Estilo BananaJobs)                -->
      <!-- =================================================================== -->
      <section id="como-funciona" class="my-16">
        <div class="text-center max-w-xl mx-auto mb-10">
          <h2 class="text-2xl sm:text-3xl font-black text-stone-900 tracking-tight">
            Tão simples quanto descascar uma banana
          </h2>
          <p class="text-xs sm:text-sm text-stone-500 mt-2">
            Desenvolvido para ser direto ao ponto, responsivo e seguro.
          </p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <!-- Passo 1 -->
          <div class="bg-white rounded-3xl p-6 border-2 border-yellow-200 shadow-xs hover:border-yellow-400 transition">
            <div class="w-12 h-12 rounded-2xl bg-yellow-300 text-stone-900 font-black text-xl flex items-center justify-center mb-4 shadow-sm">
              1
            </div>
            <h4 class="font-black text-stone-900 text-base mb-1.5">Envie seu Arquivo</h4>
            <p class="text-xs text-stone-600 leading-relaxed">
              Arraste e solte ou escolha qualquer imagem, planilha CSV, arquivo JSON ou documento de texto.
            </p>
          </div>

          <!-- Passo 2 -->
          <div class="bg-white rounded-3xl p-6 border-2 border-yellow-200 shadow-xs hover:border-yellow-400 transition">
            <div class="w-12 h-12 rounded-2xl bg-yellow-300 text-stone-900 font-black text-xl flex items-center justify-center mb-4 shadow-sm">
              2
            </div>
            <h4 class="font-black text-stone-900 text-base mb-1.5">Detecção Inteligente</h4>
            <p class="text-xs text-stone-600 leading-relaxed">
              O sistema analisa a extensão e o MIME type e sugere automaticamente para quais formatos você pode transformar.
            </p>
          </div>

          <!-- Passo 3 -->
          <div class="bg-white rounded-3xl p-6 border-2 border-yellow-200 shadow-xs hover:border-yellow-400 transition">
            <div class="w-12 h-12 rounded-2xl bg-yellow-300 text-stone-900 font-black text-xl flex items-center justify-center mb-4 shadow-sm">
              3
            </div>
            <h4 class="font-black text-stone-900 text-base mb-1.5">Baixe Imediatamente</h4>
            <p class="text-xs text-stone-600 leading-relaxed">
              O processamento em PHP roda no servidor e você faz o download do novo formato pronto para uso com 1 clique.
            </p>
          </div>
        </div>
      </section>

      <!-- =================================================================== -->
      <!-- Banner de Segurança e Privacidade                                   -->
      <!-- =================================================================== -->
      <section class="bg-gradient-to-r from-yellow-300 via-yellow-400 to-amber-400 rounded-3xl p-6 sm:p-8 text-stone-900 shadow-md flex flex-col sm:flex-row items-center justify-between gap-6">
        <div class="flex items-center gap-4">
          <div class="w-14 h-14 rounded-2xl bg-stone-900 text-yellow-300 flex items-center justify-center text-3xl flex-shrink-0 shadow-sm">
            🔒
          </div>
          <div>
            <h4 class="text-base sm:text-lg font-black leading-tight">Segurança Total dos Seus Arquivos</h4>
            <p class="text-xs sm:text-sm text-stone-800 mt-1 max-w-lg">
              Utilizamos proteção PDO contra injeções SQL, conexões protegidas e exclusão automática de arquivos do servidor a cada 2 horas.
            </p>
          </div>
        </div>
        <a href="test_db.php" class="whitespace-nowrap px-5 py-2.5 rounded-xl bg-stone-900 hover:bg-stone-800 text-yellow-300 text-xs font-bold transition shadow-sm">
          Verificar Banco MySQL →
        </a>
      </section>

    </main>

    <!-- Rodapé -->
    <?php include __DIR__ . '/views/partials/footer.php'; ?>

  </div>

  <!-- Scripts do Frontend (Toast, Modal e App Controller) -->
  <script src="assets/js/toast.js"></script>
  <script src="assets/js/modal.js"></script>
  <script src="assets/js/app.js"></script>
</body>
</html>
