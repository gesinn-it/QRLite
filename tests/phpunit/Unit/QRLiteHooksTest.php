<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\QRLite\Tests\Unit;

use MediaWiki\Extension\QRLite\QRLiteHooks;
use MediaWikiIntegrationTestCase;
use Parser;

/**
 * @covers \MediaWiki\Extension\QRLite\QRLiteHooks
 */
class QRLiteHooksTest extends MediaWikiIntegrationTestCase {

	private function makeParser(): Parser {
		return $this->getServiceContainer()->getParserFactory()->create();
	}

	public function testQrliteFunctionHookReturnsSvgHtml() {
		$hooks = new QRLiteHooks();
		$parser = $this->makeParser();

		$result = $hooks->qrliteFunctionHook( $parser, 'TestMain', 'format=svg' );

		$this->assertIsArray( $result );
		$this->assertStringContainsString( '<svg', $result[0] );
		$this->assertTrue( $result['noparse'] );
		$this->assertTrue( $result['isHTML'] );
	}

	public function testQrliteFunctionHookReturnsPngHtml() {
		$hooks = new QRLiteHooks();
		$parser = $this->makeParser();

		$result = $hooks->qrliteFunctionHook( $parser, 'TestMain', 'format=png' );

		$this->assertIsArray( $result );
		$this->assertStringContainsString( '<img', $result[0] );
	}

	public function testQrliteFunctionHookUsesMainAsPrefix() {
		$hooks = new QRLiteHooks();
		$parser = $this->makeParser();

		$result = $hooks->qrliteFunctionHook( $parser, 'https://example.com', 'format=svg' );

		$this->assertStringContainsString( 'https://example.com', $result[0] );
	}

	public function testQrliteFunctionHookWithKeyValueOptions() {
		$hooks = new QRLiteHooks();
		$parser = $this->makeParser();

		$result = $hooks->qrliteFunctionHook( $parser, '', 'prefix=KeyValueTest', 'format=png', 'ecc=1' );

		$this->assertStringContainsString( 'KeyValueTest', $result[0] );
		$this->assertStringContainsString( '<img', $result[0] );
	}

	public function testOnParserFirstCallInitRegistersHook() {
		$hooks = new QRLiteHooks();
		$parser = $this->makeParser();

		// Should not throw; verifies the hook registration code path runs
		$hooks->onParserFirstCallInit( $parser );

		$this->assertTrue( true );
	}
}
