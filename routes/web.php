<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;
use Trianity\LaravelPdfViewer\Http\Controllers\PdfStreamController;

Route::get('/documents/{document}', PdfStreamController::class)
    ->name((string) config('pdf-viewer.route_name', 'pdf-viewer.stream'));
