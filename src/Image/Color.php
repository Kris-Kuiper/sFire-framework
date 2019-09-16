<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Image;

use sFire\Image\Resource\ColorList;

class Color {


	/**
	 * @var array $list
	 */
	private static $list;


	/**
	 * Convert a hexadecimal color to color name in human language
	 * @param string $color
	 * @return null|stdClass Object
	 */
	public static function name($color) {

		if(false === is_string($color)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($color)), E_USER_ERROR);
		}

		if(1 !== preg_match('#^[0-9a-fA-F]{6,6}$#', $color)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a 6 character hexadecimal string', __METHOD__), E_USER_ERROR);
		}

		$color 	= strtoupper($color);
		$rgb 	= static :: rgb($color);
		$r 		= $rgb -> r; 
		$g 		= $rgb -> g; 
		$b 		= $rgb -> b;
		$hsl 	= static :: hsl($color);
		$h 		= $hsl -> h; 
		$s 		= $hsl -> s; 
		$l 		= $hsl -> l;
		$ndf1 	= 0;
		$ndf2 	= 0; 
		$ndf 	= 0;
		$cl 	= -1;
		$df 	= -1;

		//Index all the colors
		if(null === static :: $list) {

			new ColorList();
			static :: index();
		}

		foreach(static :: $list as $hex => $color) {

			$ndf1 = pow($r - $color -> r, 2) + pow($g - $color -> g, 2) + pow($b - $color -> b, 2);
			$ndf2 = pow($h - $color -> h, 2) + pow($s - $color -> s, 2) + pow($l - $color -> l, 2);
			$ndf  = $ndf1 + $ndf2 * 2;

			if($df < 0 || $df > $ndf) {

				$df = $ndf;
				$cl = $hex;
			}
		}

		if(true === isset(static :: $list[$cl])) {
			return static :: $list[$cl];
		}

		return null;
	}


	/**
	 * Initialise all colors
	 */
	private static function index() {

		$colors = ColorList :: get('colors');

		foreach($colors as $hex => $color) {

			$rgb = static :: rgb($hex);
		  	$hsl = static :: hsl($hex);

		  	$shade = ColorList :: get(['shades', $color['s']]);
		  	$base  = ColorList :: get(['base', $shade['b']]);

			static :: $list[$hex] = (object) [
				
				'r' => $rgb -> r,
				'g' => $rgb -> g,
				'b' => $rgb -> b,
				'h' => $hsl -> h,
				's' => $hsl -> s,
				'l' => $hsl -> l,
				'hex' => $hex,
				'title' => $color['t'],
				'shade' => (object) [

					'id' => $color['s'],
					'hex' => $shade['h']
				],
				'base' => (object) [

					'id' => $shade['b'],
					'hex' => $base['h'],
					'title' => $base['t']
				]
			];
		}
	}


	/**
	 * Convert hexadecimal color to RGB
	 * @param string $color
	 * @return stdClass Object
	 */
	private static function rgb($color) {

		return (object) [

			'r' => intval(hexdec(substr($color, 0, 2))),
			'g' => intval(hexdec(substr($color, 2, 2))),
			'b' => intval(hexdec(substr($color, 4, 2)))
		];
	}


	/**
	 * Convert hexadecimal color to HSL
	 * @param string $color
	 * @return stdClass Object
	 */
	private static function hsl($color) {

		$r = intval(hexdec(substr($color, 0, 2))) / 255;
		$g = intval(hexdec(substr($color, 2, 2))) / 255;
		$b = intval(hexdec(substr($color, 4, 2))) / 255;

		$min 	= min($r, $g, $b);
		$max 	= max($r, $g, $b);
		$delta 	= $max - $min;
		$l 		= ($min + $max) / 2;
		$s 		= 0;
		$h 		= 0;

	    if($l > 0 && $l < 1) {
	    	$s = $delta / ($l < 0.5 ? (2 * $l) : (2 - 2 * $l));
	    }

	    if($delta > 0) {

			if($max == $r && $max != $g) { 
				$h += ($g - $b) / $delta;
			}

			if($max == $g && $max != $b) {
				$h += (2 + ($b - $r) / $delta);
			}

			if($max == $b && $max != $r) {
				$h += (4 + ($r - $g) / $delta);
			}

			$h /= 6;
	    }

	    return (object) [

	    	'h' => intval($h * 255), 
	    	's' => intval($s * 255), 
	    	'l' => intval($l * 255)
	    ];
	}
}