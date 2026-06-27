<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Trianity\LaravelPdfViewer\Contracts\PdfDocumentResolver;
use Trianity\LaravelPdfViewer\Data\PdfDocument;

final class PdfStreamController extends Controller
{
    public function __invoke(Request $request, PdfDocumentResolver $resolver, string $document): StreamedResponse
    {
        $pdf = $resolver->resolve($document);

        abort_unless($pdf instanceof PdfDocument, 404);
        $this->authorizeDocument($request, $document, $pdf);
        abort_unless(Storage::disk($pdf->disk)->exists($pdf->path), 404);
        abort_unless($this->isPdf($pdf), 415);

        $stream = Storage::disk($pdf->disk)->readStream($pdf->path);
        abort_unless(is_resource($stream), 404);

        return response()->stream(function () use ($stream): void {
            fpassthru($stream);
            fclose($stream);
        }, 200, [
            'Content-Type' => 'application/pdf',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="'.$this->safeFilename($pdf->filename).'"',
        ]);
    }

    private function authorizeDocument(Request $request, string $documentId, PdfDocument $document): void
    {
        $callback = config('pdf-viewer.authorize');

        if (is_callable($callback)) {
            abort_unless((bool) $callback($request, $documentId, $document), 403);

            return;
        }

        $gate = config('pdf-viewer.gate');

        if (is_string($gate) && $gate !== '') {
            abort_if(Gate::denies($gate, [$documentId, $document]), 403);
        }
    }

    private function isPdf(PdfDocument $document): bool
    {
        return strtolower(trim($document->mime)) === 'application/pdf';
    }

    private function safeFilename(string $filename): string
    {
        $filename = basename($filename);
        $filename = preg_replace('/[^A-Za-z0-9._ -]/', '_', $filename) ?: 'document.pdf';
        $filename = trim($filename, ". \t\n\r\0\x0B");

        if ($filename === '') {
            return 'document.pdf';
        }

        return str_ends_with(strtolower($filename), '.pdf') ? $filename : $filename.'.pdf';
    }
}
