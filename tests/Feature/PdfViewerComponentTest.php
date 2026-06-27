<?php

declare(strict_types=1);

use Livewire\Livewire;
use Trianity\LaravelPdfViewer\Livewire\PdfViewer;

test('component renders signed url', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
    ])
        ->assertSee('/pdf-viewer/documents/document-1', false)
        ->assertSee('signature=', false);
});
