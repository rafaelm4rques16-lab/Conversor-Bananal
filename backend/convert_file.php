<?php
/**
 * Processamento e Conversão de Arquivos
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/config/bd.php';
require_once dirname(__DIR__) . '/services/StorageService.php';
require_once dirname(__DIR__) . '/services/FileConverter.php';

$storage   = new StorageService();
$converter = new FileConverter();

// Limpa arquivos com mais de 2 horas em segundo plano
$storage->cleanOldFiles(7200);

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new InvalidArgumentException("Método HTTP inválido. Utilize POST.");
    }

    if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $uploadErrors = [
            UPLOAD_ERR_INI_SIZE   => "O arquivo excede o limite máximo configurado no PHP (upload_max_filesize).",
            UPLOAD_ERR_FORM_SIZE  => "O arquivo excede o limite permitido pelo formulário.",
            UPLOAD_ERR_PARTIAL    => "O upload do arquivo foi interrompido.",
            UPLOAD_ERR_NO_FILE    => "Nenhum arquivo foi enviado.",
            UPLOAD_ERR_NO_TMP_DIR => "Pasta temporária ausente no servidor.",
            UPLOAD_ERR_CANT_WRITE => "Falha ao gravar arquivo em disco.",
            UPLOAD_ERR_EXTENSION  => "Upload impedido por uma extensão do PHP."
        ];
        $code = $_FILES['file']['error'] ?? UPLOAD_ERR_NO_FILE;
        throw new RuntimeException($uploadErrors[$code] ?? "Erro desconhecido durante o upload.");
    }

    $targetFormat = strtolower(trim($_POST['target_format'] ?? ''));
    if (empty($targetFormat)) {
        throw new InvalidArgumentException("Formato de destino não selecionado. Escolha para qual tipo deseja converter.");
    }

    $originalName = $_FILES['file']['name'];
    $originalSize = (int)$_FILES['file']['size'];
    $tmpSource    = $_FILES['file']['tmp_name'];
    $sourceExt    = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));

    if (empty($sourceExt)) {
        throw new InvalidArgumentException("O arquivo enviado não possui uma extensão válida.");
    }

    if (!$converter->canConvert($sourceExt, $targetFormat)) {
        throw new InvalidArgumentException("A conversão de .{$sourceExt} para .{$targetFormat} não é suportada.");
    }

    // Move arquivo de upload para o diretório seguro de uploads
    $safeSourceFilename = $storage->generateSafeFilename($sourceExt);
    $destUploadPath     = $storage->getUploadDir() . DIRECTORY_SEPARATOR . $safeSourceFilename;

    if (!move_uploaded_file($tmpSource, $destUploadPath)) {
        throw new RuntimeException("Não foi possível armazenar o arquivo temporário no servidor.");
    }

    // Prepara arquivo de destino
    $safeConvertedFilename = $storage->generateSafeFilename($targetFormat);
    $destConvertedPath     = $storage->getConvertedDir() . DIRECTORY_SEPARATOR . $safeConvertedFilename;
    $downloadToken         = bin2hex(random_bytes(24));
    $sessionId             = getSessionUserId();
    $usuarioId             = $_SESSION['usuario']['id'] ?? null;

    // Registra início no banco via PDO
    $pdo = null;
    $conversionId = null;
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("
            INSERT INTO conversoes 
            (usuario_id, session_id, nome_original, formato_origem, tamanho_origem, formato_destino, status, token_download, created_at)
            VALUES 
            (:usuario_id, :session_id, :nome_original, :formato_origem, :tamanho_origem, :formato_destino, 'processando', :token_download, datetime('now'))
        ");
        
        // Se for MySQL, adapta para NOW()
        try {
            $stmt->execute([
                ':usuario_id'      => $usuarioId,
                ':session_id'      => $sessionId,
                ':nome_original'   => $originalName,
                ':formato_origem'  => $sourceExt,
                ':tamanho_origem'  => $originalSize,
                ':formato_destino' => $targetFormat,
                ':token_download'  => $downloadToken
            ]);
        } catch (PDOException $e) {
            $stmtMysql = $pdo->prepare("
                INSERT INTO conversoes 
                (usuario_id, session_id, nome_original, formato_origem, tamanho_origem, formato_destino, status, token_download, created_at)
                VALUES 
                (:usuario_id, :session_id, :nome_original, :formato_origem, :tamanho_origem, :formato_destino, 'processando', :token_download, NOW())
            ");
            $stmtMysql->execute([
                ':usuario_id'      => $usuarioId,
                ':session_id'      => $sessionId,
                ':nome_original'   => $originalName,
                ':formato_origem'  => $sourceExt,
                ':tamanho_origem'  => $originalSize,
                ':formato_destino' => $targetFormat,
                ':token_download'  => $downloadToken
            ]);
        }
        $conversionId = (int)$pdo->lastInsertId();
    } catch (Throwable $e) {
        error_log("[Conversor Bananal] Erro ao registrar início da conversão no BD: " . $e->getMessage());
    }

    // Executa a conversão propriamente dita
    try {
        $converter->convert($destUploadPath, $destConvertedPath, $sourceExt, $targetFormat);

        if (!file_exists($destConvertedPath)) {
            throw new RuntimeException("O arquivo de destino não pôde ser gerado em disco.");
        }

        $convertedSize = (int)filesize($destConvertedPath);

        // Atualiza status para 'concluido' no banco
        if ($pdo && $conversionId) {
            $stmtUpdate = $pdo->prepare("
                UPDATE conversoes 
                SET status = 'concluido',
                    arquivo_convertido = :arquivo,
                    tamanho_convertido = :tamanho,
                    concluido_em = datetime('now')
                WHERE id = :id
            ");
            try {
                $stmtUpdate->execute([
                    ':arquivo' => $safeConvertedFilename,
                    ':tamanho' => $convertedSize,
                    ':id'      => $conversionId
                ]);
            } catch (PDOException $e) {
                $stmtUpdateMysql = $pdo->prepare("
                    UPDATE conversoes 
                    SET status = 'concluido',
                        arquivo_convertido = :arquivo,
                        tamanho_convertido = :tamanho,
                        concluido_em = NOW()
                    WHERE id = :id
                ");
                $stmtUpdateMysql->execute([
                    ':arquivo' => $safeConvertedFilename,
                    ':tamanho' => $convertedSize,
                    ':id'      => $conversionId
                ]);
            }

            registrarAuditoria('conversao_sucesso', "Conversão concluída: {$originalName} ({$sourceExt}) -> {$targetFormat}", $usuarioId ? (int)$usuarioId : null);
        }

        // Gera nome sugerido amigável para download (ex: meu_arquivo.png -> meu_arquivo.webp)
        $baseOriginalName = pathinfo($originalName, PATHINFO_FILENAME);
        $suggestedName    = $baseOriginalName . '.' . $targetFormat;

        echo json_encode([
            'success'                  => true,
            'message'                  => "🍌 Conversão realizada com sucesso!",
            'token'                    => $downloadToken,
            'download_url'             => 'backend/download.php?token=' . urlencode($downloadToken),
            'filename_original'        => $originalName,
            'filename_converted'       => $suggestedName,
            'format_original'          => strtoupper($sourceExt),
            'format_target'            => strtoupper($targetFormat),
            'size_original_formatted'  => StorageService::formatBytes($originalSize),
            'size_converted_formatted' => StorageService::formatBytes($convertedSize),
        ], JSON_UNESCAPED_UNICODE);

    } catch (Throwable $convError) {
        // Atualiza status de erro no banco
        if ($pdo && $conversionId) {
            $stmtErr = $pdo->prepare("
                UPDATE conversoes 
                SET status = 'erro',
                    mensagem_erro = :msg
                WHERE id = :id
            ");
            $stmtErr->execute([
                ':msg' => $convError->getMessage(),
                ':id'  => $conversionId
            ]);
        }
        throw $convError;
    }

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
