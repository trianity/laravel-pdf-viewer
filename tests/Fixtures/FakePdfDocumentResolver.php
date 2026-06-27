<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Tests\Fixtures;

use Trianity\LaravelPdfViewer\Contracts\PdfDocumentResolver;
use Trianity\LaravelPdfViewer\Data\PdfDocument;

final class FakePdfDocumentResolver implements PdfDocumentResolver
{
    public int $calls = 0;

    public function __construct(
        private readonly ?PdfDocument $document,
    ) {}

    public function resolve(string $documentId): ?PdfDocument
    {
        $this->calls++;

        return $this->document;
    }
}
