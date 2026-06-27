<?php

declare(strict_types=1);

namespace Trianity\LaravelPdfViewer\Livewire;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\URL;
use Livewire\Component;

final class PdfViewer extends Component
{
    public string $documentId;

    public string $height = '70vh';

    public int $initialPage = 1;

    public bool $showToolbar = true;

    public string $streamUrl = '';

    public function mount(
        string $documentId,
        string $height = '70vh',
        int $initialPage = 1,
        bool $showToolbar = true,
    ): void {
        $this->documentId = $documentId;
        $this->height = $this->sanitizeHeight($height);
        $this->initialPage = max(1, $initialPage);
        $this->showToolbar = $showToolbar;
        $this->streamUrl = URL::temporarySignedRoute(
            (string) config('pdf-viewer.route_name', 'pdf-viewer.stream'),
            now()->addMinutes((int) config('pdf-viewer.signature_expires_in', 5)),
            ['document' => $this->documentId],
        );
    }

    public function render(): View
    {
        return view()->file(__DIR__.'/../../resources/views/livewire/viewer.blade.php');
    }

    private function sanitizeHeight(string $height): string
    {
        $height = trim($height);

        if (preg_match('/^\d+(?:\.\d+)?(?:px|rem|em|vh|vw|vmin|vmax|%)$/', $height) === 1) {
            return $height;
        }

        return '70vh';
    }
}
