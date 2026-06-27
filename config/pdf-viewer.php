<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | Streaming Route
    |--------------------------------------------------------------------------
    */
    'route_prefix' => 'pdf-viewer',
    'route_name' => 'pdf-viewer.stream',
    'route_middleware' => ['web', 'signed'],
    'signature_expires_in' => 5,

    /*
    |--------------------------------------------------------------------------
    | Authorization
    |--------------------------------------------------------------------------
    |
    | Set a Gate ability name, or provide an authorize callback. The callback
    | receives: Request $request, string $documentId, PdfDocument $document.
    |
    */
    'gate' => null,
    'authorize' => null,

    /*
    |--------------------------------------------------------------------------
    | Livewire
    |--------------------------------------------------------------------------
    */
    'livewire_component' => 'pdf-viewer',
];
