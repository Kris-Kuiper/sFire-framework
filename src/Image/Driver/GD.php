<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Image\Driver;

use sFire\Image\AbstractDriver;
use sFire\Image\Color;

class GD extends AbstractDriver {


	/**
     * @param resource $image
     */
	private $image;


	/**
	 * Constructor
	 * @param resource $image
	 */
	public function __construct($image) {
		$this -> image = $image;
	}


    /**
	 * Returns an array with all the hexadecimal colors used in an image
	 * @param int $limit
	 * @param boolean $round
	 * @return array
	 */
	public function getHexColors($limit = null, $round = true) {

		$hex 	= [];
		$width 	= imagesx($this -> image);
		$height = imagesy($this -> image);
			
		for($y = 0; $y < $height; $y++) {

			for($x = 0; $x < $width; $x++) {

				$index = imagecolorat($this -> image, $x, $y);
				$color = imagecolorsforindex($this -> image, $index);
				
				if(true === $round) {

					foreach(['red', 'green', 'blue'] as $type) {

						$color[$type] = intval((($color[$type]) + 15) / 32) * 32;

						if($color[$type] >= 256){
							$color[$type] = 240;
						}
					}
				}

				$hex[] = substr('0' . dechex($color['red']), -2) . substr('0' . dechex($color['green']), -2) . substr('0' . dechex($color['blue']), -2);
			}
		}

		$hex = array_count_values($hex);

		natsort($hex);

		$hex = $limit ? array_slice(array_reverse($hex, true), 0, $limit, true) : array_reverse($hex, true);

		return $hex;
	}


	/**
	 * Returns an array with base colors and their percentages on the found colors of an image
	 * @param integer $limit
	 * @return array
	 */
	public function getBaseColors($limit = 10) {

		$hexs 	= $this -> getHexColors();
		$output = [];
		$index  = 0;

		foreach($hexs as $hex => $amount) {

			if($index >= $limit) {
				break;
			}

			$color = Color :: name((string) $hex);

			if(null !== $color) {
				$output[] = $color;
			}

			$index++;
		}

		return $output;
	}


	/**
	 * Returns a percentage (integer) of how much an image is considered black and white
	 * @return integer
	 */
	public function blackWhite() {

		$tmp = imagecreatefromstring(file_get_contents($this -> image['location']));
		$r 	 = [];
		$g 	 = [];
		$b 	 = [];
		$c 	 = 0;

		for($x = 0; $x < $this -> image['width']; $x++) {

			for($y = 0; $y < $this -> image['height']; $y++) {

				$rgb = imagecolorat($tmp, $x, $y);
				
				$r[$x][$y] = ($rgb >> 16) & 0xFF;
				$g[$x][$y] = ($rgb >> 8) & 0xFF;
				$b[$x][$y] = $rgb & 0xFF;
				
				if($r[$x][$y] == $g[$x][$y] && $r[$x][$y] == $b[$x][$y]) {
					$c++;
				}
			}
		}
		
		return round($c / ($this -> image['width'] * $this -> image['height']) * 100, 0);
	}


	/**
	 * Give current image a negative filter
	 * @return resource
	 */
	public function negate() {
		
		$this -> filter(IMG_FILTER_NEGATE);
		return $this -> image;
	}


	/**
	 * Give current image a higher or lower contrast
	 * @param int $level
	 * @return resource
	 */
	public function contrast($level = 50) {

		$this -> filter(IMG_FILTER_CONTRAST, $level);
		return $this -> image;
	}


	/**
	 * Give current image a higher or lower brightness
	 * @param int $level
	 * @return resource
	 */
	public function brightness($level = 50) {

		$this -> filter(IMG_FILTER_BRIGHTNESS);
		return $this -> image;
	}


	/**
	 * Give current image a grayscale filter
	 * @return resource
	 */
	public function grayscale() {

		$this -> filter(IMG_FILTER_GRAYSCALE);
		return $this -> image;
	}


	/**
	 * Give current image a edgedetect filter
	 * @return resource
	 */
	public function edgedetect() {

		$this -> filter(IMG_FILTER_EDGEDETECT);
		return $this -> image;
	}


	/**
	 * Give current image a emboss filter
	 * @return resource
	 */
	public function emboss() {

		$this -> filter(IMG_FILTER_EMBOSS);
		return $this -> image;
	}


	/**
	 * Give current image a gaussian blur filter
	 * @return resource
	 */
	public function gaussianblur() {

		$this -> filter(IMG_FILTER_GAUSSIAN_BLUR);
		return $this -> image;
	}


	/**
	 * Give current image a selective blur filter
	 * @return resource
	 */
	public function selectiveblur() {

		$this -> filter(IMG_FILTER_SELECTIVE_BLUR);
		return $this -> image;
	}


	/**
	 * Give current image a mean removal filter
	 * @return resource
	 */
	public function meanremoval() {

		$this -> filter(IMG_FILTER_MEAN_REMOVAL);
		return $this -> image;
	}


	/**
	 * Give current image a colorize filter
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @param int $alpha
	 * @return resource
	 */
	public function colorize($r, $g, $b, $alpha) {

		$this -> filter(IMG_FILTER_COLORIZE, $r, $g, $b, $alpha);
		return $this -> image;
	}


