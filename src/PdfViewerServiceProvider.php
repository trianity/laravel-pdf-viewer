<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;
use Livewire\Livewire;
use Trianity\LaravelPdfViewer\Livewire\PdfViewer;

final class PdfViewerServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__.'/../config/pdf-viewer.php', 'pdf-viewer');
    }

    public function boot(): void
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'pdf-viewer');

        Route::prefix((string) config('pdf-viewer.route_prefix', 'pdf-viewer'))
            ->middleware((array) config('pdf-viewer.route_middleware', ['web', 'signed']))
            ->group(__DIR__.'/../routes/web.php');

        Livewire::component((string) config('pdf-viewer.livewire_component', 'pdf-viewer'), PdfViewer::class);

        $this->publishes([
            __DIR__.'/../config/pdf-viewer.php' => config_path('pdf-viewer.php'),
        ], 'pdf-viewer-config');

        $this->publishes([
            __DIR__.'/../resources/js/pdf-viewer.js' => resource_path('js/vendor/pdf-viewer.js'),
        ], 'pdf-viewer-assets');

        $this->publishes([
            __DIR__.'/../resources/views' => resource_path('views/vendor/pdf-viewer'),
        ], 'pdf-viewer-views');
    }
}
