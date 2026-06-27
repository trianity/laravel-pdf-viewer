<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Tests\Feature;

use Livewire\Livewire;
use Trianity\LaravelPdfViewer\Livewire\PdfViewer;
use Trianity\LaravelPdfViewer\Tests\TestCase;

final class PdfViewerComponentTest extends TestCase
{
    public function test_component_renders_signed_url(): void
    {
        Livewire::test(PdfViewer::class, [
            'documentId' => 'document-1',
        ])
            ->assertSee('/pdf-viewer/documents/document-1', false)
            ->assertSee('signature=', false);
    }
}
