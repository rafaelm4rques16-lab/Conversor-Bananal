<?php
/**
 * Detecção de Tipo de Arquivo e Sugestões de Conversão
 * Conversor Bananal
 */

declare(strict_types=1);

header('Content-Type: application/json; charset=UTF-8');

require_once __DIR__ . '/auth_session.php';
require_once dirname(__DIR__) . '/services/FileConverter.php';
require_once dirname(__DIR__) . '/config/bd.php';

try {
    $filename = $_GET['filename'] ?? $_POST['filename'] ?? '';

    // Se um arquivo foi enviado temporariamente via upload direto
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $filename = $_FILES['file']['name'];
        $tmpPath = $_FILES['file']['tmp_name'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $detectedMime = $finfo->file($tmpPath) ?: 'application/octet-stream';
    } else {
        $detectedMime = null;
    }

    if (empty($filename)) {
        throw new InvalidArgumentException("Nenhum nome de arquivo informado para detecção.");
    }

    $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
    if (empty($extension)) {
        throw new InvalidArgumentException("O arquivo selecionado não possui extensão legível.");
    }

    $converter = new FileConverter();
    $suggestions = $converter->getSuggestions($extension);

    // Consulta no banco MySQL via PDO para detalhes e dados cadastrados
    $pdo = null;
    $dbData = null;
    try {
        $pdo = getDBConnection();
        $stmt = $pdo->prepare("SELECT categoria, nome_legivel, conversoes_sugeridas, mime_type FROM tipos_suportados WHERE extensao = :ext LIMIT 1");
        $stmt->execute([':ext' => $extension]);
        $dbData = $stmt->fetch();
    } catch (Throwable $e) {
        // Fallback gracioso caso o banco de dados ainda não tenha sido iniciado
        error_log("[Conversor Bananal] Aviso BD detect_format: " . $e->getMessage());
    }

    $category = $dbData['categoria'] ?? match(true) {
        in_array($extension, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp', 'svg']) => 'imagem',
        in_array($extension, ['csv', 'json', 'xml']) => 'dados',
        in_array($extension, ['txt', 'md', 'html']) => 'documento',
        in_array($extension, ['mp3', 'wav', 'ogg']) => 'audio',
        default => 'desconhecido'
    };

    $readableName = $dbData['nome_legivel'] ?? strtoupper($extension) . " File";
    $mimeType = $detectedMime ?? ($dbData['mime_type'] ?? 'application/octet-stream');

    if (empty($suggestions) && !empty($dbData['conversoes_sugeridas'])) {
        $suggestions = array_filter(array_map('trim', explode(',', $dbData['conversoes_sugeridas'])));
    }

    echo json_encode([
        'success'            => true,
        'filename'           => htmlspecialchars($filename),
        'extension'          => $extension,
        'mime_type'          => $mimeType,
        'category'           => $category,
        'readable_name'      => $readableName,
        'supported_targets'  => array_values($suggestions),
        'can_convert'        => !empty($suggestions)
    ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
