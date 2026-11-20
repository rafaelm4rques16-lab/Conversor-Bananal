<?php
declare(strict_types=1);

require_once __DIR__ . '/DocxGenerator.php';
require_once __DIR__ . '/PdfGenerator.php';

class GeneralConverter
{
    public static function getSuggestions(string $ext): array
    {
        $ext = strtolower(trim($ext));

        if (in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'], true)) {
            return ['pdf', 'webp', 'png', 'jpg'];
        }

        if (in_array($ext, ['mp3', 'wav', 'ogg', 'mp4', 'mov', 'webm'], true)) {
            return ['txt', 'mp3', 'wav', 'mp4'];
        }

        if (in_array($ext, ['txt', 'md', 'html', 'csv', 'json'], true)) {
            return ['pdf', 'docx', 'txt', 'html', 'json'];
        }

        return ['pdf', 'docx', 'txt', 'html'];
    }

    public static function convert(string $upPath, string $convPath, string $origExt, string $targetFormat): bool
    {
        $origExt = strtolower(trim($origExt));
        $targetFormat = strtolower(trim($targetFormat));

        if (!is_file($upPath)) {
            return false;
        }

        $dir = dirname($convPath);
        if (!is_dir($dir)) {
            @mkdir($dir, 0755, true);
        }

        $imgExts = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'bmp'];

        // 1. Conversão de Imagem para PDF (APENAS A IMAGEM, SEM TEXTO NENHUM)
        if (in_array($origExt, $imgExts, true) && $targetFormat === 'pdf') {
            return self::convertImageToPurePdf($upPath, $convPath, $origExt);
        }

