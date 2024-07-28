<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\Details;

use MediaWiki\Hook\ParserFirstCallInitHook;
use Parser;

class Hooks implements ParserFirstCallInitHook {
	/**
	 * @see https://www.mediawiki.org/wiki/Manual:Hooks/ParserFirstCallInit
	 *
	 * @param Parser $parser
	 */
	public function onParserFirstCallInit( $parser ): void {
		$parser->setHook( 'details', Details::class . '::parserHook' );
		$parser->setHook( 'summary', Summary::class . '::parserHook' );
	}
}
