# Trianity Laravel PDF Viewer

A secure PDF.js based PDF viewer for Laravel 13+, Livewire 4 and Tailwind CSS 4.

This package provides a reusable PDF viewer component without relying on WordPress, Joomla or remote PDF URL loading.

## Features

- PDF.js based rendering
- Laravel signed route support
- document ID based loading
- Livewire 4 component
- Tailwind CSS 4 friendly markup
- previous / next page navigation
- zoom controls
- responsive toolbar
- no arbitrary remote PDF URL loading

## Installation

```bash
composer require trianity/laravel-pdf-viewer
npm install pdfjs-dist
```

Publish configuration and assets when you need to customize them:

```bash
php artisan vendor:publish --tag=pdf-viewer-config
php artisan vendor:publish --tag=pdf-viewer-assets
```

Import the viewer asset from your Vite entrypoint:

```js
import './vendor/pdf-viewer.js';
```

## Resolver

The package never accepts arbitrary PDF URLs. Bind `PdfDocumentResolver` in your application and resolve trusted document IDs to storage metadata:

```php
use Trianity\LaravelPdfViewer\Contracts\PdfDocumentResolver;
use Trianity\LaravelPdfViewer\Data\PdfDocument;

$this->app->bind(PdfDocumentResolver::class, function () {
    return new class implements PdfDocumentResolver {
        public function resolve(string $documentId): ?PdfDocument
        {
            // Look up the document in your own database.
            return new PdfDocument(
                disk: 'private',
                path: 'documents/example.pdf',
                filename: 'example.pdf',
                mime: 'application/pdf',
            );
        }
    };
});
```

## Usage

```blade
<x-pdf-viewer::viewer
    document-id="invoice-123"
    height="75vh"
    :initial-page="1"
    :show-toolbar="true"
/>
```

You can also render the Livewire component directly:

```blade
<livewire:pdf-viewer document-id="invoice-123" height="75vh" />
```

## Authorization

Configure `config/pdf-viewer.php` with either a Gate ability or an authorization callback. The callback receives the request, document ID and resolved `PdfDocument`.

```php
'gate' => 'view-pdf-document',
```

or:

```php
'authorize' => fn ($request, string $documentId, $document) => $request->user()?->can('view', $documentId),
```

## Security Model

- Documents are streamed only through temporary signed Laravel routes.
- Stream requests use document IDs, not remote URLs.
- The resolver must return disk, path, filename and MIME type.
- Missing files return 404.
- Non-PDF MIME types are rejected.
- Stream responses set `Content-Type: application/pdf`, `X-Content-Type-Options: nosniff` and inline `Content-Disposition`.
