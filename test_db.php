<?php
/**
 * Diagnóstico e Inicialização do Banco de Dados MySQL
 * Conversor Bananal
 */

declare(strict_types=1);
define('BANANAL_ACCESS', true);

require_once __DIR__ . '/backend/auth_session.php';
require_once __DIR__ . '/config/bd.php';

$status = [
    'connected'  => false,
    'db_created' => false,
    'tables_ok'  => false,
    'message'    => '',
    'error'      => null
];

$action = $_POST['action'] ?? '';

// Inicialização automática das tabelas se solicitado ou se não existirem
try {
    $pdo = getDBConnection();
    $status['connected'] = true;

    if ($action === 'init_schema' || isset($_GET['auto_init'])) {
        $sqlFile = __DIR__ . '/database/schema.sql';
        if (file_exists($sqlFile)) {
            $sql = file_get_contents($sqlFile);
            $pdo->exec($sql);
            $status['message'] = "Tabelas e tipos suportados sincronizados com sucesso!";
        }
    }

    // Verifica se as tabelas existem
    $stmtTables = $pdo->query("SHOW TABLES LIKE 'conversoes'");
    $status['tables_ok'] = ($stmtTables->rowCount() > 0);

} catch (Throwable $e) {
    $status['error'] = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Diagnóstico MySQL - Conversor Bananal</title>
  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="assets/css/custom.css">
</head>
<body class="bg-yellow-50/50 min-h-screen text-stone-800 font-sans flex flex-col justify-between">

  <?php include __DIR__ . '/views/partials/header.php'; ?>

  <main class="max-w-3xl mx-auto px-4 py-12 flex-1 w-full">
    <div class="bg-white rounded-3xl border-2 border-yellow-300 shadow-xl p-6 sm:p-10">
      <div class="flex items-center gap-4 mb-6">
        <div class="w-14 h-14 rounded-2xl bg-yellow-400 flex items-center justify-center text-3xl shadow-md select-none">
          ⚙️
        </div>
        <div>
          <h1 class="text-2xl font-black text-stone-900">Diagnóstico de Banco de Dados</h1>
          <p class="text-sm text-stone-500">Conexão MySQL com PDO e Verificação de Tabelas</p>
        </div>
      </div>

      <?php if (!empty($status['message'])): ?>
        <div class="mb-6 p-4 rounded-2xl bg-emerald-50 border border-emerald-300 text-emerald-900 text-sm font-semibold flex items-center gap-3">
          <span class="text-xl">✅</span>
          <span><?= htmlspecialchars($status['message']) ?></span>
        </div>
      <?php endif; ?>

      <?php if ($status['error']): ?>
        <div class="mb-6 p-4 rounded-2xl bg-red-50 border border-red-300 text-red-900 text-sm font-semibold flex items-start gap-3">
          <span class="text-xl">❌</span>
          <div>
            <div class="font-bold">Falha ao Conectar com o MySQL:</div>
            <div class="text-xs font-mono mt-1 opacity-80"><?= htmlspecialchars($status['error']) ?></div>
            <div class="text-xs font-normal mt-2 text-red-800">
              Certifique-se de que o servidor MySQL (XAMPP / WAMP / Docker) esteja em execução na porta <strong><?= DB_PORT ?></strong> com o usuário <strong><?= DB_USER ?></strong>.
            </div>
          </div>
        </div>
      <?php endif; ?>

      <!-- Status Cards -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
        <div class="p-4 rounded-2xl border <?= $status['connected'] ? 'bg-yellow-50/60 border-yellow-300' : 'bg-red-50 border-red-200' ?>">
          <div class="text-xs font-bold text-stone-500 uppercase tracking-wider">Conexão PDO</div>
          <div class="text-lg font-black mt-1 flex items-center gap-2 <?= $status['connected'] ? 'text-emerald-700' : 'text-red-700' ?>">
            <span><?= $status['connected'] ? '● Ativa e Conectada' : '● Desconectado' ?></span>
          </div>
          <p class="text-xs text-stone-500 mt-1 font-mono">Host: <?= DB_HOST ?>:<?= DB_PORT ?></p>
        </div>

        <div class="p-4 rounded-2xl border <?= $status['tables_ok'] ? 'bg-yellow-50/60 border-yellow-300' : 'bg-amber-50 border-amber-200' ?>">
          <div class="text-xs font-bold text-stone-500 uppercase tracking-wider">Tabelas do Sistema</div>
          <div class="text-lg font-black mt-1 flex items-center gap-2 <?= $status['tables_ok'] ? 'text-emerald-700' : 'text-amber-700' ?>">
            <span><?= $status['tables_ok'] ? '● Tabelas Criadas' : '● Tabelas Pendentes' ?></span>
          </div>
          <p class="text-xs text-stone-500 mt-1 font-mono">Banco: <?= DB_NAME ?></p>
        </div>
      </div>

      <!-- Ações -->
      <div class="flex flex-col sm:flex-row gap-3 pt-4 border-t border-yellow-100">
        <?php if ($status['connected']): ?>
          <form method="POST" class="w-full sm:w-auto">
            <input type="hidden" name="action" value="init_schema">
            <button type="submit" class="w-full sm:w-auto px-6 py-3 bg-yellow-400 hover:bg-yellow-500 text-stone-900 font-black rounded-xl shadow-md transition transform active:scale-95 flex items-center justify-center gap-2">
              <span>🍌</span>
              <span><?= $status['tables_ok'] ? 'Recarregar Estrutura SQL' : 'Criar Tabelas Automaticamente' ?></span>
            </button>
          </form>
        <?php endif; ?>

        <a href="index.php" class="w-full sm:w-auto px-6 py-3 bg-stone-900 hover:bg-stone-800 text-yellow-300 font-bold rounded-xl text-center shadow-md transition">
          Ir para o Conversor Bananal →
        </a>
      </div>
    </div>
  </main>

  <?php include __DIR__ . '/views/partials/footer.php'; ?>
</body>
</html>
