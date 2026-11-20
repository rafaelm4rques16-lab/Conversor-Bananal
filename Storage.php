<?php
/**
 * Gerenciador de Armazenamento Seguro
 * Conversor Bananal Pro
 */

declare(strict_types=1);

class Storage
{
    private string $uploadDir;
    private string $convertedDir;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $this->uploadDir = $root . DIRECTORY_SEPARATOR . 'uploads';
        $this->convertedDir = $root . DIRECTORY_SEPARATOR . 'converted';

        $this->initDirs();
    }

    private function initDirs(): void
    {
        foreach ([$this->uploadDir, $this->convertedDir] as $dir) {
            if (!is_dir($dir)) {
                mkdir($dir, 0755, true);
            }
            $index = $dir . DIRECTORY_SEPARATOR . 'index.html';
            if (!file_exists($index)) {
                file_put_contents($index, '<!DOCTYPE html><html><head><title>403</title></head><body><h1>Acesso negado</h1></body></html>');
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

    public function generateToken(): string
    {
        return bin2hex(random_bytes(24));
    }

    public function createSafeName(string $ext): string
    {
        $cleanExt = strtolower(ltrim($ext, '.'));
        return 'bananal_' . bin2hex(random_bytes(10)) . '_' . time() . '.' . $cleanExt;
    }

    public static function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    public function cleanOld(int $maxSeconds = 7200): void
    {
        $now = time();
        foreach ([$this->uploadDir, $this->convertedDir] as $d) {
            $files = glob($d . DIRECTORY_SEPARATOR . '*');
            if ($files === false) continue;
            foreach ($files as $f) {
                if (is_file($f) && basename($f) !== 'index.html') {
                    if (($now - filemtime($f)) > $maxSeconds) {
                        @unlink($f);
                    }
                }
            }
        }
    }
}
