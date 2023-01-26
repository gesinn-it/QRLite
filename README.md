[![CI](https://github.com/gesinn-it/QRLite/actions/workflows/ci.yml/badge.svg)](https://github.com/gesinn-it/QRLite/actions/workflows/ci.yml)

# QRLite
QRLite is a MediaWiki extension that generates QR Codes in SVG or PNG format on-the-fly.
The extension is very lightweight, as it does not upload or manage the images within MediaWiki.

## Documentation
* Documentation can be found at [mediawiki.org/wiki/Extension:QRLite](https://www.mediawiki.org/wiki/Extension:QRLite)

## Built With
* [PHP QR Code](https://sourceforge.net/projects/phpqrcode/) - Create QR Codes in PHP

## Contributing
We are happy to receive pull requests. Before you commit your changes, make sure all tests are passing (see below).

## Testing
Local testing and CI are supported by Docker and Make. Ensure, both tools are installed (`docker --version`, `make --version`).

To run test locally:
`> make ci`

## Versioning
We use [SemVer](http://semver.org/) for versioning. For the versions available, see the [tags on this repository](https://github.com/gesinn-it/QRLite/tags).

## Author(s)
* The [**gesinn**.it](https://gesinn.it) team
* and [others](https://github.com/gesinn-it/QRLite/graphs/contributors)

##
This extension is part of [**semantic::core**](https://semantic.wiki/core) - Enterprise Class MediaWiki distribution.
