<?php

namespace MediaWiki\Extension\QRLite\Tests\Unit;

use MediaWiki\Extension\QRLite\QRLiteFunctions;
use MediaWikiIntegrationTestCase;

class QRLiteFunctionsTest extends MediaWikiIntegrationTestCase {

	/**
	 * Test the generateQRCode function with SVG format.
	 * @covers \MediaWiki\Extension\QRLite\QRLiteFunctions::generateQRCode
	 * @return string HTML for display of the QR code.
	 */
	public function testGenerateSvgQRCode() {
		$params = [
			'prefix' => 'TestSVG',
			'format' => 'svg',
		];

		$html = QRLiteFunctions::generateQRCode( $params );

		$this->assertStringContainsString( '<span class="svg-container"', $html );
		$this->assertStringContainsString( '<svg', $html );
	}

	/**
	 * Test the generateQRCode function with PNG format (default).
	 * @covers \MediaWiki\Extension\QRLite\QRLiteFunctions::generateQRCode
	 */
	public function testGeneratePngQRCode() {
		$params = [
			'prefix' => 'TestPNG',
			'format' => 'png',
		];

		$html = QRLiteFunctions::generateQRCode( $params );

		// Ovde očekujemo <img> tag, ne <svg>
		$this->assertStringContainsString( '<img', $html );
		$this->assertStringContainsString( 'src="data:image/png;base64', $html );
		$this->assertStringContainsString( 'title="TestPNG"', $html );
	}
}
