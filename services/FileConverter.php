<?php
/**
 * Motor de Conversão de Arquivos Multi-Formato
 * Conversor Bananal
 */

declare(strict_types=1);

class FileConverter
{
    /**
     * Mapeamento de conversões suportadas de forma nativa e segura
     */
    private const SUPPORTED_MATRIX = [
        // Imagens
        'png'  => ['jpg', 'jpeg', 'webp', 'gif', 'bmp', 'pdf', 'base64'],
        'jpg'  => ['png', 'webp', 'gif', 'bmp', 'pdf', 'base64'],
        'jpeg' => ['png', 'webp', 'gif', 'bmp', 'pdf', 'base64'],
        'webp' => ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'pdf', 'base64'],
        'gif'  => ['png', 'jpg', 'jpeg', 'webp', 'bmp', 'pdf'],
        'bmp'  => ['png', 'jpg', 'jpeg', 'webp', 'gif', 'pdf'],
        
        // Dados Estruturados
        'csv'  => ['json', 'html', 'txt', 'xml'],
        'json' => ['csv', 'xml', 'html', 'txt'],
        'xml'  => ['json', 'txt', 'html'],

        // Documentos de Texto
        'txt'  => ['html', 'pdf', 'json', 'md'],
        'md'   => ['html', 'txt', 'pdf'],
        'html' => ['txt', 'md', 'pdf']
    ];

    /**
     * Verifica se a conversão do formato de origem para destino é suportada
     */
    public function canConvert(string $fromExt, string $toExt): bool
    {
        $from = strtolower(trim($fromExt));
        $to   = strtolower(trim($toExt));

        return isset(self::SUPPORTED_MATRIX[$from]) && in_array($to, self::SUPPORTED_MATRIX[$from], true);
    }

    /**
     * Retorna a lista de extensões sugeridas para onde o arquivo pode ser convertido
     */
    public function getSuggestions(string $fromExt): array
    {
        $from = strtolower(trim($fromExt));
        return self::SUPPORTED_MATRIX[$from] ?? [];
    }

    /**
     * Executa a conversão do arquivo
     * 
     * @param string $sourcePath Caminho absoluto do arquivo original
     * @param string $targetPath Caminho absoluto onde o arquivo convertido será salvo
     * @param string $fromExt Extensão de origem
     * @param string $toExt Extensão de destino
     * @return bool True em caso de sucesso
     * @throws Exception Em caso de falha com mensagem descritiva
     */
    public function convert(string $sourcePath, string $targetPath, string $fromExt, string $toExt): bool
    {
        $from = strtolower(trim($fromExt));
        $to   = strtolower(trim($toExt));

        if (!file_exists($sourcePath)) {
            throw new InvalidArgumentException("O arquivo de origem não foi encontrado no servidor.");
        }

        if (!$this->canConvert($from, $to)) {
            throw new InvalidArgumentException("A conversão de .{$from} para .{$to} ainda não é suportada.");
        }

        // Grupo 1: Conversão de Imagens
        if ($this->isImage($from) && $this->isImage($to)) {
            return $this->convertImageToImage($sourcePath, $targetPath, $from, $to);
        }

        if ($this->isImage($from) && $to === 'base64') {
            return $this->convertImageToBase64($sourcePath, $targetPath, $from);
        }

        if ($this->isImage($from) && $to === 'pdf') {
            return $this->convertImageToPdf($sourcePath, $targetPath, $from);
        }

        // Grupo 2: Conversão de Dados (CSV / JSON / XML)
        if ($from === 'csv' && $to === 'json') {
            return $this->convertCsvToJson($sourcePath, $targetPath);
        }
        if ($from === 'csv' && $to === 'html') {
            return $this->convertCsvToHtml($sourcePath, $targetPath);
        }
        if ($from === 'csv' && $to === 'txt') {
            return copy($sourcePath, $targetPath);
        }
        if ($from === 'csv' && $to === 'xml') {
            return $this->convertCsvToXml($sourcePath, $targetPath);
        }

        if ($from === 'json' && $to === 'csv') {
            return $this->convertJsonToCsv($sourcePath, $targetPath);
        }
        if ($from === 'json' && $to === 'xml') {
            return $this->convertJsonToXml($sourcePath, $targetPath);
        }
        if ($from === 'json' && $to === 'html') {
            return $this->convertJsonToHtml($sourcePath, $targetPath);
        }
        if ($from === 'json' && $to === 'txt') {
            return copy($sourcePath, $targetPath);
        }

        if ($from === 'xml' && $to === 'json') {
            return $this->convertXmlToJson($sourcePath, $targetPath);
        }
        if ($from === 'xml' && ($to === 'txt' || $to === 'html')) {
            $content = file_get_contents($sourcePath);
            return file_put_contents($targetPath, htmlspecialchars($content ?? '')) !== false;
        }

        // Grupo 3: Documentos e Textos
        if ($from === 'md' && $to === 'html') {
            return $this->convertMarkdownToHtml($sourcePath, $targetPath);
        }
        if ($from === 'md' && $to === 'txt') {
            $content = file_get_contents($sourcePath);
            $clean = preg_replace('/[#*_`~\[\]\(\)]/', '', $content ?? '');
            return file_put_contents($targetPath, $clean) !== false;
        }
        if ($from === 'md' && $to === 'pdf') {
            $html = $this->parseMarkdown(file_get_contents($sourcePath) ?: '');
            return $this->generateSimplePdf("Documento Markdown Bananal\n\n" . strip_tags($html), $targetPath);
        }

        if ($from === 'txt' && $to === 'html') {
            $content = file_get_contents($sourcePath);
            $html = "<!DOCTYPE html>\n<html lang=\"pt-BR\">\n<head><meta charset=\"UTF-8\"><title>Texto Convertido</title><style>body{font-family:sans-serif;padding:2rem;line-height:1.6;background:#fefce8;color:#1c1917;}</style></head><body>\n" 
                  . nl2br(htmlspecialchars($content ?? '', ENT_QUOTES, 'UTF-8')) 
                  . "\n</body></html>";
            return file_put_contents($targetPath, $html) !== false;
        }

        if ($from === 'txt' && $to === 'json') {
            $lines = file($sourcePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
            $json = json_encode(['linhas' => $lines ?: []], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($targetPath, $json) !== false;
        }

        if ($from === 'txt' && $to === 'pdf') {
            $content = file_get_contents($sourcePath) ?: '';
            return $this->generateSimplePdf($content, $targetPath);
        }

        if ($from === 'html' && $to === 'txt') {
            $content = file_get_contents($sourcePath) ?: '';
            $text = strip_tags($content);
            $text = html_entity_decode($text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            return file_put_contents($targetPath, trim($text)) !== false;
        }

        if ($from === 'html' && $to === 'pdf') {
            $content = file_get_contents($sourcePath) ?: '';
            $text = strip_tags($content);
            return $this->generateSimplePdf(trim($text), $targetPath);
        }

        throw new RuntimeException("Conversão de .{$from} para .{$to} não pôde ser completada.");
    }

    private function isImage(string $ext): bool
    {
        return in_array($ext, ['png', 'jpg', 'jpeg', 'webp', 'gif', 'bmp'], true);
    }

    /**
     * Converte imagens utilizando a biblioteca nativa GD do PHP
     */
    private function convertImageToImage(string $source, string $target, string $from, string $to): bool
    {
        if (!extension_loaded('gd')) {
            throw new RuntimeException("A extensão PHP GD não está habilitada no servidor para manipulação de imagens.");
        }

        // Carrega a imagem conforme formato
        $image = match ($from) {
            'png'         => @imagecreatefrompng($source),
            'jpg', 'jpeg' => @imagecreatefromjpeg($source),
            'webp'        => @imagecreatefromwebp($source),
            'gif'         => @imagecreatefromgif($source),
            'bmp'         => @imagecreatefrombmp($source),
            default       => false
        };

        if ($image === false) {
            throw new RuntimeException("Não foi possível decodificar o arquivo de imagem original.");
        }

        $width = imagesx($image);
        $height = imagesy($image);

        // Se o destino for JPG/JPEG ou BMP, preenche o fundo transparente com branco
        if (in_array($to, ['jpg', 'jpeg', 'bmp'], true)) {
            $bg = imagecreatetruecolor($width, $height);
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefilledrectangle($bg, 0, 0, $width, $height, $white);
            imagecopy($bg, $image, 0, 0, 0, 0, $width, $height);
            imagedestroy($image);
            $image = $bg;
        } else {
            // Preserva transparência para PNG, WEBP, GIF
            imagealphablending($image, false);
            imagesavealpha($image, true);
        }

        // Salva imagem no formato de destino
        $success = match ($to) {
            'png'         => imagepng($image, $target, 8),
            'jpg', 'jpeg' => imagejpeg($image, $target, 90),
            'webp'        => imagewebp($image, $target, 90),
            'gif'         => imagegif($image, $target),
            'bmp'         => imagebmp($image, $target),
            default       => false
        };

        imagedestroy($image);

        if (!$success) {
            throw new RuntimeException("Erro ao gerar o arquivo de imagem de destino ({$to}).");
        }

        return true;
    }

    private function convertImageToBase64(string $source, string $target, string $ext): bool
    {
        $data = file_get_contents($source);
        if ($data === false) {
            throw new RuntimeException("Falha ao ler dados da imagem.");
        }
        $mime = ($ext === 'jpg' || $ext === 'jpeg') ? 'image/jpeg' : 'image/' . $ext;
        $base64 = 'data:' . $mime . ';base64,' . base64_encode($data);
        return file_put_contents($target, $base64) !== false;
    }

    private function convertImageToPdf(string $source, string $target, string $ext): bool
    {
        // Se a imagem for PNG ou GIF ou BMP, converte temporariamente para JPEG para embutir nativamente no PDF
        $tempJpg = null;
        if ($ext !== 'jpg' && $ext !== 'jpeg') {
            $tempJpg = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bananal_tmp_' . uniqid() . '.jpg';
            $this->convertImageToImage($source, $tempJpg, $ext, 'jpg');
            $imgToEmbed = $tempJpg;
        } else {
            $imgToEmbed = $source;
        }

        $imgData = file_get_contents($imgToEmbed);
        $size = @getimagesize($imgToEmbed);
        $w = $size ? $size[0] : 600;
        $h = $size ? $size[1] : 400;

        if ($tempJpg && file_exists($tempJpg)) {
            @unlink($tempJpg);
        }

        // Constrói PDF mínimo em conformidade com PDF-1.4 contendo a imagem
        $pdf = $this->buildPdfWithJpeg($imgData, $w, $h);
        return file_put_contents($target, $pdf) !== false;
    }

    private function convertCsvToJson(string $source, string $target): bool
    {
        $handle = fopen($source, 'r');
        if ($handle === false) {
            throw new RuntimeException("Não foi possível abrir o arquivo CSV.");
        }

        // Detecta delimitador (vírgula ou ponto-e-vírgula)
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count((string)$firstLine, ';') > substr_count((string)$firstLine, ',')) ? ';' : ',';

        $headers = fgetcsv($handle, 0, $delimiter);
        if ($headers === false) {
            fclose($handle);
            throw new RuntimeException("O arquivo CSV está vazio ou em formato inválido.");
        }

        // Remove possíveis BOMs dos cabeçalhos
        $headers = array_map(function($h) {
            return trim(preg_replace('/\x{EF}\x{BB}\x{BF}/', '', (string)$h));
        }, $headers);

        $rows = [];
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            if (count($data) === 1 && $data[0] === null) continue;
            $row = [];
            foreach ($headers as $index => $header) {
                $key = $header !== '' ? $header : "coluna_" . ($index + 1);
                $row[$key] = $data[$index] ?? '';
            }
            $rows[] = $row;
        }
        fclose($handle);

        $json = json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        return file_put_contents($target, $json) !== false;
    }

    private function convertCsvToHtml(string $source, string $target): bool
    {
        $handle = fopen($source, 'r');
        if ($handle === false) {
            throw new RuntimeException("Não foi possível abrir o CSV.");
        }

        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count((string)$firstLine, ';') > substr_count((string)$firstLine, ',')) ? ';' : ',';

        $html = "<!DOCTYPE html>\n<html lang=\"pt-BR\">\n<head>\n<meta charset=\"UTF-8\">\n<title>Tabela CSV Convertida - Conversor Bananal</title>\n";
        $html .= "<style>body{font-family:sans-serif;padding:2rem;background:#fefce8;color:#1c1917;}table{border-collapse:collapse;width:100%;margin-top:1rem;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 4px 6px -1px rgba(0,0,0,0.1);}th,td{padding:12px 16px;border:1px solid #fef08a;text-align:left;}th{background:#fde047;font-weight:700;color:#713f12;}tr:nth-child(even){background:#fef9c3;}</style>\n</head>\n<body>\n";
        $html .= "<h2>🍌 Planilha Convertida pelo Conversor Bananal</h2>\n<table>\n";

        $isHeader = true;
        while (($data = fgetcsv($handle, 0, $delimiter)) !== false) {
            $html .= "<tr>";
            $tag = $isHeader ? "th" : "td";
            foreach ($data as $cell) {
                $html .= "<{$tag}>" . htmlspecialchars((string)$cell, ENT_QUOTES, 'UTF-8') . "</{$tag}>";
            }
            $html .= "</tr>\n";
            $isHeader = false;
        }
        fclose($handle);

        $html .= "</table>\n</body>\n</html>";
        return file_put_contents($target, $html) !== false;
    }

    private function convertCsvToXml(string $source, string $target): bool
    {
        $handle = fopen($source, 'r');
        if ($handle === false) {
            throw new RuntimeException("Falha ao abrir CSV.");
        }
        $firstLine = fgets($handle);
        rewind($handle);
        $delimiter = (substr_count((string)$firstLine, ';') > substr_count((string)$firstLine, ',')) ? ';' : ',';

        $headers = fgetcsv($handle, 0, $delimiter);
        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><dados></dados>");

        while (($row = fgetcsv($handle, 0, $delimiter)) !== false) {
            $item = $xml->addChild('registro');
            foreach ($row as $i => $val) {
                $key = preg_replace('/[^a-zA-Z0-9_]/', '_', (string)($headers[$i] ?? 'coluna_' . ($i + 1)));
                if (empty($key) || is_numeric($key[0])) {
                    $key = 'campo_' . $key;
                }
                $item->addChild($key, htmlspecialchars((string)$val));
            }
        }
        fclose($handle);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return file_put_contents($target, $dom->saveXML()) !== false;
    }

    private function convertJsonToCsv(string $source, string $target): bool
    {
        $jsonStr = file_get_contents($source);
        $data = json_decode((string)$jsonStr, true);

        if ($data === null) {
            throw new RuntimeException("O arquivo não contém uma estrutura JSON válida.");
        }

        // Se o JSON for um único objeto associativo, encapsula em array
        if (!isset($data[0]) && is_array($data)) {
            $data = [$data];
        }

        if (empty($data) || !is_array($data[0])) {
            throw new RuntimeException("O JSON deve conter um array de objetos para conversão em planilha CSV.");
        }

        // Extrai todos os cabeçalhos únicos
        $headers = [];
        foreach ($data as $item) {
            if (is_array($item)) {
                $headers = array_unique(array_merge($headers, array_keys($item)));
            }
        }

        $handle = fopen($target, 'w');
        if ($handle === false) {
            throw new RuntimeException("Não foi possível criar o arquivo CSV.");
        }

        // Insere UTF-8 BOM para garantir compatibilidade com Excel
        fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));
        fputcsv($handle, $headers, ';');

        foreach ($data as $row) {
            $line = [];
            foreach ($headers as $header) {
                $val = $row[$header] ?? '';
                if (is_array($val)) {
                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                }
                $line[] = (string)$val;
            }
            fputcsv($handle, $line, ';');
        }

        fclose($handle);
        return true;
    }

    private function convertJsonToXml(string $source, string $target): bool
    {
        $jsonStr = file_get_contents($source);
        $data = json_decode((string)$jsonStr, true);
        if ($data === null) {
            throw new RuntimeException("Arquivo JSON inválido.");
        }

        $xml = new SimpleXMLElement("<?xml version=\"1.0\" encoding=\"UTF-8\"?><dados></dados>");
        $this->arrayToXml($data, $xml);

        $dom = dom_import_simplexml($xml)->ownerDocument;
        $dom->formatOutput = true;
        return file_put_contents($target, $dom->saveXML()) !== false;
    }

    private function arrayToXml(array $data, SimpleXMLElement &$xml): void
    {
        foreach ($data as $key => $value) {
            $nodeName = is_numeric($key) ? "item_{$key}" : preg_replace('/[^a-zA-Z0-9_]/', '_', (string)$key);
            if (empty($nodeName) || is_numeric($nodeName[0])) {
                $nodeName = 'campo_' . $nodeName;
            }

            if (is_array($value)) {
                $subNode = $xml->addChild($nodeName);
                $this->arrayToXml($value, $subNode);
            } else {
                $xml->addChild($nodeName, htmlspecialchars((string)$value));
            }
        }
    }

    private function convertJsonToHtml(string $source, string $target): bool
    {
        $jsonStr = file_get_contents($source);
        $data = json_decode((string)$jsonStr, true);
        if ($data === null) {
            throw new RuntimeException("JSON inválido.");
        }

        $formatted = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        $html = "<!DOCTYPE html>\n<html lang=\"pt-BR\">\n<head><meta charset=\"UTF-8\"><title>Visualizador JSON</title><style>body{font-family:monospace;padding:2rem;background:#18181b;color:#fde047;}pre{background:#27272a;padding:1.5rem;border-radius:8px;border:1px solid #eab308;overflow-x:auto;}</style></head><body><h2>🍌 Dados JSON Formatados</h2><pre>" . htmlspecialchars((string)$formatted) . "</pre></body></html>";

        return file_put_contents($target, $html) !== false;
    }

    private function convertXmlToJson(string $source, string $target): bool
    {
        $xmlStr = file_get_contents($source);
        $xml = @simplexml_load_string((string)$xmlStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        if ($xml === false) {
            throw new RuntimeException("Não foi possível carregar a estrutura XML.");
        }

        $json = json_encode($xml, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        return file_put_contents($target, $json) !== false;
    }

    private function convertMarkdownToHtml(string $source, string $target): bool
    {
        $md = file_get_contents($source) ?: '';
        $body = $this->parseMarkdown($md);

        $html = "<!DOCTYPE html>\n<html lang=\"pt-BR\">\n<head>\n<meta charset=\"UTF-8\">\n<meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">\n<title>Documento Convertido - Conversor Bananal</title>\n";
        $html .= "<style>body{font-family:system-ui,-apple-system,sans-serif;line-height:1.7;max-width:800px;margin:2rem auto;padding:1rem;background:#fefce8;color:#1c1917;}h1,h2,h3{color:#854d0e;}pre,code{background:#fef08a;padding:2px 6px;border-radius:4px;}pre{padding:1rem;overflow-x:auto;}blockquote{border-left:4px solid #eab308;margin:1rem 0;padding-left:1rem;color:#713f12;}</style>\n</head>\n<body>\n";
        $html .= $body;
        $html .= "\n</body>\n</html>";

        return file_put_contents($target, $html) !== false;
    }

    private function parseMarkdown(string $md): string
    {
        // Headers
        $md = preg_replace('/^### (.*$)/m', '<h3>$1</h3>', $md);
        $md = preg_replace('/^## (.*$)/m', '<h2>$1</h2>', $md);
        $md = preg_replace('/^# (.*$)/m', '<h1>$1</h1>', $md);

        // Bold & Italic
        $md = preg_replace('/\*\*(.*?)\*\*/s', '<strong>$1</strong>', $md);
        $md = preg_replace('/\*([^\*]+)\*/', '<em>$1</em>', $md);

        // Blockquotes
        $md = preg_replace('/^> (.*$)/m', '<blockquote>$1</blockquote>', $md);

        // Unordered lists
        $md = preg_replace('/^\- (.*$)/m', '<li>$1</li>', $md);

        // Links
        $md = preg_replace('/\[([^\]]+)\]\(([^)]+)\)/', '<a href="$2" target="_blank" style="color:#ca8a04;">$1</a>', $md);

        // Paragraphs
        $lines = explode("\n", $md);
        $parsed = [];
        foreach ($lines as $line) {
            $trimmed = trim($line);
            if ($trimmed !== '' && !preg_match('/^<(\/?[hH\d]|li|blockquote|ul|pre)/', $trimmed)) {
                $parsed[] = '<p>' . $trimmed . '</p>';
            } else {
                $parsed[] = $trimmed;
            }
        }

        return implode("\n", $parsed);
    }

    /**
     * Gera um PDF estruturado em padrão PDF-1.4 sem depender de bibliotecas externas
     */
    private function generateSimplePdf(string $text, string $targetPath): bool
    {
        $safeText = preg_replace('/[^\x20-\x7E\xA0-\xFF]/', ' ', $text);
        $lines = explode("\n", wordwrap($safeText, 80, "\n"));
        
        $stream = "BT /F1 11 Tf 50 780 Td 14 TL ";
        $stream .= "(CONVERSOR BANANAL - DOCUMENTO CONVERTIDO) Tj T* T* ";
        
        $maxLines = 50; // primeira página
        $count = 0;
        foreach ($lines as $line) {
            if ($count++ > $maxLines) break;
            $lineClean = str_replace(['(', ')', '\\'], ['\\(', '\\)', '\\\\'], $line);
            $stream .= "({$lineClean}) Tj T* ";
        }
        $stream .= "ET";

        $length = strlen($stream);

        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $pdf .= "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $pdf .= "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents 4 0 R /Resources << /Font << /F1 5 0 R >> >> >> endobj\n";
        $pdf .= "4 0 obj << /Length {$length} >> stream\n{$stream}\nendstream\nendobj\n";
        $pdf .= "5 0 obj << /Type /Font /Subtype /Type1 /BaseFont /Helvetica >> endobj\n";
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        $pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n%%EOF";

        return file_put_contents($targetPath, $pdf) !== false;
    }

    /**
     * Constrói PDF com imagem JPEG embutida nativamente
     */
    private function buildPdfWithJpeg(string $jpegData, int $w, int $h): string
    {
        $pageW = 595; // A4 pt
        $pageH = 842;

        // Calcula escala para caber na página A4 mantendo proporção
        $scale = min(($pageW - 80) / max($w, 1), ($pageH - 120) / max($h, 1), 1.0);
        $displayW = round($w * $scale);
        $displayH = round($h * $scale);
        $posX = round(($pageW - $displayW) / 2);
        $posY = round(($pageH - $displayH) / 2);

        $imgLen = strlen($jpegData);
        $stream = "q {$displayW} 0 0 {$displayH} {$posX} {$posY} cm /Img1 Do Q";
        $streamLen = strlen($stream);

        $pdf = "%PDF-1.4\n";
        $pdf .= "1 0 obj << /Type /Catalog /Pages 2 0 R >> endobj\n";
        $pdf .= "2 0 obj << /Type /Pages /Kids [3 0 R] /Count 1 >> endobj\n";
        $pdf .= "3 0 obj << /Type /Page /Parent 2 0 R /MediaBox [0 0 {$pageW} {$pageH}] /Contents 4 0 R /Resources << /XObject << /Img1 5 0 R >> >> >> endobj\n";
        $pdf .= "4 0 obj << /Length {$streamLen} >> stream\n{$stream}\nendstream\nendobj\n";
        $pdf .= "5 0 obj << /Type /XObject /Subtype /Image /Width {$w} /Height {$h} /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$imgLen} >> stream\n" . $jpegData . "\nendstream\nendobj\n";
        $pdf .= "xref\n0 6\n0000000000 65535 f \n";
        $pdf .= "trailer << /Size 6 /Root 1 0 R >>\nstartxref\n%%EOF";

        return $pdf;
    }
}
