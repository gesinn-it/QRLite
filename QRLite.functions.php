<?php

use Endroid\QrCode\ErrorCorrectionLevel;
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
		// Defaults and escaping
		$content = self::paramGet( $params, 'prefix', '___MAIN___' );

		$format = self::paramGet( $params, 'format', 'png' );

		$size = self::paramGet( $params, 'size', 6 );
		$margin = self::paramGet( $params, 'margin', 0 );

		$ecc = (int)self::paramGet( $params, 'ecc', 2 );

		// TODO: Doesn't seem to work
		$eccLevel = ErrorCorrectionLevel::MEDIUM();
		if ( $ecc === 1 ) {
			$eccLevel = ErrorCorrectionLevel::LOW();
		} else {
			if ( $ecc === 2 ) {
				$eccLevel = ErrorCorrectionLevel::MEDIUM();
			} else {
				if ( $ecc === 3 ) {
					$eccLevel = ErrorCorrectionLevel::QUARTILE();
				} else {
					if ( $ecc === 4 ) {
						$eccLevel = ErrorCorrectionLevel::HIGH();
					}
				}
			}
		}

		$qrCode = new QrCode( $content );
		$qrCode->setSize( $size * 30 );
		$qrCode->setMargin( $margin );
		$writer = $format === 'svg' ? new SvgWriter() : new PngWriter();
		$qrCode->setWriter( $writer );
		$qrCode->setWriterOptions( [ 'exclude_xml_declaration' => true ] );
		$qrCode->setEncoding( 'UTF-8' );
		$qrCode->setErrorCorrectionLevel( $eccLevel );
		$imageContent = $qrCode->writeString();

		if ( $format === 'svg' ) {
			$image = '<span class="svg-container" title="' . $content . '">' . $imageContent . '</span>';
		} else {
			if ( $format === 'png' ) {
				$image = Html::element(
					'img',
					[
						'src' => 'data:' . $qrCode->getContentType() . ';base64,' . base64_encode( $imageContent ),
						'title' => $content,
					]
				);
			}
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
