<?php
/**
 * Main initialization file
 *
 * This is the one file individual tools should include.
 *
 * @since 0.1
 * @author Krinkle, 2010-2014
 * @license Public domain, WTFPL
 * @package toollabs-base
 */

require_once __DIR__ . '/GlobalDefinitions.php';
require_once __DIR__ . '/GlobalConfig.php';

// Never overwrite $kgConfig, but if not set already
// make sure GlobalConfig is initiated
if ( !isset( $kgConf ) || !is_object( $kgConf ) ) {
	$kgConf = new GlobalConfig();
}

require_once __DIR__ . '/Request.php';

// POST overrides GET data
// We don't use $_REQUEST here to avoid interference from cookies.
$kgReq = new Request( $_POST + $_GET );

function kfIncludeMwClasses() {
	require_once __DIR__ . '/mw/mock.php';

	// Patched to remove:
	// - Html::htmlHeader()
	require_once __DIR__ . '/mw/Html.php';

	require_once __DIR__ . '/mw/GitInfo.php';

	// Patched to remove:
	// - Sanitizer::decodeCharReferencesAndNormalize ($wgContLang)
	// - Sanitizer::stripAllTags (StringUtils)
	// Patches to change:
	// - Sanitizer::validateEmail (wfRunHooks)
	require_once __DIR__ . '/mw/Sanitizer.php';
}
kfIncludeMwClasses();

require_once __DIR__ . '/HtmlSelect.php';
require_once __DIR__ . '/GlobalFunctions.php';
require_once __DIR__ . '/LabsDB.php';

// Must be after GlobalFunctions
$kgConf->initConfig();

// Debug
if ( $kgConf->isDebugMode() ) {
	error_reporting( E_ALL );
	ini_set( 'display_errors', 1 );
}

// Local settings
if ( file_exists(  __DIR__ . '/LocalConfig.php' ) ) {
	require_once __DIR__ . '/LocalConfig.php';
}

require_once __DIR__ . '/BaseTool.php';
require_once __DIR__ . '/KrToolBaseClass.php';
