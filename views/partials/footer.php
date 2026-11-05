<?php
/**
 * Rodapé Principal - Conversor Bananal
 */
if (!defined('BANANAL_ACCESS')) exit;
?>
<footer class="bg-stone-900 text-stone-300 border-t-4 border-yellow-400 mt-20 pt-12 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-12">
      <!-- Coluna 1: Branding -->
      <div class="md:col-span-2 space-y-4">
        <div class="flex items-center gap-2.5">
          <div class="w-10 h-10 rounded-2xl bg-yellow-400 flex items-center justify-center text-stone-950 font-black text-2xl shadow-sm">
            🍌
          </div>
          <div>
            <span class="text-xl font-black text-yellow-400 tracking-tight leading-none block">CONVERSOR BANANAL</span>
            <span class="text-xs text-stone-400 font-medium">A forma mais rápida e amarelada de transformar seus arquivos</span>
          </div>
        </div>
        <p class="text-stone-400 text-xs sm:text-sm leading-relaxed max-w-md">
          Inspirado na facilidade do Convertio e na estética descontraída e vibrante da Bananajobs. Seus arquivos são processados com total segurança e apagados automaticamente do servidor.
        </p>
        <div class="flex flex-wrap items-center gap-2 pt-2">
          <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-stone-800 text-yellow-400 border border-yellow-400/30">PHP Puro Estruturado</span>
          <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-stone-800 text-yellow-400 border border-yellow-400/30">MySQL + PDO Seguro</span>
          <span class="text-xs font-semibold px-2.5 py-1 rounded-md bg-stone-800 text-yellow-400 border border-yellow-400/30">Tailwind CSS MobileFirst</span>
        </div>
      </div>

      <!-- Coluna 2: Formatos de Imagem -->
      <div class="space-y-3">
        <h4 class="text-sm font-black text-yellow-400 uppercase tracking-wider">Imagens Suportadas</h4>
        <ul class="text-xs space-y-2 text-stone-400">
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> PNG para JPG, WEBP, GIF, PDF</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> JPG para PNG, WEBP, BMP, PDF</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> WEBP para JPG, PNG, GIF</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> Imagens para Base64 Data URL</li>
        </ul>
      </div>

      <!-- Coluna 3: Documentos & Dados -->
      <div class="space-y-3">
        <h4 class="text-sm font-black text-yellow-400 uppercase tracking-wider">Dados & Documentos</h4>
        <ul class="text-xs space-y-2 text-stone-400">
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> CSV para JSON, XML, HTML</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> JSON para CSV, XML, HTML</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> Markdown para HTML e PDF</li>
          <li class="flex items-center gap-2"><span class="text-yellow-400">✔</span> TXT para HTML, JSON e PDF</li>
        </ul>
      </div>
    </div>

    <!-- Divisor -->
    <div class="border-t border-stone-800 pt-6 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-stone-500">
      <p>&copy; <?= date('Y'); ?> Conversor Bananal. Desenvolvido com carinho e muitas bananas 🍌</p>
      <div class="flex items-center gap-4">
        <a href="test_db.php" class="text-yellow-400/80 hover:text-yellow-400 hover:underline">Configuração do Banco</a>
        <span>•</span>
        <a href="#bananal-dropzone" class="text-yellow-400/80 hover:text-yellow-400 hover:underline">Voltar ao Topo ↑</a>
      </div>
    </div>
  </div>
</footer>
