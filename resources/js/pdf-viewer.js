import * as pdfjsLib from 'pdfjs-dist';
import workerUrl from 'pdfjs-dist/build/pdf.worker.mjs?url';

pdfjsLib.GlobalWorkerOptions.workerSrc = workerUrl;

const MIN_SCALE = 0.5;
const MAX_SCALE = 3;
const SCALE_STEP = 0.25;

class PdfViewer {
    constructor(element) {
        this.element = element;
        this.canvas = element.querySelector('[data-pdf-canvas]');
        this.context = this.canvas.getContext('2d');
        this.loading = element.querySelector('[data-pdf-loading]');
        this.error = element.querySelector('[data-pdf-error]');
        this.currentPageEl = element.querySelector('[data-pdf-current-page]');
        this.totalPagesEl = element.querySelector('[data-pdf-total-pages]');
        this.controls = [...element.querySelectorAll('[data-pdf-action]')];

        this.viewportContainer = element.querySelector('[data-pdf-viewport]');

        this.pdf = null;
        this.currentPage = Math.max(1, Number.parseInt(element.dataset.initialPage || '1', 10));

        // Ez innentől nem abszolút PDF.js scale, hanem user zoom szorzó.
        this.scale = 1;

        this.rendering = false;
        this.pendingPage = null;
        this.resizeObserver = null;
        this.resizeTimer = null;
    }

    async init() {
        this.bindControls();
        this.bindResizeObserver();
        this.setLoading(true);

        try {
            this.pdf = await pdfjsLib.getDocument({ url: this.element.dataset.pdfUrl }).promise;
            this.currentPage = Math.min(this.currentPage, this.pdf.numPages);
            this.updateState();
            await this.renderPage(this.currentPage);
        } catch (error) {
            this.showError(error);
        } finally {
            this.setLoading(false);
        }
    }

    bindControls() {
        this.controls.forEach((control) => {
            control.addEventListener('click', () => {
                const action = control.dataset.pdfAction;

                if (action === 'previous') {
                    this.queuePage(this.currentPage - 1);
                }

                if (action === 'next') {
                    this.queuePage(this.currentPage + 1);
                }

                if (action === 'zoom-out') {
                    this.zoom(-SCALE_STEP);
                }

                if (action === 'zoom-in') {
                    this.zoom(SCALE_STEP);
                }
            });
        });
    }

    bindResizeObserver() {
        if (!this.viewportContainer || !window.ResizeObserver) {
            return;
        }

        this.resizeObserver = new ResizeObserver(() => {
            window.clearTimeout(this.resizeTimer);

            this.resizeTimer = window.setTimeout(() => {
                if (this.pdf && !this.rendering) {
                    this.renderPage(this.currentPage);
                }
            }, 120);
        });

        this.resizeObserver.observe(this.viewportContainer);
    }

    async zoom(delta) {
        this.scale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, this.scale + delta));
        await this.renderPage(this.currentPage);
    }

    async queuePage(pageNumber) {
        if (!this.pdf || pageNumber < 1 || pageNumber > this.pdf.numPages) {
            return;
        }

        if (this.rendering) {
            this.pendingPage = pageNumber;
            return;
        }

        await this.renderPage(pageNumber);
    }

    getAvailableRenderSize() {
        const container = this.viewportContainer || this.canvas.parentElement;

        if (!container) {
            return {
                width: this.element.clientWidth,
                height: this.element.clientHeight,
            };
        }

        const styles = window.getComputedStyle(container);

        const paddingX =
            Number.parseFloat(styles.paddingLeft || '0') +
            Number.parseFloat(styles.paddingRight || '0');

        const paddingY =
            Number.parseFloat(styles.paddingTop || '0') +
            Number.parseFloat(styles.paddingBottom || '0');

        return {
            width: Math.max(1, container.clientWidth - paddingX),
            height: Math.max(1, container.clientHeight - paddingY),
        };
    }

    getFitScale(baseViewport) {
        const available = this.getAvailableRenderSize();

        const widthScale = available.width / baseViewport.width;
        const heightScale = available.height / baseViewport.height;

        // Alap fit: férjen be szélességben és magasságban is.
        // Így nem torzul, hanem a kisebb illesztési arány nyer.
        return Math.min(widthScale, heightScale);
    }

    async renderPage(pageNumber) {
        if (!this.pdf) {
            return;
        }

        this.rendering = true;
        this.setLoading(true);

        try {
            const page = await this.pdf.getPage(pageNumber);

            const baseViewport = page.getViewport({ scale: 1 });
            const fitScale = this.getFitScale(baseViewport);
            const renderScale = fitScale * this.scale;

            const viewport = page.getViewport({ scale: renderScale });
            const outputScale = window.devicePixelRatio || 1;

            this.canvas.width = Math.floor(viewport.width * outputScale);
            this.canvas.height = Math.floor(viewport.height * outputScale);

            this.canvas.style.width = `${Math.floor(viewport.width)}px`;
            this.canvas.style.height = `${Math.floor(viewport.height)}px`;

            this.context.setTransform(1, 0, 0, 1, 0, 0);
            this.context.clearRect(0, 0, this.canvas.width, this.canvas.height);

            await page.render({
                canvasContext: this.context,
                viewport,
                transform: outputScale !== 1 ? [outputScale, 0, 0, outputScale, 0, 0] : null,
            }).promise;

            this.currentPage = pageNumber;
            this.updateState();
        } catch (error) {
            this.showError(error);
        } finally {
            this.rendering = false;
            this.setLoading(false);

            if (this.pendingPage !== null) {
                const nextPage = this.pendingPage;
                this.pendingPage = null;
                await this.renderPage(nextPage);
            }
        }
    }

    updateState() {
        if (this.currentPageEl) {
            this.currentPageEl.textContent = String(this.currentPage);
        }

        if (this.totalPagesEl) {
            this.totalPagesEl.textContent = String(this.pdf ? this.pdf.numPages : 0);
        }

        this.controls.forEach((control) => {
            const action = control.dataset.pdfAction;

            control.disabled =
                !this.pdf ||
                (action === 'previous' && this.currentPage <= 1) ||
                (action === 'next' && this.currentPage >= this.pdf.numPages) ||
                (action === 'zoom-out' && this.scale <= MIN_SCALE) ||
                (action === 'zoom-in' && this.scale >= MAX_SCALE);
        });
    }

    setLoading(isLoading) {
        if (this.loading) {
            this.loading.classList.toggle('hidden', !isLoading);
            this.loading.classList.toggle('flex', isLoading);
        }
    }

    showError(error) {
        if (this.error) {
            this.error.textContent = error?.message || 'Unable to load this PDF.';
            this.error.classList.remove('hidden');
        }
    }
}

export function initPdfViewers(root = document) {
    root.querySelectorAll('[data-pdf-viewer]:not([data-pdf-ready])').forEach((element) => {
        element.dataset.pdfReady = 'true';
        new PdfViewer(element).init();
    });
}

document.addEventListener('DOMContentLoaded', () => initPdfViewers());
document.addEventListener('livewire:navigated', () => initPdfViewers());
