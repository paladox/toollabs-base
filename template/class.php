<?php
/**
 * Main class
 *
 * @package mw-tool-example
 */
class ExampleTool extends KrToolBaseClass {

	protected $settingsKeys = array(
		'foo',
		'bar',
		'baz',
	);

	protected function show() {
		global $kgBase;

		$kgBase->setHeadTitle( 'Home' );
		$kgBase->setLayout( 'header', array(
			'titleText' => 'Welcome',
			'captionHtml' => 'Some text here',
		) );

		$kgBase->addOut( '<div class="container">' );

		$kgBase->addOut( kfAlertHtml( 'info', '<strong>Welcome!</strong> Hello there.' ) );

		$kgBase->addOut( 'Hello world' );

		// kfCacheKey( 'example', .. )

		// Close wrapping container
		$kgBase->addOut( '</div>' );
	}
}
