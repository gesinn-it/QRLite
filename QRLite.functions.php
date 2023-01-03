<?php

require_once "lib/phpqrcode/qrlib.php";

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
		global $wgTmpDirectory;

		// Defaults and escaping
		$content = self::paramGet( $params, 'prefix', '___MAIN___' );

		$format = self::paramGet( $params, 'format', 'png' );

		$size = self::paramGet( $params, 'size', 6 );
		$margin = self::paramGet( $params, 'margin', 0 );

		$ecc = self::paramGet( $params, 'ecc', 2 );

		// TODO: Doesn't seem to work
		$eccLevel = QR_ECLEVEL_M;
		if ( $ecc === 1 ) {
			$eccLevel = QR_ECLEVEL_L;
		} else {
			if ( $ecc === 2 ) {
				$eccLevel = QR_ECLEVEL_M;
			} else {
				if ( $ecc === 3 ) {
					$eccLevel = QR_ECLEVEL_Q;
				} else {
					if ( $ecc === 4 ) {
						$eccLevel = QR_ECLEVEL_H;
					}
				}
			}
		}

		$image = '';

		try {
			if ( $format === 'svg' ) {
				// Create a temporary svg file, as the library would otherwise print the result to the page itself
				$tempFileName = tempnam( $wgTmpDirectory, "SVGLite_" ) . '.svg';
				QRcode::svg( $content, $tempFileName, $eccLevel, $size, $margin );
				$svgContent = file_get_contents( $tempFileName );

				unlink( $tempFileName );
				$image = '<span class="svg-container" title="' . $content . '">' . $svgContent . '</span>';
			} else {
				if ( $format === 'png' ) {
					$tempFileName = tempnam( $wgTmpDirectory, "SVGLite_" ) . '.png';
					QRcode::png( $content, $tempFileName, $eccLevel, $size, $margin );
					$pngContent = file_get_contents( $tempFileName );

					// Delete temporary files
					foreach ( glob( $wgTmpDirectory . "/SVGLite_*" ) as $filename ) {
						unlink( $filename );
					}
					$image =
						'<img src="data:image/png;base64,' . base64_encode( $pngContent ) . '" alt="' . $content .
						'" title="' . $content . '">';
				}
			}
		} catch ( Exception $e ) {
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
	 * @param mixed $obj
	 * @return string
	 */
	public static function toJSON( $obj ) {
		header( 'Content-Type: application/json' );
		echo json_encode( $obj, JSON_PRETTY_PRINT );
		die();
	}

}
