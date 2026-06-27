<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Data;

final readonly class PdfDocument
{
    public function __construct(
        public string $disk,
        public string $path,
        public string $filename,
        public string $mime,
    ) {}
}
