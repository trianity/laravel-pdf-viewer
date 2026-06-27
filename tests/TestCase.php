<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Livewire\LivewireServiceProvider;
use Trianity\LaravelPdfViewer\PdfViewerServiceProvider;

abstract class TestCase extends Orchestra
{
    protected function getPackageProviders($app): array
    {
        return [
            LivewireServiceProvider::class,
            PdfViewerServiceProvider::class,
        ];
    }

    protected function defineEnvironment($app): void
    {
        $app['config']->set('app.key', 'base64:WQHpLX+Yxlv7+71hl7U3M+ijgN68JAtfuqC2a4aG7xw=');
        $app['config']->set('app.url', 'http://localhost');
        $app['config']->set('filesystems.disks.pdfs', [
            'driver' => 'local',
            'root' => storage_path('app/pdfs'),
        ]);
    }
}
