# Changelog

All notable changes to this project will be documented in this file.

## [1.1.0] - 2024-05-01
### Added
- Added maskable icon support for better home screen experience
- Added purpose field to icons in manifest.json
- Added scope field to manifest.json for better PWA control
- Added notification settings to the PWA configuration
- Added PWA installation prompt with customizable settings
- Added command-line tool `cms:pwa:publish` to publish PWA assets
- Added multilingual support with translations for 8 languages (ar, bn, es, fr, hi, id, tr, vi)

### Fixed
- Fixed service worker to prevent caching in admin panel using pattern-based detection
- Fixed chrome-extension URL caching error in service worker
- Fixed null name issue in manifest.json
- Fixed security issue by not exposing admin path in public JavaScript
- Improved error handling in PublishPwaAssets class
- Improved service worker caching strategy with better error handling

### Changed
- Renamed variables from `isInAdmin` to `isCacheIgnored` for better semantics
- Removed comments from public-facing files for security
- Improved service worker to use skipWaiting() for immediate activation
- Added clients.claim() for better control of service worker

## [1.0.0] - 2024-04-17
- Initial release
- Add Progressive Web App (PWA) support for all themes
- Support customizable PWA settings
- Add service worker for offline functionality
- Generate PWA icons from site logo
