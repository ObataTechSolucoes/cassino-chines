<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {

        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        $this->routes(function () {
            // API routes: load main file and all files in routes/api recursively
            Route::middleware('api')
                ->prefix('api')
                ->group(function () {
                    require base_path('routes/api.php');
                    $this->loadRoutesFromDirectory(base_path('routes/api'));
                });

            // WEB routes: load main file and all files in routes/web (layouts/app last)
            Route::middleware('web')
                ->group(function () {
                    require base_path('routes/web.php');
                    $this->loadRoutesFromDirectory(base_path('routes/web'), [
                        'exclude' => [base_path('routes/web/layouts/app.php')],
                    ]);
                    $fallback = base_path('routes/web/layouts/app.php');
                    if (file_exists($fallback)) {
                        require $fallback;
                    }
                });
        });
    }

    /**
     * Require all route files in a directory (recursively).
     *
     * @param string $directory
     * @param array $options ['exclude' => [paths]]
     * @return void
     */
    protected function loadRoutesFromDirectory(string $directory, array $options = []): void
    {
        if (!is_dir($directory)) {
            return;
        }

        $exclude = $options['exclude'] ?? [];
        $rii = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($directory));
        foreach ($rii as $file) {
            if ($file->isDir()) {
                continue;
            }
            if ($file->getExtension() !== 'php') {
                continue;
            }
            $path = $file->getPathname();
            if (in_array($path, $exclude, true)) {
                continue;
            }
            require $path;
        }
    }
}
