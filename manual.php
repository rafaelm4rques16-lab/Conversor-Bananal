<?php
/**
 * Manual de Uso, Guia de Formatos e FAQ - Conversor Bananal
 */
declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manual do Bananal - Guia de Formatos & FAQ 🍌</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-900 font-sans flex flex-col justify-between">

  <!-- Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full space-y-12">
    
    <!-- Hero do Manual -->
    <div class="text-center max-w-2xl mx-auto">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-yellow-200 text-yellow-950 text-xs font-bold mb-3">
        <span>📖</span> Manual de Instruções & Guia
      </div>
      <h1 class="text-3xl sm:text-4xl font-black text-stone-900">Como Aproveitar ao Máximo o Bananal</h1>
      <p class="text-xs sm:text-sm text-stone-600 mt-2">
        Aprenda como funcionam as conversões, veja comparativos de formato e tire suas dúvidas mais comuns.
      </p>
    </div>

    <!-- Seção 1: Matriz de Formatos e Comparativo -->
    <section class="bg-white rounded-3xl border-2 border-yellow-200 p-6 sm:p-8 shadow-sm">
      <h2 class="text-xl font-black text-stone-900 mb-6 flex items-center gap-2">
        <span>📊</span>
        <span>Comparativo de Formatos de Imagem</span>
      </h2>

      <div class="overflow-x-auto">
        <table class="w-full text-left text-xs sm:text-sm border-collapse">
          <thead>
            <tr class="bg-yellow-100/70 border-b border-yellow-200 text-stone-800 font-bold uppercase text-[11px]">
              <th class="py-3 px-4">Formato</th>
              <th class="py-3 px-4">Transparência</th>
              <th class="py-3 px-4">Compressão</th>
              <th class="py-3 px-4">Melhor Uso</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-yellow-100 text-stone-700">
            <tr>
              <td class="py-3 px-4 font-bold text-stone-900">WEBP</td>
              <td class="py-3 px-4 text-emerald-700 font-semibold">Sim (Alfa)</td>
              <td class="py-3 px-4">Muito Alta (até 30% menor que JPEG)</td>
              <td class="py-3 px-4">Sites modernos, web apps e economia de banda.</td>
            </tr>
            <tr>
              <td class="py-3 px-4 font-bold text-stone-900">PNG</td>
              <td class="py-3 px-4 text-emerald-700 font-semibold">Sim (Alfa Total)</td>
              <td class="py-3 px-4">Sem perda (Lossless)</td>
              <td class="py-3 px-4">Logotipos, ilustrações, capturas de tela e ícones.</td>
            </tr>
            <tr>
              <td class="py-3 px-4 font-bold text-stone-900">JPEG / JPG</td>
              <td class="py-3 px-4 text-red-600 font-semibold">Não (fundo branco)</td>
              <td class="py-3 px-4">Com perda ajustável</td>
              <td class="py-3 px-4">Fotografias reais e imagens ricas em degradês.</td>
            </tr>
            <tr>
              <td class="py-3 px-4 font-bold text-stone-900">PDF</td>
              <td class="py-3 px-4 text-stone-500">Documento</td>
              <td class="py-3 px-4">Embutimento direto</td>
              <td class="py-3 px-4">Impressão, relatórios e envios formais.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>

    <!-- Seção 2: Perguntas Frequentes (FAQ) -->
    <section class="space-y-4">
      <h2 class="text-xl font-black text-stone-900 text-center mb-6">Perguntas Frequentes (FAQ) 🍌</h2>

      <div class="bg-white rounded-2xl border border-yellow-200 p-5 shadow-xs">
        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Meus arquivos ficam salvos para sempre no servidor?</h4>
        <p class="text-xs sm:text-sm text-stone-600 mt-1.5 leading-relaxed">
          Não! Prezamos pela privacidade total. Os arquivos temporários são excluídos automaticamente a cada 2 horas por rotinas de limpeza programadas no <code class="bg-yellow-100 text-yellow-900 px-1.5 py-0.5 rounded text-xs">StorageService.php</code>.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-yellow-200 p-5 shadow-xs">
        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Como funciona a conversão de CSV para JSON ou HTML?</h4>
        <p class="text-xs sm:text-sm text-stone-600 mt-1.5 leading-relaxed">
          O motor detecta automaticamente se o delimitador da planilha é vírgula ou ponto-e-vírgula, lê a primeira linha como cabeçalho de atributos e mapeia cada linha em objetos JSON ou linhas de tabela HTML formatada.
        </p>
      </div>

      <div class="bg-white rounded-2xl border border-yellow-200 p-5 shadow-xs">
        <h4 class="font-bold text-stone-900 text-sm sm:text-base">Como funciona o fallback para SQLite?</h4>
        <p class="text-xs sm:text-sm text-stone-600 mt-1.5 leading-relaxed">
          Caso seu servidor MySQL não esteja inicializado no momento, o arquivo <code class="bg-yellow-100 text-yellow-900 px-1.5 py-0.5 rounded text-xs">bd.php</code> automaticamente cria e utiliza um banco de dados local em SQLite (<code class="text-xs">database/bananal.sqlite</code>), permitindo que você teste e utilize a aplicação imediatamente sem erros de tela.
        </p>
      </div>
    </section>

    <!-- Chamada para Ação -->
    <div class="text-center py-6">
      <a href="index.php" class="inline-flex items-center gap-2 px-8 py-3.5 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-2xl shadow-md transition transform active:scale-95 text-sm sm:text-base">
        <span>Começar a Converter Agora</span>
        <span>🍌</span>
      </a>
    </div>

  </main>

  <!-- Rodapé -->
  <?php include __DIR__ . '/includes/footer.php'; ?>
</body>
</html>
