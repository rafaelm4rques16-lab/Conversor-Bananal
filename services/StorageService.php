<?php
/**
 * Serviço de Gerenciamento de Arquivos e Diretórios
 * Conversor Bananal
 */

declare(strict_types=1);

class StorageService
{
    private string $uploadDir;
    private string $convertedDir;

    public function __construct(?string $baseDir = null)
    {
        $root = $baseDir ?? dirname(__DIR__);
        $this->uploadDir = $root . DIRECTORY_SEPARATOR . 'uploads';
        $this->convertedDir = $root . DIRECTORY_SEPARATOR . 'converted';

        $this->ensureDirectories();
    }

    /**
     * Garante que os diretórios necessários existam e contenham proteção contra listagem direta
     */
    private function ensureDirectories(): void
    {
        foreach ([$this->uploadDir, $this->convertedDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            // Cria um index.html em branco para impedir Directory Browsing
            $indexFile = $dir . DIRECTORY_SEPARATOR . 'index.html';
            if (!file_exists($indexFile)) {
                file_put_contents($indexFile, '<!DOCTYPE html><html><head><title>403 Forbidden</title></head><body><h1>Acesso negado</h1></body></html>');
            }
        }
    }

    public function getUploadDir(): string
    {
        return $this->uploadDir;
    }

    public function getConvertedDir(): string
    {
        return $this->convertedDir;
    }

    /**
     * Gera um nome de arquivo criptograficamente aleatório e seguro
     */
    public function generateSafeFilename(string $extension): string
    {
        $extension = strtolower(ltrim($extension, '.'));
        return bin2hex(random_bytes(16)) . '_' . time() . '.' . $extension;
    }

    /**
     * Remove arquivos temporários antigos (mais de 2 horas) para poupar disco
     */
    public function cleanOldFiles(int $maxAgeSeconds = 7200): void
    {
        $now = time();
        foreach ([$this->uploadDir, $this->convertedDir] as $dir) {
            $files = glob($dir . DIRECTORY_SEPARATOR . '*');
            if ($files === false) continue;
            foreach ($files as $file) {
                if (is_file($file) && basename($file) !== 'index.html') {
                    if (($now - filemtime($file)) > $maxAgeSeconds) {
                        @unlink($file);
                    }
                }
            }
        }
    }

    /**
     * Formata bytes para representação legível (KB, MB, GB)
     */
    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}
