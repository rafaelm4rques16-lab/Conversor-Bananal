  <!-- Rodapé Global -->
  <footer class="bg-white border-t border-banana-200 mt-12 py-8 text-slate-500 text-xs">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-4">
      
      <div class="flex items-center space-x-2">
        <span class="text-xl select-none">🍌</span>
        <span class="font-bold text-banana-950 font-display text-sm">Conversor Bananal</span>
        <span>— O conversor de arquivos nada banal!</span>
      </div>

      <div class="flex flex-wrap items-center gap-4 text-xs font-medium text-banana-800">
        <button onclick="toggleSidebar(true)" class="hover:underline flex items-center gap-1 font-bold">
          <i data-lucide="menu" class="w-3.5 h-3.5"></i> Conversores
        </button>
        <a href="manual.php" class="hover:underline">Manual de Formatos</a>
        <a href="historico.php" class="hover:underline">Histórico</a>
        <a href="auditoria.php" class="hover:underline">Auditoria</a>
      </div>

      <div class="text-center md:text-right text-[11px] text-slate-400">
        Desenvolvido com PHP, PDO MySQL e Tailwind CSS • ETEC / Trabalho de A.I
      </div>

    </div>
  </footer>

  <script>
    // Inicialização dos ícones Lucide
    if (window.lucide) {
      lucide.createIcons();
    }
  </script>
</body>
</html>
