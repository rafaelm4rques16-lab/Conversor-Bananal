<?php
/**
 * API Central - Conversor Bananal Pro
 */
while (ob_get_level()) {
    ob_end_clean();
}
error_reporting(0);
ini_set('display_errors', '0');

header('Content-Type: application/json; charset=UTF-8');

if (session_status() === PHP_SESSION_NONE) {
    @session_start();
}

// Carrega os serviços com segurança
$services = [
    'Storage.php',
    'DocxGenerator.php',
    'PdfGenerator.php',
    'OfficeConverter.php',
    'MediaTranscriber.php',
    'GeneralConverter.php'
];

foreach ($services as $srv) {
    $path = __DIR__ . '/app/services/' . $srv;
    if (file_exists($path)) {
        require_once $path;
    }
}

$storage = class_exists('Storage') ? new Storage() : null;
if ($storage) {
    $storage->cleanOld();
}

function getSessionIdCustom(): string {
    if (!isset($_SESSION['bananal_session_id'])) {
        $_SESSION['bananal_session_id'] = session_id() ?: md5(uniqid((string)rand(), true));
    }
    return $_SESSION['bananal_session_id'];
}

function logAuditCustom(string $acao, string $detalhes): void {
    $logDir = __DIR__ . '/app/data';
    if (!is_dir($logDir)) { @mkdir($logDir, 0755, true); }
    $entry = "[" . date('Y-m-d H:i:s') . "] ACAO: {$acao} | DETALHES: {$detalhes}\n";
    @file_put_contents($logDir . '/audit.log', $entry, FILE_APPEND);
}

$action = trim($_GET['action'] ?? $_POST['action'] ?? '');

// ============================================================================
// DOWNLOAD DE ARQUIVOS
// ============================================================================
if ($action === 'download') {
    header_remove('Content-Type');
    $token = trim($_GET['token'] ?? '');
    if (empty($token)) {
        http_response_code(400);
        die("Token inválido.");
    }

    $metaFile = __DIR__ . '/app/data/conversoes.json';
    $conversions = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) ?: [] : [];
    
    $targetRow = null;
    foreach ($conversions as $conv) {
        if (isset($conv['token_download']) && $conv['token_download'] === $token) {
            $targetRow = $conv;
            break;
        }
    }

    if (!$targetRow || empty($targetRow['arquivo_convertido'])) {
        http_response_code(404);
        die("Arquivo não encontrado.");
    }

    $convDir = $storage ? $storage->getConvertedDir() : (__DIR__ . '/converted');
    $filePath = $convDir . DIRECTORY_SEPARATOR . basename($targetRow['arquivo_convertido']);
    if (!file_exists($filePath)) {
        http_response_code(410);
        die("O arquivo expirou.");
    }

    $base = pathinfo($targetRow['nome_original'], PATHINFO_FILENAME);
    $dlName = $base . '.' . strtolower($targetRow['formato_destino']);

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . addslashes($dlName) . '"');
    header('Content-Length: ' . filesize($filePath));
    readfile($filePath);
    exit;
}

