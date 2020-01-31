<?php

use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelHigh;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelLow;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelMedium;
use Endroid\QrCode\ErrorCorrectionLevel\ErrorCorrectionLevelQuartile;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Writer\SvgWriter;

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

	public static function generateQRCode( $params = [] ) {
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

		// TODO: Doesn't seem to work
		$eccLevel = new ErrorCorrectionLevelMedium();
		if ( $ecc === 1 ) {
			$eccLevel = new ErrorCorrectionLevelLow();
		} else {
			if ( $ecc === 2 ) {
				$eccLevel = new ErrorCorrectionLevelMedium();
			} else {
				if ( $ecc === 3 ) {
					$eccLevel = new ErrorCorrectionLevelQuartile();
				} else {
					if ( $ecc === 4 ) {
						$eccLevel = new ErrorCorrectionLevelHigh();
					}
				}
			}
		}

		$image = '';
		try {
			$qrCode = new QrCode( $content );
			$qrCode->setSize( $size * 30 );
			$qrCode->setMargin( $margin );
			$qrCode->setErrorCorrectionLevel( $eccLevel );
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
		}
		catch ( Exception $e ) {
			$image = '<span class="error-message">' . $e->getMessage() . '</span>';

		}

		$downloadButtons = '';
		$result = '<span class="qrlite-result">' . $image . $downloadButtons . '</span>';

		return $result;
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
	public static function paramGet( $params, $key, $default = null ) {
		if ( isset( $params[$key] ) ) {
			return trim( $params[$key] );
		} else {
			return $default;
		}
	}

	/**
	 * Debug function that converts an object/array to a <pre> wrapped pretty printed JSON string
	 *
	 * @param $obj
	 * @return string
	 */
	public static function toJSON( $obj ) {
		header( 'Content-Type: application/json' );
		echo json_encode( $obj, JSON_PRETTY_PRINT );
		die();
	}

}
