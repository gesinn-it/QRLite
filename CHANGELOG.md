# Changelog

All notable changes to this project will be documented in this file.
This project adheres to [Semantic Versioning](https://semver.org/) and
[Keep a Changelog](https://keepachangelog.com/en/1.1.0/).

## [Unreleased]

## [3.0.0] - 2026-11-22

Raises the minimum requirements: MediaWiki 1.43 or later and endroid/qr-code 6.x are now required.

### Breaking Changes
- Require MediaWiki 1.43 or later; MediaWiki 1.39 is no longer supported [`59932bb`](https://github.com/gesinn-it/QRLite/commit/59932bb)
- Require endroid/qr-code 6.x instead of 5.x [`59932bb`](https://github.com/gesinn-it/QRLite/commit/59932bb)

### Fixed
- Fix class loading conflict with other extensions that require endroid/qr-code 6.x, such as OATHAuth [`59932bb`](https://github.com/gesinn-it/QRLite/commit/59932bb)
- Declare the real minimum MediaWiki version (1.43) in the extension metadata [`5659073`](https://github.com/gesinn-it/QRLite/commit/5659073)

[Unreleased]: https://github.com/gesinn-it/QRLite/compare/3.0.0...HEAD
[3.0.0]: https://github.com/gesinn-it/QRLite/compare/2.0.0...3.0.0
