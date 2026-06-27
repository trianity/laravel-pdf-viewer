<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;

use function Pest\Laravel\get;

use Trianity\LaravelPdfViewer\Contracts\PdfDocumentResolver;
use Trianity\LaravelPdfViewer\Data\PdfDocument;
use Trianity\LaravelPdfViewer\Tests\Fixtures\FakePdfDocumentResolver;

test('signed route rejects unsigned requests', function () {
    get('/pdf-viewer/documents/document-1')->assertForbidden();
});

test('resolver is called', function () {
    Storage::fake('pdfs');
    Storage::disk('pdfs')->put('document.pdf', pdfContent());
    $resolver = bindResolver(new PdfDocument('pdfs', 'document.pdf', 'document.pdf', 'application/pdf'));

    get(signedUrl('document-1'))->assertOk();

    expect($resolver->calls)->toBe(1);
});

test('missing file returns 404', function () {
    Storage::fake('pdfs');
    bindResolver(new PdfDocument('pdfs', 'missing.pdf', 'missing.pdf', 'application/pdf'));

    get(signedUrl('missing-document'))->assertNotFound();
});

test('non pdf mime is rejected', function () {
    Storage::fake('pdfs');
    Storage::disk('pdfs')->put('document.txt', 'not a pdf');
    bindResolver(new PdfDocument('pdfs', 'document.txt', 'document.txt', 'text/plain'));

    get(signedUrl('document-1'))->assertStatus(415);
});

test('valid pdf streams successfully', function () {
    Storage::fake('pdfs');
    Storage::disk('pdfs')->put('unsafe.pdf', pdfContent());
    bindResolver(new PdfDocument('pdfs', 'unsafe.pdf', '../unsafe name.pdf', 'application/pdf'));

    $response = get(signedUrl('document-1'));

    $response->assertOk();
    $response->assertHeader('Content-Type', 'application/pdf');
    $response->assertHeader('X-Content-Type-Options', 'nosniff');
    $response->assertHeader('Content-Disposition', 'inline; filename="unsafe name.pdf"');
    $response->assertStreamedContent(pdfContent());
});

function bindResolver(?PdfDocument $document): FakePdfDocumentResolver
{
    $resolver = new FakePdfDocumentResolver($document);
    app()->instance(PdfDocumentResolver::class, $resolver);

    return $resolver;
}

function signedUrl(string $documentId): string
{
    return URL::temporarySignedRoute('pdf-viewer.stream', now()->addMinutes(5), [
        'document' => $documentId,
    ]);
}

function pdfContent(): string
{
    return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n";
}
