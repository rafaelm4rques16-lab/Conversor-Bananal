<?php
/**
 * Gerador Nativo de PDF (Padrão PDF-1.4)
 * Conversor Bananal Pro
 */

declare(strict_types=1);

class PdfGenerator
{
    public static function createPdf(string $title, string $content, string $outputPath): bool
    {
        $winContent = self::toWinAnsi($content);

        $rawLines = explode("\n", str_replace("\r", "", $winContent));
        $wrappedLines = [];

        foreach ($rawLines as $line) {
            $line = trim($line);
            if ($line === '') {
                $wrappedLines[] = '';
                continue;
            }
            $parts = explode("\n", wordwrap($line, 85, "\n", true));
            foreach ($parts as $p) {
                $wrappedLines[] = trim($p);
            }
        }

        $linesPerPage = 52;
        $pages = array_chunk($wrappedLines, $linesPerPage);
        if (empty($pages)) {
            $pages = [['(Documento sem conteúdo)']];
        }

        $pageCount = count($pages);
        $objects = [];

        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        $kids = [];
        $pageStartObj = 3;
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjId = $pageStartObj + ($i * 2);
            $kids[] = "{$pageObjId} 0 R";
        }
        $kidsStr = implode(' ', $kids);

        $objects[2] = "<< /Type /Pages /Kids [{$kidsStr}] /Count {$pageCount} >>";

        $currentObjId = 3;
        $fontObjId = 3 + ($pageCount * 2);

        foreach ($pages as $pageLines) {
            $contentObjId = $currentObjId + 1;

            $objects[$currentObjId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Contents {$contentObjId} 0 R /Resources << /Font << /F1 {$fontObjId} 0 R >> >> >>";

            $stream = "BT /F1 11 Tf 50 790 Td 14 TL ";
            foreach ($pageLines as $l) {
                if ($l === '') {
                    $stream .= "T* ";
                } else {
                    $stream .= "(" . self::escapePdf($l) . ") Tj T* ";
                }
            }
            $stream .= "ET";

            $streamLen = strlen($stream);
            $objects[$contentObjId] = "<< /Length {$streamLen} >> stream\n{$stream}\nendstream";

            $currentObjId += 2;
        }

        $objects[$fontObjId] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";

        $output = "%PDF-1.4\n";
        $offsets = [0];

        for ($id = 1; $id <= $fontObjId; $id++) {
            $offsets[$id] = strlen($output);
            $output .= "{$id} 0 obj\n" . $objects[$id] . "\nendobj\n";
        }

        $xrefStart = strlen($output);
        $totalObjs = $fontObjId + 1;
        $output .= "xref\n0 {$totalObjs}\n";
        $output .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $fontObjId; $id++) {
            $output .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $output .= "trailer\n<< /Size {$totalObjs} /Root 1 0 R >>\n";
        $output .= "startxref\n{$xrefStart}\n%%EOF";

        return file_put_contents($outputPath, $output) !== false;
    }