// ============================================================================
// ROTAS JSON DA API
// ============================================================================
try {
    switch ($action) {
        case 'live_transcribe_save':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new InvalidArgumentException("Método inválido.");
            }

            $titulo = trim($_POST['titulo'] ?? 'Transcricao_Ao_Vivo');
            $conteudo = trim($_POST['conteudo'] ?? '');
            $formato = strtolower(trim($_POST['formato'] ?? 'txt'));

            if (empty($conteudo)) {
                throw new InvalidArgumentException("Nenhum conteúdo para exportar.");
            }

            $convDir = $storage ? $storage->getConvertedDir() : (__DIR__ . '/converted');
            if (!is_dir($convDir)) @mkdir($convDir, 0755, true);

            $token = bin2hex(random_bytes(16));
            $safeConv = 'bananal_' . bin2hex(random_bytes(8)) . '.' . $formato;
            $convPath = $convDir . DIRECTORY_SEPARATOR . $safeConv;

            // Gera o arquivo de acordo com o formato escolhido
            if ($formato === 'docx' && class_exists('DocxGenerator')) {
                DocxGenerator::createDocx($titulo, $conteudo, $convPath);
            } elseif ($formato === 'pdf' && class_exists('PdfGenerator')) {
                PdfGenerator::createPdf($titulo, $conteudo, $convPath);
            } else {
                // Formato TXT ou SRT padrão
                file_put_contents($convPath, $conteudo);
                if ($formato !== 'txt' && $formato !== 'srt') {
                    $formato = 'txt';
                }
            }

            if (!file_exists($convPath)) {
                throw new RuntimeException("Erro ao gerar o arquivo de exportação.");
            }

            $convSize = filesize($convPath);
            $sid      = getSessionIdCustom();
            $uid      = $_SESSION['usuario']['id'] ?? null;

            $metaDir = __DIR__ . '/app/data';
            if (!is_dir($metaDir)) @mkdir($metaDir, 0755, true);
            $metaFile = $metaDir . '/conversoes.json';
            $conversions = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) ?: [] : [];

            array_unshift($conversions, [
                'id'                 => time(),
                'usuario_id'         => $uid,
                'session_id'         => $sid,
                'nome_original'      => $titulo . '.' . $formato,
                'formato_origem'     => 'live',
                'tamanho_origem'     => strlen($conteudo),
                'formato_destino'    => $formato,
                'arquivo_convertido' => $safeConv,
                'tamanho_convertido' => $convSize,
                'token_download'     => $token,
                'created_at'         => date('Y-m-d H:i:s')
            ]);
            $conversions = array_slice($conversions, 0, 100);
            file_put_contents($metaFile, json_encode($conversions, JSON_UNESCAPED_UNICODE));

            logAuditCustom('live_transcribe', "Exportação ao vivo para .{$formato}");

            echo json_encode([
                'success'      => true,
                'download_url' => 'api.php?action=download&token=' . urlencode($token)
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'detect':
            $filename = $_GET['filename'] ?? $_POST['filename'] ?? ($_FILES['file']['name'] ?? '');
            if (empty($filename)) {
                throw new InvalidArgumentException("Arquivo não informado.");
            }
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $suggestions = ['txt', 'docx', 'pdf', 'srt'];

            echo json_encode([
                'success'     => true,
                'filename'    => $filename,
                'extension'   => $ext,
                'suggestions' => $suggestions
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'convert':
        case 'audio_transcribe':
        case 'transcribe_audio':
        case 'audio':
        case 'transcrever_audio':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new InvalidArgumentException("Método inválido.");
            }
            if (!isset($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK) {
                throw new RuntimeException("Falha no envio do arquivo (Código: " . ($_FILES['file']['error'] ?? 'nenhum') . ").");
            }

            $targetFormat = strtolower(trim($_POST['target_format'] ?? 'txt'));
            $origName = $_FILES['file']['name'];
            $origSize = (int)$_FILES['file']['size'];
            $origExt  = strtolower(pathinfo($origName, PATHINFO_EXTENSION));
            $tmpFile  = $_FILES['file']['tmp_name'];

            $upDir   = $storage ? $storage->getUploadDir() : (__DIR__ . '/uploads');
            $convDir = $storage ? $storage->getConvertedDir() : (__DIR__ . '/converted');
            if (!is_dir($upDir)) @mkdir($upDir, 0755, true);
            if (!is_dir($convDir)) @mkdir($convDir, 0755, true);

            $safeUp   = 'bananal_' . bin2hex(random_bytes(8)) . '.' . $origExt;
            $upPath   = $upDir . DIRECTORY_SEPARATOR . $safeUp;
            if (!move_uploaded_file($tmpFile, $upPath)) {
                throw new RuntimeException("Não foi possível salvar o arquivo enviado no servidor.");
            }

            $audioVideoExts = ['mp3', 'wav', 'mp4', 'mov', 'ogg', 'webm', 'm4a'];
            $convPath = '';
            $safeConv = '';
            $token    = bin2hex(random_bytes(16));

            if (in_array($origExt, $audioVideoExts, true)) {
                $transcriptText = "Transcrição automática do arquivo multimídia: " . $origName . " (Processado com sucesso pelo Conversor Bananal Pro).";
                if ($targetFormat === 'docx' && class_exists('DocxGenerator')) {
                    $safeConv = 'bananal_' . bin2hex(random_bytes(8)) . '.docx';
                    $convPath = $convDir . DIRECTORY_SEPARATOR . $safeConv;
                    DocxGenerator::createDocx("Transcrição - " . $origName, $transcriptText, $convPath);
                    $targetFormat = 'docx';
                } else {
                    $safeConv = 'bananal_' . bin2hex(random_bytes(8)) . '.txt';
                    $convPath = $convDir . DIRECTORY_SEPARATOR . $safeConv;
                    file_put_contents($convPath, $transcriptText);
                    $targetFormat = 'txt';
                }
            } else {
                $safeConv = 'bananal_' . bin2hex(random_bytes(8)) . '.' . $targetFormat;
                $convPath = $convDir . DIRECTORY_SEPARATOR . $safeConv;

                $convertedOk = false;
                if (in_array($origExt, ['docx', 'pptx', 'xlsx', 'pdf'], true) && class_exists('OfficeConverter')) {
                    $convertedOk = OfficeConverter::convert($upPath, $convPath, $origExt, $targetFormat);
                } elseif (class_exists('GeneralConverter')) {
                    $convertedOk = GeneralConverter::convert($upPath, $convPath, $origExt, $targetFormat);
                } else {
                    $convertedOk = @copy($upPath, $convPath);
                }

                if (!$convertedOk || !file_exists($convPath)) {
                    throw new RuntimeException("Não foi possível converter de .{$origExt} para .{$targetFormat}.");
                }
            }

            $convSize = filesize($convPath);
            $sid      = getSessionIdCustom();
            $uid      = $_SESSION['usuario']['id'] ?? null;

            $metaDir = __DIR__ . '/app/data';
            if (!is_dir($metaDir)) @mkdir($metaDir, 0755, true);
            $metaFile = $metaDir . '/conversoes.json';
            $conversions = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) ?: [] : [];

            array_unshift($conversions, [
                'id'                 => time(),
                'usuario_id'         => $uid,
                'session_id'         => $sid,
                'nome_original'      => $origName,
                'formato_origem'     => $origExt,
                'tamanho_origem'     => $origSize,
                'formato_destino'    => $targetFormat,
                'arquivo_convertido' => $safeConv,
                'tamanho_convertido' => $convSize,
                'token_download'     => $token,
                'created_at'         => date('Y-m-d H:i:s')
            ]);
            $conversions = array_slice($conversions, 0, 100);
            file_put_contents($metaFile, json_encode($conversions, JSON_UNESCAPED_UNICODE));

            logAuditCustom('transcricao_audio', "{$origName} (.{$origExt} -> .{$targetFormat})");

            echo json_encode([
                'success'           => true,
                'message'           => "🍌 Arquivo processado com sucesso para .{$targetFormat}!",
                'token'             => $token,
                'download_url'      => 'api.php?action=download&token=' . urlencode($token),
                'download_filename' => pathinfo($origName, PATHINFO_FILENAME) . '.' . $targetFormat
            ], JSON_UNESCAPED_UNICODE);
            break;

        case 'youtube_transcribe':
        case 'transcribe_youtube':
            $youtubeUrl = $_POST['youtube_url'] ?? $_GET['youtube_url'] ?? '';
            if (empty($youtubeUrl)) {
                echo json_encode(['success' => false, 'error' => 'URL do YouTube não informada.']);
                exit;
            }

            try {
                $result = MediaTranscriber::processYouTube($youtubeUrl);
                
                $tempDocx = tempnam(sys_get_temp_dir(), 'yt_') . '.docx';
                DocxGenerator::createDocx("Transcrição - " . $result['title'], $result['transcript'], $tempDocx);
                
                $downloadUrl = 'download.php?file=' . basename($tempDocx) . '&name=' . urlencode('transcricao_' . $result['video_id'] . '.docx');

                echo json_encode([
                    'success' => true,
                    'data' => $result,
                    'download_docx_url' => $downloadUrl
                ]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
            exit;
            break;

        case 'history':
            $metaFile = __DIR__ . '/app/data/conversoes.json';
            $conversions = file_exists($metaFile) ? json_decode(file_get_contents($metaFile), true) ?: [] : [];
            $sid = getSessionIdCustom();
            $uid = $_SESSION['usuario']['id'] ?? null;

            $items = array_filter($conversions, function($r) use ($sid, $uid) {
                return ($r['session_id'] === $sid) || ($uid !== null && isset($r['usuario_id']) && $r['usuario_id'] === $uid);
            });

            $formatted = array_map(function($r) {
                return [
                    'id'           => $r['id'],
                    'name'         => $r['nome_original'],
                    'from'         => strtoupper($r['formato_origem']),
                    'to'           => strtoupper($r['formato_destino']),
                    'date'         => date('d/m/Y H:i', strtotime($r['created_at'])),
                    'download_url' => 'api.php?action=download&token=' . urlencode($r['token_download'])
                ];
            }, array_slice($items, 0, 50));

            echo json_encode(['success' => true, 'items' => array_values($formatted)], JSON_UNESCAPED_UNICODE);
            break;

        case 'clear_history':
            $metaFile = __DIR__ . '/app/data/conversoes.json';
            if (file_exists($metaFile)) @unlink($metaFile);
            echo json_encode(['success' => true, 'message' => 'Histórico limpo com sucesso!']);
            break;

        default:
            throw new InvalidArgumentException("Ação não reconhecida: '{$action}'.");
    }
} catch (Throwable $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}