<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\QRLite;

use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\ErrorCorrectionLevel;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;
use Exception;
use Html;

/**
 * The actual QRLite Functions
 *
 * They can be used in a programmatic way through this class
 * Use the static getId function as the main entry point
 *
 * @file
 * @ingroup Extensions
 */
class QRLiteFunctions {

	/**
	 * @param array $params
	 * @return string HTML for display of the QR code.
	 */
	public static function generateQRCode( array $params = [] ): string {
		// Dependency check.
		if ( !class_exists( QrCode::class ) ) {
			return Html::errorBox( 'QRLite error: QrCode class not found, you may need to run "composer install".' );
		}

		// Defaults and escaping
		$content = self::paramGet( $params, 'prefix', '___MAIN___' );

		$format = self::paramGet( $params, 'format', 'png' );

		$size = self::paramGet( $params, 'size', 6 );
		$margin = self::paramGet( $params, 'margin', 0 );

		$ecc = (int)self::paramGet( $params, 'ecc', 2 );

		if ( $ecc === 1 ) {
			$eccLevel = ErrorCorrectionLevel::Low;
		} elseif ( $ecc === 3 ) {
			$eccLevel = ErrorCorrectionLevel::Quartile;
		} elseif ( $ecc === 4 ) {
			$eccLevel = ErrorCorrectionLevel::High;
		} else {
			$eccLevel = ErrorCorrectionLevel::Medium;
		}

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
				$image = Html::element( 'img', [ 'src' => $result->getDataUri(), 'title' => $content ] );
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
