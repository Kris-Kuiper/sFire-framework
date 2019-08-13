<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Image;

use sFire\Handler\NotImplementedHandler;

abstract class AbstractDriver {


	/**
	 * Returns an array with all the hexadecimal colors used in an image
	 * @param int $limit
	 * @param boolean $round
	 */
    public function getHexColors($limit = null, $round = true) {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
	 * Returns a percentage (integer) of how much an image is considered black and white
	 */
	public function blackWhite() {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
	 * Returns an array with base colors and their percentages on the found colors of an image
	 * @return array
	 */
	public function getBaseColors($limit = 10) {
        throw new NotImplementedHandler(__METHOD__);
    }
    

	/**
	 * Give current image a negative filter
	 */
	public function negate() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a higher or lower contrast
	 * @param int $level
	 */
	public function contrast($level = 50) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a higher or lower brightness
	 * @param int $level
	 */
	public function brightness($level = 50) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a grayscale filter
	 */
	public function grayscale() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a edgedetect filter
	 */
	public function edgedetect() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a emboss filter
	 */
	public function emboss() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a gaussian blur filter
	 */
	public function gaussianblur() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a selective blur filter
	 */
	public function selectiveblur() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a mean removal filter
	 */
	public function meanremoval() {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a colorize filter
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @param int $alpha
	 */
	public function colorize($r, $g, $b, $alpha) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a smooth filter
	 * @param int $level
	 */
	public function smooth($level = 50) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Give current image a pixelate filter
	 * @param int $blocksize
	 * @param int $effect
	 */
	public function pixelate($blocksize = 5, $effect = 50) {
		throw new NotImplementedHandler(__METHOD__);
	}


    /**
	 * Crop an image
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param boolean $interlace
	 */
	public function crop($x, $y, $width, $height, $interlace = false) {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
	 * Resize an image
	 * @param int $width
	 * @param int $height
	 * @param boolean $ratio
	 * @param boolean $interlace
	 */
	public function resize($width, $height, $ratio = false, $interlace = false) {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
	 * Rotates an image with a given angle
	 * @param int $degrees
	 * @param int $bgcolor
	 * @param int $transparent
	 */
	public function rotate($degrees, $bgcolor = 0, $transparent = 0) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Flip the current image horizontal, vertical or both
	 * @param int $mode
	 */
	public function flip($mode) {
		throw new NotImplementedHandler(__METHOD__);
	}


	/**
	 * Save the image to an optional new file giving with the $file parameter
     * @param array $commands
	 * @param string $extension
	 * @param int $quality
	 * @param string $file
	 */
	public function save($commands, $extension, $quality = 90, $file = null) {
		throw new NotImplementedHandler(__METHOD__);
	}
}
?>