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

test('component sanitizes unsafe height values', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'height' => '1px; background: red',
    ])
        ->assertSet('height', '70vh');
});

test('component defaults theme to auto', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
    ])
        ->assertSet('theme', 'auto')
        ->assertSee('data-pdf-viewer-theme="auto"', false);
});

test('component accepts valid theme values', function (string $theme) {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => $theme,
    ])
        ->assertSet('theme', $theme)
        ->assertSee('data-pdf-viewer-theme="'.$theme.'"', false);
})->with(['light', 'dark', 'soft']);

test('component falls back to auto for invalid theme values', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => 'pastel',
    ])
        ->assertSet('theme', 'auto')
        ->assertSee('data-pdf-viewer-theme="auto"', false);
});
