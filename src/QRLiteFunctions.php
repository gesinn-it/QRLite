<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\QRLite;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;

/**
 * The actual QRLite Functions
 *
 * They can be used in a programmatic way through this class
 * Use the static getId function as the main entry point
 */
class QRLiteFunctions {

	/**
	 * @param array $params
	 * @return string HTML for display of the QR code.
	 */
	public static function generateQRCode( array $params = [] ): string {
		// MediaWiki\Html\Html was introduced in MW 1.40; the global Html alias was removed in MW 1.44.
		// This shim resolves the correct class for all supported versions (>= 1.39).
		$htmlClass = class_exists( \MediaWiki\Html\Html::class )
			? \MediaWiki\Html\Html::class
			: \Html::class;

		// Dependency check.
		if ( !class_exists( QrCode::class ) ) {
			$msg = 'QRLite error: QrCode class not found, you may need to run "composer install".';
			return $htmlClass::errorBox( $msg );
		}

		// Defaults and escaping
		$content = self::paramGet( $params, 'prefix', '___MAIN___' );

		$format = self::paramGet( $params, 'format', 'png' );

		$size = self::paramGet( $params, 'size', 6 );
		$margin = self::paramGet( $params, 'margin', 0 );

		$ecc = (int)self::paramGet( $params, 'ecc', 2 );

		$eccLevel = match ( $ecc ) {
			1 => ErrorCorrectionLevel::Low,
			3 => ErrorCorrectionLevel::Quartile,
			4 => ErrorCorrectionLevel::High,
			default => ErrorCorrectionLevel::Medium,
		};

		$image = '';
		try {
			$qrCode = new QrCode(
				data: $content,
				encoding: new Encoding( 'UTF-8' ),
				errorCorrectionLevel: $eccLevel,
				size: (int)$size * 30,
				margin: (int)$margin,
			);
			$writer = $format === 'svg' ? new SvgWriter() : new PngWriter();
			$writerOptions = [
				SvgWriter::WRITER_OPTION_EXCLUDE_XML_DECLARATION => true
			];
			$result = $writer->write( $qrCode, null, null, $writerOptions );

			if ( $format === 'svg' ) {
				$image = '<span class="svg-container" title="' . $content . '">' . $result->getString() . '</span>';
			} else {
				$image = $htmlClass::element( 'img', [ 'src' => $result->getDataUri(), 'title' => $content ] );
			}
		} catch ( Exception $e ) {
			$image = '<span class="error-message">' . $e->getMessage() . '</span>';

		}

		return '<span class="qrlite-result">' . $image . '</span>';
	}

	//////////////////////////////////////////
	// HELPER FUNCTIONS                     //
	//////////////////////////////////////////

	/**
	 * Helper function, that safely checks whether an array key exists
	 * and returns the trimmed value. If it doesn't exist, returns $default or null
	 *
	 * @param array $params
	 * @param string $key
	 * @param mixed|null $default
	 *
	 * @return mixed
	 */
	public static function paramGet( array $params, string $key, $default = null ) {
		if ( isset( $params[$key] ) ) {
			return trim( $params[$key] );
		} else {
			return $default;
		}
	}

}
