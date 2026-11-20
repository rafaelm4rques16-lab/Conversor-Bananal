<?php
/**
 * Gerador Nativo de Arquivos Microsoft Word (.docx)
 * Conversor Bananal Pro
 */

declare(strict_types=1);

class DocxGenerator
{
    /**
     * Gera um arquivo .docx válido a partir de texto ou parágrafos
     */
    public static function createDocx(string $title, string|array $content, string $outputPath): bool
    {
        $paragraphs = is_array($content) ? $content : explode("\n", (string)$content);

        $bodyXml = '';
        
        // Título estilizado no Word
        $safeTitle = htmlspecialchars($title, ENT_XML1, 'UTF-8');
        $bodyXml .= '<w:p><w:pPr><w:pStyle w:val="Heading1"/><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="36"/><w:color w:val="CA8A04"/></w:rPr><w:t>' . $safeTitle . '</w:t></w:r></w:p>';
        $bodyXml .= '<w:p><w:pPr><w:jc w:val="center"/></w:pPr><w:r><w:rPr><w:i/><w:sz w:val="20"/><w:color w:val="713F12"/></w:rPr><w:t>Gerado por Conversor Bananal 🍌 em ' . date('d/m/Y H:i') . '</w:t></w:r></w:p>';
        $bodyXml .= '<w:p><w:r><w:t></w:t></w:r></w:p>';

        foreach ($paragraphs as $p) {
            $text = trim((string)$p);
            if ($text === '') {
                $bodyXml .= '<w:p><w:r><w:t></w:t></w:r></w:p>';
                continue;
            }

            $safeText = htmlspecialchars($text, ENT_XML1, 'UTF-8');

            // Detecta se é cabeçalho ou subtítulo
            if (str_starts_with($text, '# ') || str_starts_with($text, '## ')) {
                $cleanHeading = htmlspecialchars(ltrim($text, '# '), ENT_XML1, 'UTF-8');
                $bodyXml .= '<w:p><w:pPr><w:pStyle w:val="Heading2"/></w:pPr><w:r><w:rPr><w:b/><w:sz w:val="28"/><w:color w:val="EAB308"/></w:rPr><w:t>' . $cleanHeading . '</w:t></w:r></w:p>';
            } elseif (preg_match('/^\[\d{2}:\d{2}(:\d{2})?\]/', $text)) {
                // Formato de transcrição com minutagem [00:15] Fala...
                $bodyXml .= '<w:p><w:r><w:rPr><w:b/><w:color w:val="CA8A04"/></w:rPr><w:t>' . $safeText . '</w:t></w:r></w:p>';
            } else {
                $bodyXml .= '<w:p><w:pPr><w:spacing w:line="276" w:lineRule="auto"/></w:pPr><w:r><w:rPr><w:sz w:val="24"/></w:rPr><w:t xml:space="preserve">' . $safeText . '</w:t></w:r></w:p>';
            }
        }

        $documentXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<w:document xmlns:w="http://schemas.openxmlformats.org/wordprocessingml/2006/main">' .
            '<w:body>' . $bodyXml . '<w:sectPr><w:pgSz w:w="11906" w:h="16838"/><w:pgMar w:top="1440" w:right="1440" w:bottom="1440" w:left="1440"/></w:sectPr></w:body></w:document>';

        $contentTypesXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types">' .
            '<Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/>' .
            '<Default Extension="xml" ContentType="application/xml"/>' .
            '<Override PartName="/word/document.xml" ContentType="application/vnd.openxmlformats-officedocument.wordprocessingml.document.main+xml"/>' .
            '</Types>';

        $relsXml = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>' .
            '<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships">' .
            '<Relationship Id="rId1" Type="http://schemas.openxmlformats.org/officeDocument/2006/relationships/officeDocument" Target="word/document.xml"/>' .
            '</Relationships>';

        // Empacota em formato ZIP padrão (extensão .docx)
        $zip = new ZipArchive();
        if ($zip->open($outputPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            // Se ZipArchive falhar, fallback grava arquivo de texto formatado
            return file_put_contents($outputPath, $title . "\n\n" . (is_array($content) ? implode("\n", $content) : $content)) !== false;
        }

        $zip->addEmptyDir('_rels');
        $zip->addEmptyDir('word');
        $zip->addFromString('[Content_Types].xml', $contentTypesXml);
        $zip->addFromString('_rels/.rels', $relsXml);
        $zip->addFromString('word/document.xml', $documentXml);
        $zip->close();

        return file_exists($outputPath);
    }
}
