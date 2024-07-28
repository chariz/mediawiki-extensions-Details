<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\Details;

use PPFrame;
use Parser;
use MediaWiki\Parser\Sanitizer;

class Summary {
	public static function parserHook( ?string $input, array $args, Parser $parser, PPFrame $frame ) {
		// Add tracking category
		$parser->addTrackingCategory( 'details-category' );

		// Add modules for collapsible handling
		$parser->getOutput()->addModuleStyles( [ 'ext.details.styles' ] );
		$parser->getOutput()->addModules( [ 'ext.details' ] );

		// Render content, stripping <p> if it’s the outermost tag
		$body = trim( $parser->recursiveTagParse( $input, $frame ) );
		$body = Parser::stripOuterParagraph( $body );

		// Sanitize to attributes that would be valid on a <div>
		$attrs = Sanitizer::safeEncodeTagAttributes( Sanitizer::validateTagAttributes( $args, 'div' ) );

		return '<summary ' . $attrs . '>' .
			$body .
			'</summary>';
	}
}
