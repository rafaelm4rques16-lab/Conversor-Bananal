  <!-- Rodapé Bananal -->
  <footer class="bg-white border-t border-banana-200 mt-16 py-8 text-center text-xs text-slate-500">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4">
      <div class="flex items-center gap-2">
        <span class="text-2xl banana-bounce select-none">🍌</span>
        <div class="text-left">
          <p class="font-bold text-banana-950 font-display text-sm">Conversor Bananal</p>
          <p class="text-slate-400">Um conversor de arquivos nada banal!</p>
        </div>
      </div>

      <div class="flex flex-wrap items-center justify-center gap-6 font-semibold text-slate-600">
        <a href="index.php" class="hover:text-banana-700 transition">Conversor</a>
        <a href="historico.php" class="hover:text-banana-700 transition">Histórico</a>
        <a href="auditoria.php" class="hover:text-banana-700 transition">Auditoria</a>
        <a href="manual.php" class="hover:text-banana-700 transition">Manual de Formatos</a>
      </div>

      <div class="text-xs text-slate-400">
        <p>🔒 Processamento Seguro & Auditoria Ativa</p>
        <p class="text-[11px] text-banana-700 font-medium">Stack: PHP + MySQL (PDO) + Tailwind CSS</p>
      </div>
    </div>
  </footer>

  <!-- Inicialização dos Ícones Lucide -->
  <script>
    document.addEventListener('DOMContentLoaded', () => {
      if (window.lucide) {
        lucide.createIcons();
      }
    });
  </script>
</body>
</html>
