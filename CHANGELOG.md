# Changelog

## v0.1.3 - 2026-06-28

### Documentation

- Added Tailwind CSS 4 `@source` setup instructions for scanning package Blade views and PHP files.

### Fixed

- Documented the required Tailwind CSS 4 source configuration to prevent missing viewer interface styles in host applications.

===

## v0.1.2 - Viewer Theme Isolation

### Changed

- Improved explicit `light`, `soft` and `dark` viewer themes with color-scheme isolation.
- Strengthened `soft` toolbar contrast for light and pastel host page backgrounds.

### Fixed

- Fixed viewer controls becoming hard to see when the browser or operating system is in dark mode while the viewer is configured with a light or soft interface theme.

===

## v0.1.1 - Viewer Theme Option

### Added

- Explicit `auto`, `light`, `dark` and `soft` viewer interface themes.
- Stable `data-pdf-viewer-theme` attribute for debugging and tests.

===

## v0.1.0 - Initial Light Release

### Added

- Secure PDF.js based PDF viewer for Laravel 13, Livewire 4 and Tailwind CSS 4.
- Document ID based PDF loading through a configurable resolver.
- Temporary signed route based PDF streaming.
- Safe inline PDF response headers.
- Livewire viewer component.
- Vite-compatible PDF.js asset with worker configuration.
- Laravel Pint, Larastan and Pest quality tooling.
- Feature tests for stream security and viewer behavior.