	/**
	 * Give current image a smooth filter
	 * @param int $level
	 * @return resource
	 */
	public function smooth($level = 50) {

		$this -> filter(IMG_FILTER_SMOOTH, $level);
		return $this -> image;
	}


	/**
	 * Give current image a pixelate filter
	 * @param int $blocksize
	 * @param int $effect
	 * @return resource
	 */
	public function pixelate($blocksize = 5, $effect = 50) {

		$this -> filter(IMG_FILTER_PIXELATE, $blocksize, $effect);
		return $this -> image;
	}


	/**
	 * Crop an image
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param boolean $interlace
	 * @return boolean
	 */
	public function crop($x, $y, $width, $height, $interlace = false) {
		return $this -> createImage($x, $y, $width, $height, $width, $height, $interlace);
	}


	/**
	 * Resize an image
	 * @param int $width
	 * @param int $height
	 * @param boolean $ratio
	 * @param boolean $interlace
	 * @return boolean
	 */
	public function resize($width, $height, $ratio = false, $interlace = false) {

		$image = ['width' => imagesx($this -> image), 'height' => imagesy($this -> image)];

		//Set width and height
		if($height == 0 && $width > 0) {
			$height = round($image['height'] / ($image['width'] / $width), 0);
		}
		elseif($width == 0 && $height > 0) {
			$width = round($image['width'] / ($image['height'] / $height), 0);
		}

		$x = 0;
		$y = 0;

		//Ratio
		if(true === $ratio) {

			$ratio 		= [$image['width'] / $image['height'], $width / $height];
			$tmp_width 	= $image['width'];
			$tmp_height = $image['height'];

			if($ratio[0] > $ratio[1]) {

				$image['width'] = $image['height'] * $ratio[1];
				$x = ($tmp_width - $image['width']) / 2;
			}
			elseif($ratio[0] < $ratio[1]) {

				$image['height'] = $image['width'] / $ratio[1];
				$y = ($tmp_height - $image['height']) / 2;
			}
		}

		//Resizing
		return $this -> createImage($x, $y, $image['width'], $image['height'], $width, $height, $interlace);
	}


	/**
	 * Rotates an image with a given angle
	 * @param int $degrees
	 * @param int $bgcolor
	 * @param int $transparent
	 * @return resource
	 */
	public function rotate($degrees, $bgcolor = 0, $transparent = 0) {

		imagealphablending($this -> image, false);
    	imagesavealpha($this -> image, true);

	    $rotation = imagerotate($this -> image, $degrees, imageColorAllocateAlpha($this -> image, 0, 0, 0, $bgcolor), $transparent);
	    imagealphablending($rotation, false);
	    imagesavealpha($rotation, true);

		$this -> image = $rotation;
		return $this -> image;
	}


	/**
	 * Flip the current image horizontal, vertical or both
	 * @param int $mode
	 */
	public function flip($mode) {
		
		imageflip($this -> image, $mode);
		return $this -> image;
	}


	/**
	 * Save the image to an optional new file giving with the $file parameter
     * @param array $commands
	 * @param string $extension
	 * @param int $quality
	 * @param string $file
	 * @return boolean
	 */
	public function save($commands, $extension, $quality = 90, $file = null) {

		foreach($commands as $command) {
			call_user_func_array([$this, $command -> method], $command -> params);
		}

		switch(strtolower($extension)) {

			case 'bmp'	: return imagebmp($this -> image, $file); break;
			case 'png'	: return imagepng($this -> image, $file, $quality); break;
			case 'gif'	: return imagegif($this -> image, $file); break;
			case 'webp' : return imagewebp($this -> image, $file, $quality); break;
			default 	: return imagejpeg($this -> image, $file, $quality); break;
		}
	}


	/**
	 * Creates new image resource. Returns false if failed.
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param int $new_width
	 * @param int $new_height
	 * @param boolean $interlace
	 * @return boolean
	 */
	private function createImage($x, $y, $width, $height, $new_width, $new_height, $interlace = false) {

		$resource = imagecreatetruecolor($new_width, $new_height);

		if(false === imagesavealpha($resource, true)) {
			trigger_error('Could not set the flag to save full alpha channel', E_USER_ERROR);
		}

		if(false === imagealphablending($resource, false)) {
			trigger_error('Could not set the blending mode', E_USER_ERROR);
		}

		if(false === imagefill($resource, 0, 0, imagecolorallocate($resource, 255, 255, 255))) {
			trigger_error('Could not flood fill the image', E_USER_ERROR);
		}

		if(false === imagecopyresampled($resource, $this -> image, 0, 0, $x, $y, $new_width, $new_height, $width, $height)) {
			trigger_error('Could not copy and resize part of an image with resampling', E_USER_ERROR);
		}

		if((true === $interlace && imageinterlace($this -> image, true)) || false === $interlace) {
				
			$this -> image = $resource;
			return true;
		}

		return false;
	}


	/**
	 * Applies a filter to current image
	 * @return boolean
	 */
	private function filter() {

		$params = func_get_args();
		return call_user_func_array('imagefilter', array_merge([$this -> image], $params));
	}
}
?>