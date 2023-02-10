<?php
/**
 * MediaWiki QRLite Extension
 *
 * Generates QRCodes in .png and .svg format on the fly.
 *
 * @link https://www.mediawiki.org/wiki/Extension:QRLite
 *
 * @author Simon Heimler (gesinn.it GmbH & Co. KG)
 * @author Alexander Gesinn (gesinn.it GmbH & Co. KG)
 * @license GPL-3.0-or-later
 */

// Don't run in CLI mode (maintenance scripts)
if ( PHP_SAPI === "cli" ) {
	wfDebugLog( 'QRLite', "[QRLite] Skipping in CLI Mode" );
	return false;
}

if ( function_exists( 'wfLoadExtension' ) ) {

	wfLoadExtension( 'QRLite' );

	// Keep i18n globals so mergeMessageFileList.php doesn't break
	$wgMessagesDirs['QRLite'] = __DIR__ . '/i18n';

	wfWarn( 'Deprecated PHP entry point used for the QRLite extension. ' .
			'Please use wfLoadExtension("QRLite"); instead, ' .
			'see https://www.mediawiki.org/wiki/Extension_registration for more details.' );
	return;

} else {

}
