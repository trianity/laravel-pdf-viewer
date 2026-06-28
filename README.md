# Trianity Laravel PDF Viewer

A secure PDF.js based PDF viewer for Laravel 13+, Livewire 4 and Tailwind CSS 4.

This package provides a reusable PDF viewer component without relying on WordPress, Joomla or remote PDF URL loading.

## Features

* PDF.js based rendering
* Laravel signed route support
* Document ID based loading
* Livewire 4 component
* Tailwind CSS 4 friendly markup
* Previous / next page navigation
* Zoom controls
* Responsive toolbar
* No arbitrary remote PDF URL loading
* No `/pdf?url=...` style API

## Requirements

* PHP 8.3+
* Laravel 13+
* Livewire 4+
* Tailwind CSS 4 compatible frontend
* Vite
* `pdfjs-dist`

## Installation

```bash
composer require trianity/laravel-pdf-viewer
npm install pdfjs-dist
```

Publish the configuration when you need to customize the package:

```bash
php artisan vendor:publish --tag=pdf-viewer-config
```

Publish the viewer asset when you want to include or customize the JavaScript entry manually:

```bash
php artisan vendor:publish --tag=pdf-viewer-assets
```

Import the viewer asset from your Vite entrypoint.

For example, if the published asset is located at `resources/js/vendor/pdf-viewer.js`:

```js
import './vendor/pdf-viewer.js';
```

Then rebuild your frontend assets:

```bash
npm run build
```

## Configuration

The package configuration is published to:

```txt
config/pdf-viewer.php
```

Important options include:

```php
return [
    'route_prefix' => 'pdf-viewer',

    'route_name' => 'pdf-viewer.',

    'middleware' => ['web', 'signed'],

    'signed_url_ttl' => 10,

    'gate' => null,

    'authorize' => null,
];
```

Use either a Gate ability or an authorization callback when your application needs explicit access control.

## Resolver

The package never accepts arbitrary PDF URLs.

Instead, bind `PdfDocumentResolver` in your application and resolve trusted document IDs to storage metadata:

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

A typical application resolver may query an Eloquent model, check ownership or tenancy boundaries, and then return the storage disk, path, safe filename and MIME type.

## Usage

Use the Blade component:

```blade
<x-pdf-viewer::viewer
    document-id="invoice-123"
    height="75vh"
    theme="soft"
    :initial-page="1"
    :show-toolbar="true"
/>
```

You can also render the Livewire component directly:

```blade
<livewire:pdf-viewer document-id="invoice-123" height="75vh" />
```

The `height` option accepts simple CSS length values such as:

```txt
600px
40rem
75vh
100%
```

Invalid values fall back to `70vh`.

### Viewer theme

The `theme` option controls only the viewer interface and toolbar. It does not change, invert or recolor the rendered PDF page.

Supported values:

* `auto` - default; follows the host application's light/dark mode through Tailwind `dark:` variants.
* `light` - always uses a light toolbar/interface.
* `dark` - always uses a dark toolbar/interface.
* `soft` - uses a subtle translucent light interface for light or pastel page backgrounds.

Explicit `light`, `soft` and `dark` themes are isolated from the browser/OS color scheme as far as possible. This helps keep the viewer controls readable when the host page uses a different visual background than the global dark/light mode.

The `soft` theme is recommended for light or pastel landing pages. The theme option affects only the viewer interface; it does not modify the rendered PDF page or canvas.

Invalid values fall back to `auto`.

## Tailwind CSS 4

If your application uses Tailwind CSS 4, make sure Tailwind scans the package Blade views.

Add this to your application CSS entrypoint, for example `resources/css/app.css`:

```css
@source "../../vendor/trianity/laravel-pdf-viewer/resources/views/**/*.blade.php";
@source "../../vendor/trianity/laravel-pdf-viewer/src/**/*.php";
```
Then rebuild your frontend assets:
```bash
npm run build
```

## Authorization

Configure `config/pdf-viewer.php` with either a Gate ability or an authorization callback.

Using a Gate ability:

```php
'gate' => 'viewPdfDocument',
```

Using an authorization callback:

```php
'authorize' => fn ($request, string $documentId, $document) => $request->user()?->can('viewPdfDocument', $document),
```

The callback receives the resolved PdfDocument, so your application can authorize access using your own Gate, Policy, tenancy or ownership rules.

If neither gate nor authorize is configured, the package assumes that your resolver only returns documents that the current request is allowed to access.

## Security Model

* Documents are streamed only through temporary signed Laravel routes.
* Stream requests use document IDs, not remote URLs.
* The package does not provide arbitrary remote PDF proxying.
* The resolver must return disk, path, filename and MIME type.
* Missing files return 404.
* Non-PDF MIME types are rejected.
* Stream responses set:

  * `Content-Type: application/pdf`
  * `X-Content-Type-Options: nosniff`
  * inline `Content-Disposition`
* Filenames are sanitized before being written to response headers.
* Host applications remain responsible for document ownership, tenancy and business-level authorization.

## Testing

The package uses Pest, Orchestra Testbench, Laravel Pint and Larastan.

```bash
composer format
composer analyse
composer test
composer quality
```

The quality command runs formatting, static analysis and the test suite.

## Versioning

This package follows semantic versioning.

The current Light/free version focuses on a secure PDF.js based viewer.

The following features are intentionally not included in the first release:

* Flipbook animation
* Thumbnail sidebar
* PDF text search
* Annotations
* Watermarking
* Analytics
* Pro/commercial features

## License

The MIT License.
