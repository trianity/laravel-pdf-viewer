<div
    class="w-full overflow-hidden rounded border border-neutral-200 bg-white text-neutral-900 shadow-sm"
    data-pdf-viewer
    data-pdf-url="{{ $streamUrl }}"
    data-initial-page="{{ $initialPage }}"
    style="height: {{ $height }};"
    wire:ignore
>
    @if ($showToolbar)
        <div class="flex h-12 items-center gap-2 border-b border-neutral-200 bg-neutral-50 px-3">
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40"
                data-pdf-action="previous"
                aria-label="Previous page"
                disabled
            >
                &lt;
            </button>
            <button
                type="button"
                class="inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40"
                data-pdf-action="next"
                aria-label="Next page"
                disabled
            >
                &gt;
            </button>
            <div class="min-w-24 text-center text-sm tabular-nums text-neutral-700">
                <span data-pdf-current-page>{{ $initialPage }}</span>
                <span>/</span>
                <span data-pdf-total-pages>0</span>
            </div>
            <div class="ml-auto flex items-center gap-2">
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40"
                    data-pdf-action="zoom-out"
                    aria-label="Zoom out"
                    disabled
                >
                    -
                </button>
                <button
                    type="button"
                    class="inline-flex h-8 w-8 items-center justify-center rounded border border-neutral-300 bg-white text-sm hover:bg-neutral-100 disabled:cursor-not-allowed disabled:opacity-40"
                    data-pdf-action="zoom-in"
                    aria-label="Zoom in"
                    disabled
                >
                    +
                </button>
            </div>
        </div>
    @endif

    <div class="relative flex min-h-0 flex-1 items-start justify-center overflow-auto bg-neutral-100 p-4" style="height: {{ $showToolbar ? 'calc(100% - 3rem)' : '100%' }};">
        <div class="absolute inset-0 hidden items-center justify-center bg-white/70 text-sm text-neutral-700" data-pdf-loading>
            Loading PDF
        </div>
        <div class="absolute inset-x-4 top-4 hidden rounded border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-800" data-pdf-error></div>
        <canvas class="max-w-full bg-white shadow" data-pdf-canvas></canvas>
    </div>
</div>
