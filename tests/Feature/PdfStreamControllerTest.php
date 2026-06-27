<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Tests\Feature;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Trianity\LaravelPdfViewer\Contracts\PdfDocumentResolver;
use Trianity\LaravelPdfViewer\Data\PdfDocument;
use Trianity\LaravelPdfViewer\Tests\Fixtures\FakePdfDocumentResolver;
use Trianity\LaravelPdfViewer\Tests\TestCase;

final class PdfStreamControllerTest extends TestCase
{
    public function test_signed_route_rejects_unsigned_requests(): void
    {
        $this->get('/pdf-viewer/documents/document-1')->assertForbidden();
    }

    public function test_resolver_is_called(): void
    {
        Storage::fake('pdfs');
        Storage::disk('pdfs')->put('document.pdf', $this->pdfContent());
        $resolver = $this->bindResolver(new PdfDocument('pdfs', 'document.pdf', 'document.pdf', 'application/pdf'));

        $this->get($this->signedUrl('document-1'))->assertOk();

        $this->assertSame(1, $resolver->calls);
    }

    public function test_missing_file_returns_404(): void
    {
        Storage::fake('pdfs');
        $this->bindResolver(new PdfDocument('pdfs', 'missing.pdf', 'missing.pdf', 'application/pdf'));

        $this->get($this->signedUrl('missing-document'))->assertNotFound();
    }

    public function test_non_pdf_mime_is_rejected(): void
    {
        Storage::fake('pdfs');
        Storage::disk('pdfs')->put('document.txt', 'not a pdf');
        $this->bindResolver(new PdfDocument('pdfs', 'document.txt', 'document.txt', 'text/plain'));

        $this->get($this->signedUrl('document-1'))->assertStatus(415);
    }

    public function test_valid_pdf_streams_successfully(): void
    {
        Storage::fake('pdfs');
        Storage::disk('pdfs')->put('unsafe.pdf', $this->pdfContent());
        $this->bindResolver(new PdfDocument('pdfs', 'unsafe.pdf', '../unsafe name.pdf', 'application/pdf'));

        $response = $this->get($this->signedUrl('document-1'));

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/pdf');
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Content-Disposition', 'inline; filename="unsafe name.pdf"');
        $response->assertStreamedContent($this->pdfContent());
    }

    private function bindResolver(?PdfDocument $document): FakePdfDocumentResolver
    {
        $resolver = new FakePdfDocumentResolver($document);
        $this->app->instance(PdfDocumentResolver::class, $resolver);

        return $resolver;
    }

    private function signedUrl(string $documentId): string
    {
        return URL::temporarySignedRoute('pdf-viewer.stream', now()->addMinutes(5), [
            'document' => $documentId,
        ]);
    }

    private function pdfContent(): string
    {
        return "%PDF-1.4\n1 0 obj\n<< /Type /Catalog >>\nendobj\ntrailer\n<< /Root 1 0 R >>\n%%EOF\n";
    }
}
