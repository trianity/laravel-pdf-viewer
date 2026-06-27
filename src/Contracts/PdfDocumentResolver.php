<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Contracts;

use Trianity\LaravelPdfViewer\Data\PdfDocument;

interface PdfDocumentResolver
{
    public function resolve(string $documentId): ?PdfDocument;
}