        // 2. Conversão de Imagem para outro formato de Imagem (JPG, PNG, WEBP, GIF)
        if (in_array($origExt, $imgExts, true) && in_array($targetFormat, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            if (extension_loaded('gd')) {
                $image = self::loadImage($upPath, $origExt);
                if ($image !== null) {
                    $ok = self::saveImage($image, $convPath, $targetFormat);
                    @imagedestroy($image);
                    if ($ok && is_file($convPath)) {
                        return true;
                    }
                }
            }
            return @copy($upPath, $convPath);
        }

        // 3. Documentos / Texto para DOCX, PDF ou outros formatos
        $rawContent = @file_get_contents($upPath);
        $textContent = ($rawContent !== false) ? $rawContent : '';

        if ($targetFormat === 'docx') {
            $cleanText = strip_tags($textContent);
            return DocxGenerator::createDocx("Documento Convertido", $cleanText !== '' ? $cleanText : "Conteúdo convertido.", $convPath);
        }

        if ($targetFormat === 'pdf') {
            $cleanText = strip_tags($textContent);
            return PdfGenerator::createPdf("Documento Convertido", $cleanText !== '' ? $cleanText : "Conteúdo convertido.", $convPath);
        }

        if ($targetFormat === 'html' && $origExt === 'md') {
            $html = "<!DOCTYPE html><html lang='pt-BR'><head><meta charset='UTF-8'><title>Markdown Convertido</title></head><body><pre>" . htmlspecialchars($textContent) . "</pre></body></html>";
            return file_put_contents($convPath, $html) !== false;
        }

        if ($targetFormat === 'json' && in_array($origExt, ['txt', 'md', 'html', 'csv'], true)) {
            $payload = json_encode([
                'source_file' => basename($upPath),
                'from' => $origExt,
                'content' => $textContent
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            return file_put_contents($convPath, (string)$payload) !== false;
        }

        if ($rawContent === false) {
            return @copy($upPath, $convPath);
        }

        return file_put_contents($convPath, $rawContent) !== false;
    }

    /**
     * Gera um arquivo PDF válido contendo EXCLUSIVAMENTE a imagem original (sem nenhum texto).
     */
    private static function convertImageToPurePdf(string $imagePath, string $pdfPath, string $origExt): bool
    {
        $jpegPath = $imagePath;
        $tempJpeg = null;

        // PDF nativo embute JPEG diretamente via DCTDecode. Se não for JPG, converte temporariamente via GD.
        if (!in_array($origExt, ['jpg', 'jpeg'], true)) {
            if (!extension_loaded('gd')) {
                return false;
            }
            $img = self::loadImage($imagePath, $origExt);
            if ($img === null) {
                return false;
            }

            $w = imagesx($img);
            $h = imagesy($img);
            $bg = imagecreatetruecolor($w, $h);
            $white = imagecolorallocate($bg, 255, 255, 255);
            imagefill($bg, 0, 0, $white);
            imagecopy($bg, $img, 0, 0, 0, 0, $w, $h);

            $tempJpeg = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'bananal_tmp_' . uniqid() . '.jpg';
            $saved = imagejpeg($bg, $tempJpeg, 92);
            @imagedestroy($img);
            @imagedestroy($bg);

            if (!$saved || !is_file($tempJpeg)) {
                return false;
            }
            $jpegPath = $tempJpeg;
        }

        $info = @getimagesize($jpegPath);
        $jpegData = @file_get_contents($jpegPath);

        if ($tempJpeg && is_file($tempJpeg)) {
            @unlink($tempJpeg);
        }

        if (!$info || $jpegData === false) {
            return false;
        }

        $widthPx  = (int)$info[0];
        $heightPx = (int)$info[1];
        $channels = isset($info['channels']) && (int)$info['channels'] === 4 ? '/DeviceCMYK' : '/DeviceRGB';
        $imgLen   = strlen($jpegData);

        // Converte pixels para pontos PDF (72 DPI) mantendo proporção exata da imagem
        $pageW = round($widthPx * 0.75, 2);
        $pageH = round($heightPx * 0.75, 2);

        $contentStream = "q {$pageW} 0 0 {$pageH} 0 0 cm /Im1 Do Q";
        $streamLen = strlen($contentStream);

        $objects = [];
        $objects[1] = "1 0 obj\n<< /Type /Catalog /Pages 2 0 R >>\nendobj\n";
        $objects[2] = "2 0 obj\n<< /Type /Pages /Kids [3 0 R] /Count 1 >>\nendobj\n";
        $objects[3] = "3 0 obj\n<< /Type /Page /Parent 2 0 R /Resources << /XObject << /Im1 4 0 R >> >> /MediaBox [0 0 {$pageW} {$pageH}] /Contents 5 0 R >>\nendobj\n";
        $objects[4] = "4 0 obj\n<< /Type /XObject /Subtype /Image /Width {$widthPx} /Height {$heightPx} /ColorSpace {$channels} /BitsPerComponent 8 /Filter /DCTDecode /Length {$imgLen} >>\nstream\n{$jpegData}\nendstream\nendobj\n";
        $objects[5] = "5 0 obj\n<< /Length {$streamLen} >>\nstream\n{$contentStream}\nendstream\nendobj\n";

        $pdf = "%PDF-1.4\n";
        $offsets = [0];
        foreach ($objects as $idx => $obj) {
            $offsets[$idx] = strlen($pdf);
            $pdf .= $obj;
        }

        $xrefPos = strlen($pdf);
        $pdf .= "xref\n0 " . (count($objects) + 1) . "\n";
        $pdf .= "0000000000 65535 f \n";
        for ($i = 1; $i <= count($objects); $i++) {
            $pdf .= sprintf("%010d 00000 n \n", $offsets[$i]);
        }

        $pdf .= "trailer\n<< /Size " . (count($objects) + 1) . " /Root 1 0 R >>\n";
        $pdf .= "startxref\n{$xrefPos}\n%%EOF";

        return file_put_contents($pdfPath, $pdf) !== false;
    }

    private static function loadImage(string $path, string $ext)
    {
        if (($ext === 'jpg' || $ext === 'jpeg') && function_exists('imagecreatefromjpeg')) {
            return @imagecreatefromjpeg($path) ?: null;
        }
        if ($ext === 'png' && function_exists('imagecreatefrompng')) {
            return @imagecreatefrompng($path) ?: null;
        }
        if ($ext === 'webp' && function_exists('imagecreatefromwebp')) {
            return @imagecreatefromwebp($path) ?: null;
        }
        if ($ext === 'gif' && function_exists('imagecreatefromgif')) {
            return @imagecreatefromgif($path) ?: null;
        }
        if ($ext === 'bmp' && function_exists('imagecreatefrombmp')) {
            return @imagecreatefrombmp($path) ?: null;
        }
        return null;
    }

    private static function saveImage($image, string $targetPath, string $format): bool
    {
        if ($format === 'webp' && function_exists('imagewebp')) {
            return @imagewebp($image, $targetPath, 90);
        }
        if ($format === 'png' && function_exists('imagepng')) {
            if (function_exists('imagesavealpha')) {
                @imagesavealpha($image, true);
            }
            return @imagepng($image, $targetPath, 6);
        }
        if (($format === 'jpg' || $format === 'jpeg') && function_exists('imagejpeg')) {
            return @imagejpeg($image, $targetPath, 90);
        }
        if ($format === 'gif' && function_exists('imagegif')) {
            return @imagegif($image, $targetPath);
        }
        return false;
    }
}