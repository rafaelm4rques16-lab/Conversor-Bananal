<?php
/**
 * Rodapé Global - Conversor Bananal Pro
 */
?>
<footer class="bg-stone-900 text-stone-300 border-t-4 border-yellow-400 mt-20 pt-10 pb-8">
  <div class="max-w-7xl mx-auto px-4 sm:px-6">
    <div class="grid grid-cols-1 md:grid-cols-4 gap-8 mb-8 text-xs">
      
      <div class="md:col-span-2 space-y-3">
        <div class="flex items-center gap-2">
          <span class="text-3xl select-none">🍌</span>
          <span class="text-lg font-black text-yellow-400">BANANAL PRO</span>
        </div>
        <p class="text-stone-400 leading-relaxed max-w-sm">
          A plataforma definitiva para converter arquivos de escritório (Word, PPT, Excel, PDF), transcrever áudios, vídeos e gerar legendas com o melhor estilo Bananal.
        </p>
      </div>

      <div>
        <h4 class="font-bold text-yellow-400 uppercase tracking-wider mb-2">Ferramentas</h4>
        <ul class="space-y-1.5 text-stone-400">
          <li><a href="index.php?page=office" class="hover:text-yellow-300">Word / PPT para PDF</a></li>
          <li><a href="index.php?page=audio_texto" class="hover:text-yellow-300">Áudio & Vídeo para Texto</a></li>
          <li><a href="index.php?page=youtube" class="hover:text-yellow-300">Transcrever YouTube</a></li>
          <li><a href="index.php?page=vivo" class="hover:text-yellow-300">Transcrição ao Vivo</a></li>
          <li><a href="index.php?page=legendas" class="hover:text-yellow-300">Gerador de Legendas SRT</a></li>
        </ul>
      </div>

      <div>
        <h4 class="font-bold text-yellow-400 uppercase tracking-wider mb-2">Segurança & Sistema</h4>
        <ul class="space-y-1.5 text-stone-400">
          <li><a href="index.php?page=historico" class="hover:text-yellow-300">Histórico de Sessão</a></li>
          <li><a href="index.php?page=auditoria" class="hover:text-yellow-300">Trilha de Auditoria</a></li>
          <li><span class="text-emerald-400">✔ Senhas Bcrypt Ativas</span></li>
          <li><span class="text-yellow-400">✔ PDO MySQL & SQLite Fallback</span></li>
          <li><span class="text-stone-400">Arquivos limpos a cada 2h</span></li>
        </ul>
      </div>

    </div>

    <div class="border-t border-stone-800 pt-6 text-center text-xs text-stone-500">
      &copy; <?= date('Y') ?> Conversor Bananal Pro 🍌 • Todos os direitos reservados.
    </div>
  </div>
</footer>
