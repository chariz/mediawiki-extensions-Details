<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\Details;

use PPFrame;
use Parser;
use MediaWiki\Parser\Sanitizer;

class Details {
	public static function parserHook( ?string $input, array $args, Parser $parser, PPFrame $frame ) {
		// Handling a self-closing tag is pointless
		if ( $input === null ) {
			return '';
		}

		// Add tracking category
		$parser->addTrackingCategory( 'details-category' );

		// Add our attributes
		$args = Sanitizer::mergeAttributes( [
			'class' => 'details--root'
		], $args);

		// Sanitize to attributes that would be valid on a <div>
		$attrs = Sanitizer::safeEncodeTagAttributes( Sanitizer::validateTagAttributes( $args, 'div' ) );

		// Add open attribute manually if set, because the sanitizer will have stripped it out
		if ( isset( $args['open'] ) ) {
			$attrs .= ' open';
		}

		return '<details ' . $attrs . '>' .
			trim( $parser->recursiveTagParse( $input, $frame ) ) .
			'</details>';
	}
}
