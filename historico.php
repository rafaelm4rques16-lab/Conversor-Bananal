<?php
/**
 * Histórico de Conversões - Conversor Bananal
 */
declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';
require_once __DIR__ . '/services/StorageService.php';

$sessionId = getSessionUserId();
$usuarioId = $_SESSION['usuario']['id'] ?? null;
$pdo = getDBConnection();

// Busca histórico
$stmt = $pdo->prepare("
    SELECT id, nome_original, formato_origem, tamanho_origem, formato_destino,
           arquivo_convertido, tamanho_convertido, status, mensagem_erro,
           token_download, created_at, concluido_em
    FROM conversoes
    WHERE session_id = :sid OR (:uid IS NOT NULL AND usuario_id = :uid)
    ORDER BY id DESC
    LIMIT 100
");
$stmt->execute([':sid' => $sessionId, ':uid' => $usuarioId]);
$conversoes = $stmt->fetchAll();

// Métricas da página
$totalItens = count($conversoes);
$totalSucesso = count(array_filter($conversoes, fn($c) => $c['status'] === 'concluido'));
$totalBytes = array_sum(array_column($conversoes, 'tamanho_origem'));
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Histórico de Conversões - Conversor Bananal 🍌</title>
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🍌</text></svg>">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-900 font-sans flex flex-col justify-between">

  <!-- Header -->
  <?php include __DIR__ . '/includes/header.php'; ?>

  <main class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-10 flex-1 w-full">
    
    <!-- Topo do Histórico -->
    <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 mb-8">
      <div>
        <div class="flex items-center gap-2.5">
          <span class="text-3xl">🕒</span>
          <h1 class="text-2xl sm:text-3xl font-black text-stone-900">Histórico de Conversões</h1>
        </div>
        <p class="text-xs sm:text-sm text-stone-500 mt-1">Acompanhe e baixe novamente todos os arquivos processados na sua sessão</p>
      </div>

      <?php if ($totalItens > 0): ?>
        <button id="btn-limpar-tudo" type="button" class="px-4 py-2.5 bg-red-100 hover:bg-red-200 text-red-800 text-xs font-bold rounded-xl border border-red-300 transition flex items-center gap-2 shadow-xs">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
          <span>Limpar Histórico Completo</span>
        </button>
      <?php endif; ?>
    </div>

    <!-- Cards de Métricas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Total de Arquivos</span>
        <div class="text-2xl font-black text-stone-900 mt-1"><?= $totalItens ?></div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Taxa de Sucesso</span>
        <div class="text-2xl font-black text-emerald-600 mt-1"><?= $totalItens > 0 ? round(($totalSucesso / $totalItens) * 100) : 0 ?>%</div>
      </div>
      <div class="bg-white p-5 rounded-2xl border border-yellow-200 shadow-xs">
        <span class="text-xs font-bold uppercase tracking-wider text-stone-400">Volume Processado</span>
        <div class="text-2xl font-black text-yellow-600 mt-1"><?= StorageService::formatBytes($totalBytes) ?></div>
      </div>
    </div>

    <!-- Tabela / Lista de Conversões -->
    <div class="bg-white rounded-3xl border-2 border-yellow-200 shadow-md overflow-hidden">
      <?php if (empty($conversoes)): ?>
        <div class="text-center py-16 px-4">
          <span class="text-5xl block mb-3 animate-pulse">🍌</span>
          <h3 class="text-lg font-black text-stone-800">Nenhum arquivo no histórico</h3>
          <p class="text-xs sm:text-sm text-stone-500 max-w-sm mx-auto mt-1 mb-6">Você ainda não converteu arquivos nesta sessão. Comece agora mesmo!</p>
          <a href="index.php" class="inline-flex items-center gap-2 px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-950 font-black rounded-xl shadow-md transition text-sm">
            <span>Ir para o Conversor</span>
            <span>🍌</span>
          </a>
        </div>
      <?php else: ?>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse text-xs sm:text-sm">
            <thead>
              <tr class="bg-yellow-100/60 border-b border-yellow-200 text-stone-700 font-bold uppercase text-[11px] tracking-wider">
                <th class="py-4 px-5">Arquivo Original</th>
                <th class="py-4 px-4">Conversão</th>
                <th class="py-4 px-4">Tamanho</th>
                <th class="py-4 px-4">Status</th>
                <th class="py-4 px-4">Data/Hora</th>
                <th class="py-4 px-5 text-right">Ações</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-yellow-100 font-medium text-stone-800">
              <?php foreach ($conversoes as $item): ?>
                <tr class="hover:bg-yellow-50/40 transition">
                  <td class="py-4 px-5">
                    <div class="font-bold text-stone-900 truncate max-w-xs" title="<?= htmlspecialchars($item['nome_original']) ?>">
                      <?= htmlspecialchars($item['nome_original']) ?>
                    </div>
                  </td>
                  <td class="py-4 px-4 whitespace-nowrap">
                    <span class="px-2 py-0.5 rounded font-bold bg-yellow-200 text-yellow-950 uppercase text-xs"><?= htmlspecialchars($item['formato_origem']) ?></span>
                    <span class="mx-1 text-stone-400">➔</span>
                    <span class="px-2 py-0.5 rounded font-bold bg-emerald-100 text-emerald-900 uppercase text-xs"><?= htmlspecialchars($item['formato_destino']) ?></span>
                  </td>
                  <td class="py-4 px-4 whitespace-nowrap font-mono text-xs text-stone-600">
                    <?= StorageService::formatBytes((int)$item['tamanho_origem']) ?>
                    <?php if ($item['tamanho_convertido']): ?>
                      <span class="text-stone-400">➔</span> <?= StorageService::formatBytes((int)$item['tamanho_convertido']) ?>
                    <?php endif; ?>
                  </td>
                  <td class="py-4 px-4 whitespace-nowrap">
                    <?php if ($item['status'] === 'concluido'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-emerald-100 text-emerald-800">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Concluído
                      </span>
                    <?php elseif ($item['status'] === 'processando'): ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-yellow-100 text-yellow-800 animate-pulse">
                        Processando
                      </span>
                    <?php else: ?>
                      <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-bold bg-red-100 text-red-800" title="<?= htmlspecialchars($item['mensagem_erro'] ?? '') ?>">
                        Erro
                      </span>
                    <?php endif; ?>
                  </td>
                  <td class="py-4 px-4 whitespace-nowrap text-xs text-stone-500 font-mono">
                    <?= date('d/m/Y H:i', strtotime($item['created_at'])) ?>
                  </td>
                  <td class="py-4 px-5 text-right whitespace-nowrap space-x-1">
                    <?php if ($item['status'] === 'concluido'): ?>
                      <a href="backend/download.php?token=<?= urlencode($item['token_download']) ?>" class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-yellow-400 hover:bg-yellow-500 text-stone-900 font-bold text-xs shadow-xs transition" title="Baixar Arquivo">
                        <span>Baixar</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                      </a>
                    <?php endif; ?>
                    <button type="button" onclick="deletarItem(<?= $item['id'] ?>)" class="p-1.5 rounded-xl text-stone-400 hover:text-red-600 hover:bg-red-50 transition" title="Excluir do Histórico">
                      <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>

  </main>

  <!-- Rodapé -->
  <?php include __DIR__ . '/includes/footer.php'; ?>

  <!-- Scripts -->
  <script src="assets/js/toast.js"></script>
  <script src="assets/js/modal.js"></script>
  <script>
    async function deletarItem(id) {
      const confirmou = await BananalModal.confirm(
        'Deseja realmente excluir este arquivo do seu histórico?',
        'Excluir Item'
      );

      if (confirmou) {
        try {
          const form = new FormData();
          form.append('id', id);
          const res = await fetch('backend/history_delete.php', { method: 'POST', body: form });
          const data = await res.json();
          if (data.success) {
            BananalToast.success(data.message);
            setTimeout(() => location.reload(), 600);
          } else {
            BananalToast.error(data.error || 'Erro ao excluir.');
          }
        } catch (e) {
          BananalToast.error('Erro de conexão com o servidor.');
        }
      }
    }

    const btnLimpar = document.getElementById('btn-limpar-tudo');
    if (btnLimpar) {
      btnLimpar.addEventListener('click', async () => {
        const confirmou = await BananalModal.confirm(
          'Deseja apagar TODO o seu histórico e remover os arquivos temporários vinculados?',
          'Limpar Todo o Histórico'
        );

        if (confirmou) {
          try {
            const res = await fetch('backend/history_delete.php', { method: 'POST' });
            const data = await res.json();
            if (data.success) {
              BananalToast.success(data.message);
              setTimeout(() => location.reload(), 600);
            } else {
              BananalToast.error(data.error);
            }
          } catch (e) {
            BananalToast.error('Erro de conexão.');
          }
        }
      });
    }
  </script>
</body>
</html>
