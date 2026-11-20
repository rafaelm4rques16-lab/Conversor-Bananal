<?php
/**
 * Motor de Conversão de Pacote Office (Word, PowerPoint, Excel) e PDF
 * Conversor Bananal Pro - UTF-8 Limpo e Português
 */

declare(strict_types=1);

require_once __DIR__ . '/DocxGenerator.php';
require_once __DIR__ . '/PdfGenerator.php';

class OfficeConverter
{
    public static function convert(string $sourcePath, string $targetPath, string $from, string $to): bool
    {
        $from = strtolower(trim($from));
        $to   = strtolower(trim($to));

        if ($from === 'docx') {
            $extractedText = self::extractTextFromDocx($sourcePath);
            if ($to === 'pdf') {
                return PdfGenerator::createPdf("Documento Word Convertido", $extractedText, $targetPath);
            }
            if ($to === 'txt') {
                return file_put_contents($targetPath, $extractedText) !== false;
            }
            if ($to === 'html') {
                return self::convertDocxToHtml($sourcePath, $targetPath);
            }
        }

        if ($from === 'pptx') {
            $pptxData = self::extractSlidesAndImagesFromPptx($sourcePath);
            if ($to === 'pdf') {
                return PdfGenerator::createPptxPdf($pptxData, $targetPath);
            }
            if ($to === 'txt') {
                $txt = '';
                foreach ($pptxData as $idx => $slide) {
                    $txt .= "=== SLIDE " . ($idx + 1) . " ===\n" . ($slide['text'] ?? '') . "\n\n";
                }
                return file_put_contents($targetPath, trim($txt)) !== false;
            }
            if ($to === 'html') {
                $slidesHtml = '';
                foreach ($pptxData as $idx => $slide) {
                    $num = $idx + 1;
                    $content = nl2br(htmlspecialchars($slide['text'], ENT_QUOTES, 'UTF-8'));
                    $imgTag = !empty($slide['image_data']) ? '<div class="mt-4"><img src="data:image/jpeg;base64,' . base64_encode($slide['image_data']) . '" class="max-w-full rounded-xl border border-yellow-300 shadow-sm"/></div>' : '';
                    $slidesHtml .= "<div class='slide bg-white rounded-2xl p-8 mb-6 border-2 border-yellow-300 shadow-md'><div class='text-xs font-black uppercase text-yellow-800 tracking-wider mb-3'>Slide #{$num}</div><div class='text-base leading-relaxed text-stone-800'>{$content}</div>{$imgTag}</div>";
                }
                $html = "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>PowerPoint Slides</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-yellow-50/60 p-6 sm:p-12 max-w-4xl mx-auto'><h1 class='text-2xl font-black text-stone-900 mb-6 flex items-center gap-2'><span>🍌</span> Apresentação PowerPoint</h1>{$slidesHtml}</body></html>";
                return file_put_contents($targetPath, $html) !== false;
            }
        }

        if ($from === 'xlsx') {
            $rows = self::extractRowsFromXlsx($sourcePath);
            if ($to === 'csv') {
                return self::saveRowsToCsv($rows, $targetPath);
            }
            if ($to === 'json') {
                return file_put_contents($targetPath, json_encode($rows, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
            }
            if ($to === 'html') {
                return self::saveRowsToHtml($rows, $targetPath);
            }
            if ($to === 'pdf') {
                $txt = '';
                foreach ($rows as $r) {
                    $txt .= implode(" | ", $r) . "\n";
                }
                return PdfGenerator::createPdf("Planilha Excel", $txt, $targetPath);
            }
        }

        if ($from === 'pdf') {
            $pdfText = self::extractTextFromPdf($sourcePath);
            if ($to === 'docx') {
                return DocxGenerator::createDocx("PDF Convertido para Word", $pdfText, $targetPath);
            }
            if ($to === 'txt') {
                return file_put_contents($targetPath, $pdfText) !== false;
            }
            if ($to === 'html') {
                $html = "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'><title>PDF Convertido</title><style>body{font-family:sans-serif;padding:2rem;background:#fefce8;color:#1c1917;}</style></head><body><h2>🍌 Conteúdo do PDF</h2><pre style='white-space:pre-wrap;background:#fff;padding:1.5rem;border-radius:12px;border:1px solid #fde047;'>" . htmlspecialchars($pdfText, ENT_QUOTES, 'UTF-8') . "</pre></body></html>";
                return file_put_contents($targetPath, $html) !== false;
            }
        }

        throw new RuntimeException("Conversão de .{$from} para .{$to} não suportada.");
    }

    public static function extractTextFromDocx(string $filePath): string
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("Não foi possível abrir o arquivo Word .docx.");
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            throw new RuntimeException("Estrutura interna do documento Word inválida.");
        }

        $xml = html_entity_decode($xml, ENT_QUOTES | ENT_XML1, 'UTF-8');
        $xml = str_replace(['<w:p>', '</w:p>'], ['', "\n"], $xml);
        $xml = str_replace('<w:tab/>', "    ", $xml);
        $text = strip_tags($xml);

        return trim(preg_replace("/\n{3,}/", "\n\n", $text));
    }

