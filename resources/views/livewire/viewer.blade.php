@php
    $rootClasses = match ($theme) {
        'light', 'soft' => 'w-full overflow-hidden rounded border border-neutral-200 bg-white text-neutral-900 shadow-sm [color-scheme:light]',
        'dark' => 'w-full overflow-hidden rounded border border-neutral-800 bg-neutral-950 text-neutral-100 shadow-sm [color-scheme:dark]',
        default => 'w-full overflow-hidden rounded border border-neutral-200 bg-white text-neutral-900 shadow-sm',
    };

    $toolbarClasses = match ($theme) {
        'light' => 'flex h-12 items-center gap-2 border-b border-neutral-200 bg-white px-3 text-neutral-900 shadow-sm',
        'dark' => 'flex h-12 items-center gap-2 border-b border-white/10 bg-neutral-950 px-3 text-neutral-100',
        'soft' => 'flex h-12 items-center gap-2 border-b border-slate-200/80 bg-white/90 px-3 text-slate-800 shadow-sm ring-1 ring-slate-900/10 backdrop-blur',
        default => 'flex h-12 items-center gap-2 border-b border-neutral-200 bg-neutral-50 px-3 text-neutral-900 dark:border-neutral-800 dark:bg-neutral-950 dark:text-neutral-100',
    };

    $buttonClasses = match ($theme) {
        'light' => 'inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm text-neutral-900 shadow-sm hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40',
        'dark' => 'inline-flex h-8 w-8 items-center justify-center rounded border border-white/15 bg-white/10 text-sm text-neutral-100 hover:bg-white/15 disabled:cursor-not-allowed disabled:opacity-40',
        'soft' => 'inline-flex h-8 w-8 items-center justify-center rounded border border-slate-300 bg-white text-sm text-slate-900 shadow-sm ring-1 ring-slate-900/5 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40',
        default => 'inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm text-neutral-900 hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40 dark:border-white/15 dark:bg-white/10 dark:text-neutral-100 dark:hover:bg-white/15',
    };

    $pageCountClasses = match ($theme) {
        'light' => 'min-w-24 text-center text-sm tabular-nums text-neutral-700',
        'dark' => 'min-w-24 text-center text-sm tabular-nums text-neutral-200',
        'soft' => 'min-w-24 text-center text-sm tabular-nums text-slate-700',
        default => 'min-w-24 text-center text-sm tabular-nums text-neutral-700 dark:text-neutral-200',
    };
@endphp

<div
    class="{{ $rootClasses }}"
    data-pdf-viewer
    data-pdf-viewer-theme="{{ $theme }}"
    data-pdf-url="{{ $streamUrl }}"
    data-initial-page="{{ $initialPage }}"
    style="height: {{ $height }};"
    wire:ignore
>
    @if ($showToolbar)
        <div class="{{ $toolbarClasses }}" data-pdf-viewer-toolbar>
            <button
                type="button"
                class="{{ $buttonClasses }}"
                data-pdf-action="previous"
                aria-label="Previous page"
                disabled
            >
                &lt;
            </button>
            <button
                type="button"
                class="{{ $buttonClasses }}"
                data-pdf-action="next"
                aria-label="Next page"
                disabled
            >
                &gt;
            </button>
            <div class="{{ $pageCountClasses }}">
                <span data-pdf-current-page>{{ $initialPage }}</span>
                <span>/</span>
                <span data-pdf-total-pages>0</span>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button
                    type="button"
                    class="{{ $buttonClasses }}"
                    data-pdf-action="zoom-out"
                    aria-label="Zoom out"
                    disabled
                >
                    -
                </button>
                <button
                    type="button"
                    class="{{ $buttonClasses }}"
                    data-pdf-action="zoom-in"
                    aria-label="Zoom in"
                    disabled
                >
                    +
                </button>
            </div>
        </div>
    @endif

    <div
        class="relative flex min-h-0 flex-1 items-start justify-center overflow-auto bg-neutral-100 p-4"
        style="height: {{ $showToolbar ? 'calc(100% - 3rem)' : '100%' }};"
        data-pdf-viewport
    >
        <div class="absolute inset-0 hidden items-center justify-center bg-white/70 text-sm text-neutral-700" data-pdf-loading>
            Loading PDF
        </div>

        <div class="absolute inset-x-4 top-4 hidden rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" data-pdf-error></div>

        <canvas class="block max-w-full bg-white shadow" data-pdf-canvas></canvas>
    </div>
</div>
