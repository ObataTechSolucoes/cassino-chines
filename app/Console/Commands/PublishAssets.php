<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishAssets extends Command
{
    protected $signature = 'app:publish-assets {--force : Overwrite existing files}';
    protected $description = 'Publish resources images and assets to public/assets (non-destructive by default)';

    public function handle(): int
    {
        $force = (bool) $this->option('force');

        // Map of source => destination
        $pairs = [
            resource_path('images') => public_path('assets/images'),
            resource_path('assets/images') => public_path('assets/images'),
            resource_path('assets/css') => public_path('assets/css'),
            resource_path('assets/js') => public_path('assets/js'),
            resource_path('assets/fonts') => public_path('assets/fonts'),
            resource_path('assets/webfonts') => public_path('assets/webfonts'),
            resource_path('assets/music') => public_path('assets/music'),
        ];

        $this->info('Publishing assets... (force: ' . ($force ? 'yes' : 'no') . ')');

        foreach ($pairs as $src => $dst) {
            if (!is_dir($src)) {
                $this->line('Skip (missing): ' . $src);
                continue;
            }
            $this->line('From: ' . $src);
            $this->line('To:   ' . $dst);
            $this->publishDirectory($src, $dst, $force);
        }

        $this->info('Done publishing assets.');
        return self::SUCCESS;
    }

    private function publishDirectory(string $source, string $target, bool $force = false): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $relative = ltrim(str_replace($source, '', $item->getPathname()), DIRECTORY_SEPARATOR);
            $destPath = $target . DIRECTORY_SEPARATOR . $relative;

            if ($item->isDir()) {
                if (!is_dir($destPath)) {
                    mkdir($destPath, 0755, true);
                }
                continue;
            }

            // Ensure directory exists
            $destDir = dirname($destPath);
            if (!is_dir($destDir)) {
                mkdir($destDir, 0755, true);
            }

            if (file_exists($destPath) && !$force) {
                // Skip if identical size and mtime newer or equal
                $srcSize = filesize($item->getPathname());
                $dstSize = filesize($destPath);
                if ($srcSize === $dstSize) {
                    continue;
                }
            }

            copy($item->getPathname(), $destPath);
            $this->line('Copied: ' . $relative);
        }
    }
}
