@props([
    'documentId',
    'height' => '70vh',
    'initialPage' => 1,
    'showToolbar' => true,
    'theme' => 'auto',
])

@livewire(config('pdf-viewer.livewire_component', 'pdf-viewer'), [
    'documentId' => $documentId,
    'height' => $height,
    'initialPage' => (int) $initialPage,
    'showToolbar' => filter_var($showToolbar, FILTER_VALIDATE_BOOL),
    'theme' => $theme,
], key('pdf-viewer-' . $documentId))
