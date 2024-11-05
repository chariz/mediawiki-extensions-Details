<?php

declare( strict_types=1 );

namespace MediaWiki\Extension\Details;

use MediaWiki\MediaWikiServices;
use PPFrame;
use Parser;
/* TODO: Switch to MediaWiki\\Parser\\Sanitizer when Sanitizer fallback is fully dropped */ 
use Sanitizer;

class Details {
	private const HEAD_PARTS = [ 'head', 'top' ];
	private const FOOT_PARTS = [ 'foot', 'bottom', 'tail' ];
	private const FALSY_VALUES = [ 'no', 'n', 'false', 'off', '0' ];

	/* @var bool */
	private static $compatibilityMode = true;

	public static function parserHook( ?string $input, array $args, Parser $parser, PPFrame $frame ) {
		$config = MediaWikiServices::getInstance()->getMainConfig();
		self::$compatibilityMode =  $config->get( 'DetailsMWCollapsibleCompatibility' );

		$part = $args['part'] ?? null;

		// Handle invalid part
		if ( $part !== null && !in_array( $part, Details::HEAD_PARTS ) && !in_array( $part, Details::FOOT_PARTS ) ) {
			$part = null;
		}

		// Handle an invalid self-closing tag
		if ( $input === null && $part === null ) {
			return '';
		}

		// Add tracking category
		$parser->addTrackingCategory( 'details-category' );

		$result = '';

		if ( !in_array( $part, Details::FOOT_PARTS ) ) {
			// Add our attributes
			if ( self::$compatibilityMode === true ) {
				$args = Sanitizer::mergeAttributes( [
					'class' => 'details--root'
				], $args);
			}

			// Sanitize to attributes that would be valid on a <div>
			$attrs = Sanitizer::safeEncodeTagAttributes( Sanitizer::validateTagAttributes( $args, 'div' ) );

			// Add open attribute manually if set, because the sanitizer will have stripped it out.
			// We also support some falsy values, to help templates that use the open attribute.
			if ( isset( $args['open'] ) && !in_array( strtolower( $args['open'] ), Details::FALSY_VALUES ) ) {
				$attrs .= ' open';
			}

			$result .= '<details ' . $attrs . '>';
		}

		if ( !in_array( $part, Details::HEAD_PARTS ) && !in_array( $part, Details::FOOT_PARTS ) ) {
			$result .= trim( $parser->recursiveTagParse( $input, $frame ) );
		}

		if ( !in_array( $part, Details::HEAD_PARTS ) ) {
			$result .= '</details>';
		}

		return $result;
	}
}
