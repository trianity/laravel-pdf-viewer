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

test('component accepts explicit light theme', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => 'light',
    ])
        ->assertSet('theme', 'light')
        ->assertSee('data-pdf-viewer', false)
        ->assertSee('data-pdf-viewer-theme="light"', false)
        ->assertSee('data-pdf-viewer-toolbar', false)
        ->assertSee('[color-scheme:light]', false);
});

test('component accepts explicit soft theme', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => 'soft',
    ])
        ->assertSet('theme', 'soft')
        ->assertSee('data-pdf-viewer-theme="soft"', false)
        ->assertSee('[color-scheme:light]', false)
        ->assertSee('bg-white/90', false)
        ->assertSee('ring-slate-900/10', false);
});

test('component accepts explicit dark theme', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => 'dark',
    ])
        ->assertSet('theme', 'dark')
        ->assertSee('data-pdf-viewer-theme="dark"', false)
        ->assertSee('[color-scheme:dark]', false);
});

test('component falls back to auto for invalid theme values', function () {
    Livewire::test(PdfViewer::class, [
        'documentId' => 'document-1',
        'theme' => 'pastel',
    ])
        ->assertSet('theme', 'auto')
        ->assertSee('data-pdf-viewer-theme="auto"', false);
});
