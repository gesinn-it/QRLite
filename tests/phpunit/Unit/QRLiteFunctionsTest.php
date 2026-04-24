<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\QRLite\Tests\Unit;

use MediaWiki\Extension\QRLite\QRLiteFunctions;
use MediaWikiIntegrationTestCase;

/**
 * @covers \MediaWiki\Extension\QRLite\QRLiteFunctions
 */
class QRLiteFunctionsTest extends MediaWikiIntegrationTestCase {

	public function testGenerateSvgQRCode() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestSVG', 'format' => 'svg' ] );

		$this->assertStringContainsString( '<span class="svg-container"', $html );
		$this->assertStringContainsString( '<svg', $html );
	}

	public function testGeneratePngQRCode() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestPNG', 'format' => 'png' ] );

		$this->assertStringContainsString( '<img', $html );
		$this->assertStringContainsString( 'src="data:image/png;base64', $html );
		$this->assertStringContainsString( 'title="TestPNG"', $html );
	}

	public function testDefaultFormatIsPng() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestDefault' ] );

		$this->assertStringContainsString( '<img', $html );
	}

	public function testEccLevelLow() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestEccLow', 'ecc' => '1' ] );

		$this->assertStringContainsString( 'qrlite-result', $html );
	}

	public function testEccLevelQuartile() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestEccQuartile', 'ecc' => '3' ] );

		$this->assertStringContainsString( 'qrlite-result', $html );
	}

	public function testEccLevelHigh() {
		$html = QRLiteFunctions::generateQRCode( [ 'prefix' => 'TestEccHigh', 'ecc' => '4' ] );

		$this->assertStringContainsString( 'qrlite-result', $html );
	}

	public function testCustomSizeAndMargin() {
		$html = QRLiteFunctions::generateQRCode( [
			'prefix' => 'TestSize',
			'format' => 'svg',
			'size'   => '4',
			'margin' => '2',
		] );

		$this->assertStringContainsString( '<svg', $html );
	}

	public function testParamGetReturnsDefault() {
		$this->assertSame( 'fallback', QRLiteFunctions::paramGet( [], 'missing', 'fallback' ) );
	}

	public function testParamGetReturnsValue() {
		$this->assertSame( 'hello', QRLiteFunctions::paramGet( [ 'key' => ' hello ' ], 'key' ) );
	}

	public function testParamGetReturnsNullWhenNoDefault() {
		$this->assertNull( QRLiteFunctions::paramGet( [], 'missing' ) );
	}
}