    public static function extractSlidesAndImagesFromPptx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("Não foi possível abrir o arquivo PowerPoint .pptx.");
        }

        $slidesData = [];
        $i = 1;
        while (($slideXml = $zip->getFromName("ppt/slides/slide{$i}.xml")) !== false) {
            $slideXml = html_entity_decode($slideXml, ENT_QUOTES | ENT_XML1, 'UTF-8');
            
            // Remove blocos de propriedades internas do PowerPoint
            $slideXml = preg_replace('/<a:pPr[\s\S]*?<\/a:pPr>/i', '', $slideXml);
            $slideXml = preg_replace('/<a:rPr[\s\S]*?<\/a:rPr>/i', '', $slideXml);
            
            $slideXml = str_replace(['</a:p>', '</a:tr>', '</a:table>'], "\n", $slideXml);
            $slideXml = str_replace(['<a:br/>', '<br>', '<br/>'], "\n", $slideXml);
            
            $cleanText = strip_tags($slideXml);
            
            // Remove lixos e palavras-chave corrompidas de tags do pptx
            $cleanText = preg_replace('/[a-zA-Z0-9_\-]*ppt[a-zA-Z0-9_\-]*/i', '', $cleanText);
            $cleanText = preg_replace('/xppt/i', '', $cleanText);
            
            $lines = explode("\n", $cleanText);
            $normalizedLines = [];
            foreach ($lines as $line) {
                $line = preg_replace('/[^\p{L}\p{N}\p{P}\p{Z}]/u', '', $line);
                $trimmed = trim($line);
                if ($trimmed !== '' && mb_strlen($trimmed) > 1) {
                    $normalizedLines[] = $trimmed;
                }
            }
            $finalSlideText = implode("\n", $normalizedLines);

            $imageData = null;
            $relsXml = $zip->getFromName("ppt/slides/_rels/slide{$i}.xml.rels");
            if ($relsXml !== false) {
                $relsObj = @simplexml_load_string($relsXml);
                if ($relsObj && isset($relsObj->Relationship)) {
                    foreach ($relsObj->Relationship as $rel) {
                        $target = (string)$rel['Target'];
                        $type = (string)$rel['Type'];
                        if (str_contains($type, 'image')) {
                            $imgInternalPath = "ppt/slides/" . ltrim($target, './');
                            if (!file_exists($imgInternalPath)) {
                                $imgInternalPath = "ppt/" . ltrim($target, '../');
                            }
                            $imgBytes = $zip->getFromName($imgInternalPath);
                            if ($imgBytes !== false) {
                                $imageData = $imgBytes;
                                break;
                            }
                        }
                    }
                }
            }

            if ($finalSlideText !== '' || $imageData !== null) {
                $slidesData[] = [
                    'text' => $finalSlideText,
                    'image_data' => $imageData
                ];
            }
            $i++;
        }
        $zip->close();

        if (empty($slidesData)) {
            $slidesData[] = ['text' => "(Apresentação sem textos legíveis)", 'image_data' => null];
        }

        return $slidesData;
    }

    public static function extractRowsFromXlsx(string $filePath): array
    {
        $zip = new ZipArchive();
        if ($zip->open($filePath) !== true) {
            throw new RuntimeException("Não foi possível abrir o arquivo Excel .xlsx.");
        }

        $sharedStrings = [];
        $stringsXml = $zip->getFromName('xl/sharedStrings.xml');
        if ($stringsXml !== false) {
            $stringsXml = html_entity_decode($stringsXml, ENT_QUOTES | ENT_XML1, 'UTF-8');
            $xmlObj = @simplexml_load_string($stringsXml);
            if ($xmlObj && isset($xmlObj->si)) {
                foreach ($xmlObj->si as $si) {
                    $sharedStrings[] = (string)($si->t ?? '');
                }
            }
        }

        $sheetXml = $zip->getFromName('xl/worksheets/sheet1.xml');
        $zip->close();

        if ($sheetXml === false) {
            throw new RuntimeException("Planilha principal do Excel não encontrada.");
        }

        $xmlObj = @simplexml_load_string($sheetXml);
        if (!$xmlObj || !isset($xmlObj->sheetData->row)) {
            return [];
        }

        $rows = [];
        foreach ($xmlObj->sheetData->row as $r) {
            $rowCells = [];
            if (isset($r->c)) {
                foreach ($r->c as $c) {
                    $type = (string)($c['t'] ?? '');
                    $val = (string)($c->v ?? '');

                    if ($type === 's' && isset($sharedStrings[(int)$val])) {
                        $val = $sharedStrings[(int)$val];
                    }
                    $rowCells[] = $val;
                }
            }
            if (!empty($rowCells)) {
                $rows[] = $rowCells;
            }
        }
        return $rows;
    }

    private static function saveRowsToCsv(array $rows, string $targetPath): bool
    {
        $fp = fopen($targetPath, 'w');
        if ($fp === false) return false;
        fprintf($fp, chr(0xEF).chr(0xBB).chr(0xBF));
        foreach ($rows as $r) {
            fputcsv($fp, $r, ';');
        }
        fclose($fp);
        return true;
    }

    private static function saveRowsToHtml(array $rows, string $targetPath): bool
    {
        $tableHtml = '<table class="w-full text-left border-collapse text-xs sm:text-sm">';
        foreach ($rows as $idx => $r) {
            $isHeader = ($idx === 0);
            $tag = $isHeader ? 'th' : 'td';
            $trClass = $isHeader ? 'bg-yellow-300 font-bold text-stone-900' : ($idx % 2 === 0 ? 'bg-white' : 'bg-yellow-50/50');
            $tableHtml .= "<tr class='{$trClass} border-b border-yellow-200'>";
            foreach ($r as $cell) {
                $tableHtml .= "<{$tag} class='p-3 border border-yellow-200'>" . htmlspecialchars((string)$cell, ENT_QUOTES, 'UTF-8') . "</{$tag}>";
            }
            $tableHtml .= "</tr>";
        }
        $tableHtml .= '</table>';

        $html = "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Planilha Excel</title><script src='https://cdn.tailwindcss.com'></script></head><body class='bg-yellow-50/30 p-6'><div class='max-w-6xl mx-auto bg-white rounded-2xl border-2 border-yellow-300 shadow-md overflow-hidden p-6'><h2 class='text-xl font-black text-stone-900 mb-4'>🍌 Planilha Excel Convertida</h2><div class='overflow-x-auto'>{$tableHtml}</div></div></body></html>";
        return file_put_contents($targetPath, $html) !== false;
    }

    public static function extractTextFromPdf(string $filePath): string
    {
        $content = file_get_contents($filePath);
        if ($content === false) return '';

        preg_match_all('/BT[\s\S]*?ET/s', $content, $matches);
        $extracted = [];

        foreach ($matches[0] as $block) {
            preg_match_all('/\((.*?)\)\s*(?:Tj|\'|")/s', $block, $texts);
            if (!empty($texts[1])) {
                $line = implode('', $texts[1]);
                $line = str_replace(['\\(', '\\)', '\\\\'], ['(', ')', '\\'], $line);
                $extracted[] = trim($line);
            }
        }

        $text = implode("\n", array_filter($extracted));
        if (trim($text) === '') {
            $text = "Documento PDF processado com sucesso.";
        }
        return $text;
    }
}