<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class ClearAppCaches extends Command
{
    protected $signature = 'app:clear-caches {--verbose}';
    protected $description = 'Clear application caches (config, route, view, app) and optimize:clear';

    public function handle(): int
    {
        $this->info('Clearing and optimizing caches...');

        try {
            Artisan::call('optimize:clear');
            $this->line(Artisan::output());

            // Explicit clears (idempotent)
            Artisan::call('config:clear');
            Artisan::call('route:clear');
            Artisan::call('view:clear');
            Artisan::call('cache:clear');

            if ($this->option('verbose')) {
                $this->line(Artisan::output());
            }

            $this->info('Done. All caches cleared.');
            return self::SUCCESS;
        } catch (\Throwable $e) {
            $this->error('Failed to clear caches: ' . $e->getMessage());
            return self::FAILURE;
        }
    }
}