    public static function createPptxPdf(array $slidesData, string $outputPath): bool
    {
        $pageCount = count($slidesData);
        if ($pageCount === 0) {
            return self::createPdf("Apresentação", "Apresentação vazia", $outputPath);
        }

        $objects = [];
        $objects[1] = "<< /Type /Catalog /Pages 2 0 R >>";

        $kids = [];
        $pageStartObj = 3;
        for ($i = 0; $i < $pageCount; $i++) {
            $pageObjId = $pageStartObj + ($i * 3);
            $kids[] = "{$pageObjId} 0 R";
        }
        $kidsStr = implode(' ', $kids);

        $objects[2] = "<< /Type /Pages /Kids [{$kidsStr}] /Count {$pageCount} >>";

        $currentObjId = 3;
        $fontObjId = $pageStartObj + ($pageCount * 3);

        foreach ($slidesData as $idx => $slide) {
            $pageObjId = $currentObjId;
            $contentObjId = $currentObjId + 1;
            $xObjId = $currentObjId + 2;

            // Limpa e formata linhas do slide de forma estrita
            $cleanText = self::toWinAnsi($slide['text']);
            $rawLines = explode("\n", str_replace("\r", "", $cleanText));
            $wrappedLines = [];
            foreach ($rawLines as $l) {
                $l = trim($l);
                if ($l === '') {
                    $wrappedLines[] = '';
                    continue;
                }
                $chunks = explode("\n", wordwrap($l, 70, "\n", true));
                foreach ($chunks as $chunk) {
                    $wrappedLines[] = trim($chunk);
                }
            }

            $hasImage = !empty($slide['image_data']);
            $resources = "<< /Font << /F1 {$fontObjId} 0 R >>";
            if ($hasImage) {
                $resources .= " /XObject << /Im1 {$xObjId} 0 R >>";
            }
            $resources .= " >>";

            $objects[$pageObjId] = "<< /Type /Page /Parent 2 0 R /MediaBox [0 0 595 842] /Resources {$resources} /Contents {$contentObjId} 0 R >>";

            $stream = "";
            if ($hasImage) {
                $imgInfo = @getimagesizefromstring($slide['image_data']);
                if ($imgInfo) {
                    $imgW = (int)$imgInfo[0];
                    $imgH = (int)$imgInfo[1];
                    $maxW = 420;
                    $maxH = 360;
                    $scale = min($maxW / $imgW, $maxH / $imgH);
                    $wPt = round($imgW * $scale, 2);
                    $hPt = round($imgH * $scale, 2);
                    $xPt = round((595 - $wPt) / 2, 2);
                    $yPt = 440;

                    $stream .= "q {$wPt} 0 0 {$hPt} {$xPt} {$yPt} cm /Im1 Do Q\n";
                }

                $imgLen = strlen($slide['image_data']);
                $objects[$xObjId] = "<< /Type /XObject /Subtype /Image /Width " . ($imgInfo[0] ?? 100) . " /Height " . ($imgInfo[1] ?? 100) . " /ColorSpace /DeviceRGB /BitsPerComponent 8 /Filter /DCTDecode /Length {$imgLen} >> stream\n" . $slide['image_data'] . "\nendstream";
            } else {
                $objects[$xObjId] = "<< /Type /XObject /Subtype /Image /Width 10 /Height 10 /ColorSpace /DeviceRGB /BitsPerComponent 8 /Length 0 >> stream\n\nendstream";
            }

            $textY = $hasImage ? 400 : 750;
            $stream .= "BT /F1 11 Tf 50 {$textY} Td 14 TL ";
            foreach ($wrappedLines as $line) {
                if ($line === '') {
                    $stream .= "T* ";
                } else {
                    $stream .= "(" . self::escapePdf($line) . ") Tj T* ";
                }
            }
            $stream .= "ET";

            $streamLen = strlen($stream);
            $objects[$contentObjId] = "<< /Length {$streamLen} >> stream\n{$stream}\nendstream";

            $currentObjId += 3;
        }

        $objects[$fontObjId] = "<< /Type /Font /Subtype /Type1 /BaseFont /Helvetica /Encoding /WinAnsiEncoding >>";

        $output = "%PDF-1.4\n";
        $offsets = [0];

        for ($id = 1; $id <= $fontObjId; $id++) {
            $offsets[$id] = strlen($output);
            $output .= "{$id} 0 obj\n" . $objects[$id] . "\nendobj\n";
        }

        $xrefStart = strlen($output);
        $totalObjs = $fontObjId + 1;
        $output .= "xref\n0 {$totalObjs}\n";
        $output .= "0000000000 65535 f \n";

        for ($id = 1; $id <= $fontObjId; $id++) {
            $output .= sprintf("%010d 00000 n \n", $offsets[$id]);
        }

        $output .= "trailer\n<< /Size {$totalObjs} /Root 1 0 R >>\n";
        $output .= "startxref\n{$xrefStart}\n%%EOF";

        return file_put_contents($outputPath, $output) !== false;
    }

    private static function toWinAnsi(string $utf8Text): string
    {
        $clean = preg_replace('/^\xEF\xBB\xBF/', '', $utf8Text) ?? $utf8Text;

        if (function_exists('mb_convert_encoding')) {
            $converted = @mb_convert_encoding($clean, 'Windows-1252', 'UTF-8');
            if ($converted !== false && $converted !== '') {
                return preg_replace('/[^\x20-\x7E\xA0-\xFF\n\r]/', ' ', $converted) ?? $converted;
            }
        }

        if (function_exists('iconv')) {
            $converted = @iconv('UTF-8', 'Windows-1252//TRANSLIT//IGNORE', $clean);
            if ($converted !== false) {
                return preg_replace('/[^\x20-\x7E\xA0-\xFF\n\r]/', ' ', $converted) ?? $converted;
            }
        }

        return preg_replace('/[^\x20-\x7E\xA0-\xFF\n\r]/', ' ', $clean) ?? $clean;
    }

    private static function escapePdf(string $str): string
    {
        return str_replace(['\\', '(', ')'], ['\\\\', '\\(', '\\)'], $str);
    }
}