<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Image\Driver;

use sFire\Image\Image;

class GD {

	/**
	 * @var array $colors
	 */
	private $colors = ['black', 'blue', 'green', 'red', 'lightblue', 'yellow', 'pink', 'white', 'orange', 'purple', 'gray', 'brown'];


	/**
	 * @var array $image
	 */
	private $image = [];


	/**
	 * Constructor
	 * @param string $image
	 * @return sFire\System\Image
	 */
	public function __construct($file) {

		if(false === is_string($file)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_file($file)) {
			return trigger_error(sprintf('File "%s" passed to %s() does not exists', $file, __METHOD__), E_USER_ERROR);
		}

		if(false === is_readable($file)) {
			return trigger_error(sprintf('File "%s" passed to %s() is not readable', $file, __METHOD__), E_USER_ERROR);
		}

		if(list($width, $height) = @getimagesize($file)) {
			
			$this -> image = [

				'location' 	=> $file, 
				'width' 	=> $width, 
				'height' 	=> $height, 
				'extension' => pathinfo($file, PATHINFO_EXTENSION)
			];
		}
	}


	/**
	 * Returns an array with all the hexadecimal colors used in an image
	 * @param int $limit
	 * @param boolean $round
	 * @return array
	 */
	public function getHexColors($limit = null, $round = true) {

		if(null !== $limit && false === ('-' . intval($limit) == '-' . $limit)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($limit)), E_USER_ERROR);
		}

		if(null !== $round && false === is_bool($round)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($round)), E_USER_ERROR);
		}

		$image = imagecreatefromstring(file_get_contents($this -> image['location']));
		$hex   = [];
			
		for($y = 0; $y < $this -> image['height']; $y++) {

			for($x = 0; $x < $this -> image['width']; $x++) {

				$index = imagecolorat($image, $x, $y);
				$color = imagecolorsforindex($image, $index);
				
				if($round) {

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
	 * Returns a percentage (integer) of how much an image is considered black and white
	 * @return int
	 */
	public function blackWhite() {

		$image = imagecreatefromstring(file_get_contents($this -> image['location']));

		$r = [];
		$g = [];
		$b = [];
		$c = 0;

		for($x = 0; $x < $this -> image['width']; $x++) {

			for($y = 0; $y < $this -> image['height']; $y++) {

				$rgb = imagecolorat($image, $x, $y);
				
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
	 * Returns the base color of an image
	 * @return string
	 */
	public function getBaseColor() {

		$colors = $this -> getBaseColors();

		return array_search(max($colors), $colors);
	}


	/**
	 * Returns an array with base colors and their percentages on the found colors of an image
	 * @return array
	 */
	public function getBaseColors() {

		$image  = imagecreatefromstring(file_get_contents($this -> image['location']));
		$amount = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0];

		for($x = 0; $x < $this -> image['width']; $x++) {

			for($y = 0; $y < $this -> image['height']; $y++) {
				$amount[$this -> pixel2color(imagecolorat($image, $x, $y))]++;
			}
		}

		$colors = [];
		$sum 	= array_sum($amount);
		
		foreach($amount as $index => $color) {
			$colors[$this -> colors[$index]] = (floor(1000 * $color / $sum) / 10);
		}
		
		return $colors;
	}


	/**
	 * Give current image a negative filter
	 * @return sFire\System\Image
	 */
	public function negate() {
		return $this -> imageFilter(IMG_FILTER_NEGATE);
	}


	/**
	 * Give current image a higher or lower contrast
	 * @param int $level
	 * @return sFire\System\Image
	 */
	public function contrast($level = 50) {

		if(false === ('-' . intval($level) == '-' . $level)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($level)), E_USER_ERROR);
		}

		return $this -> imageFilter(IMG_FILTER_CONTRAST, $level);
	}


	/**
	 * Give current image a higher or lower brightness
	 * @return sFire\System\Image
	 */
	public function brightness($level = 50) {

		if(false === ('-' . intval($level) == '-' . $level)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($level)), E_USER_ERROR);
		}

		return $this -> imageFilter(IMG_FILTER_BRIGHTNESS, $level);
	}


	/**
	 * Give current image a grayscale filter
	 * @return sFire\System\Image
	 */
	public function grayscale() {
		return $this -> imageFilter(IMG_FILTER_GRAYSCALE);
	}


	/**
	 * Give current image a edgedetect filter
	 * @return sFire\System\Image
	 */
	public function edgedetect() {
		return $this -> imageFilter(IMG_FILTER_EDGEDETECT);
	}


	/**
	 * Give current image a emboss filter
	 * @return sFire\System\Image
	 */
	public function emboss() {
		return $this -> imageFilter(IMG_FILTER_EMBOSS);
	}


	/**
	 * Give current image a gaussian blur filter
	 * @return sFire\System\Image
	 */
	public function gaussianblur() {
		return $this -> imageFilter(IMG_FILTER_GAUSSIAN_BLUR);
	}


	/**
	 * Give current image a selective blur filter
	 * @return sFire\System\Image
	 */
	public function selectiveblur() {
		return $this -> imageFilter(IMG_FILTER_SELECTIVE_BLUR);
	}


	/**
	 * Give current image a mean removal filter
	 * @return sFire\System\Image
	 */
	public function meanremoval() {
		return $this -> imageFilter(IMG_FILTER_MEAN_REMOVAL);
	}


	/**
	 * Give current image a colorize filter
	 * @return sFire\System\Image
	 */
	public function colorize($r, $g, $b, $alpha) {
		return $this -> imageFilter(IMG_FILTER_COLORIZE, $r, $g, $b, $alpha);
	}


	/**
	 * Give current image a smooth filter
	 * @param int $level
	 * @return sFire\System\Image
	 */
	public function smooth($level = 50) {

		if(false === ('-' . intval($level) == '-' . $level)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($level)), E_USER_ERROR);
		}

		return $this -> imageFilter(IMG_FILTER_SMOOTH, $level);
	}


	/**
	 * Give current image a pixelate filter
	 * @param int $blocksize
	 * @param int $effect
	 * @return sFire\System\Image
	 */
	public function pixelate($blocksize = 5, $effect = 50) {

		if(false === ('-' . intval($blocksize) == '-' . $blocksize)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($blocksize)), E_USER_ERROR);
		}

		if(false === ('-' . intval($effect) == '-' . $effect)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($effect)), E_USER_ERROR);
		}

		return $this -> imageFilter(IMG_FILTER_PIXELATE, $blocksize, $effect);
	}


	/**
	 * Crop an image
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param int $quality
	 * @param string $file
	 * @param boolean $interlace
	 * @return sFire\System\Image
	 */
	public function crop($x, $y, $width, $height, $quality = 90, $file = null, $interlace = false) {

		if(false === ('-' . intval($x) == '-' . $x)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($x)), E_USER_ERROR);
		}

		if(false === ('-' . intval($y) == '-' . $y)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($y)), E_USER_ERROR);
		}

		if(false === ('-' . intval($width) == '-' . $width)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($width)), E_USER_ERROR);
		}

		if(false === ('-' . intval($height) == '-' . $height)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($height)), E_USER_ERROR);
		}

		if(false === ('-' . intval($quality) == '-' . $quality)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($quality)), E_USER_ERROR);
		}

		if(null !== $file && false === is_string($file)) {
			return trigger_error(sprintf('Argument 6 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_bool($interlace)) {
			return trigger_error(sprintf('Argument 7 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($interlace)), E_USER_ERROR);
		}

		$file 		= $file ? $file : $this -> image['location'];
		$extension  = $file ? strtolower(pathinfo($file, PATHINFO_EXTENSION)) : $this -> image['extension'];

		$quality = $this -> getQuality($quality, $extension);

		//Check if folder is writeable
		if(false === is_writable(dirname($file))) {
			return trigger_error(sprintf('Folder "%s" is not writable', dirname($file)), E_USER_ERROR);
		}

		$this -> createImage($x, $y, $width, $height, $width, $height, $quality, $file, $interlace);

		return new Image(new GD($file));
	}


	/**
	 * Resize an image
	 * @param int $width
	 * @param int $height
	 * @param boolean $ratio
	 * @param int $quality
	 * @param string $file
	 * @param boolean $interlace
	 * @return sFire\System\Image
	 */
	public function resize($width, $height, $ratio = false, $quality = 90, $file = null, $interlace = false) {

		if(false === ('-' . intval($width) == '-' . $width)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($width)), E_USER_ERROR);
		}

		if(false === ('-' . intval($height) == '-' . $height)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($height)), E_USER_ERROR);
		}

		if(null !== $ratio && false === is_bool($ratio)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($ratio)), E_USER_ERROR);
		}

		if(null !== $quality && false === ('-' . intval($quality) == '-' . $quality)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($quality)), E_USER_ERROR);
		}

		if(null !== $file && false === is_string($file)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_bool($interlace)) {
			return trigger_error(sprintf('Argument 6 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($interlace)), E_USER_ERROR);
		}

		$file 		= $file ? $file : $this -> image['location'];
		$extension  = $file ? strtolower(pathinfo($file, PATHINFO_EXTENSION)) : $this -> image['extension'];
		$quality 	= $this -> getQuality($quality, $extension);

		//Check if folder is writeable
		if(false === is_writable(dirname($file))) {
			return trigger_error(sprintf('Folder "%s" is not writable', dirname($file)), E_USER_ERROR);
		}

		//Set width and height
		if($height == 0 && $width > 0) {
			$height = round($this -> image['height'] / ($this -> image['width'] / $width), 0);
		}
		elseif($width == 0 && $height > 0) {
			$width = round($this -> image['width'] / ($this -> image['height'] / $height), 0);
		}

		$x = 0;
		$y = 0;

		//Ratio
		if(true === $ratio) {

			$ratio 		= [$this -> image['width'] / $this -> image['height'], $width / $height];
			$tmp_width 	= $this -> image['width'];
			$tmp_height = $this -> image['height'];

			if($ratio[0] > $ratio[1]) {

				$this -> image['width'] = $this -> image['height'] * $ratio[1];
				$x = ($tmp_width - $this -> image['width']) / 2;
			}
			elseif($ratio[0] < $ratio[1]) {

				$this -> image['height'] = $this -> image['width'] / $ratio[1];
				$y = ($tmp_height - $this -> image['height']) / 2;
			}
		}

		//Resizing
		$this -> createImage($x, $y, $this -> image['width'], $this -> image['height'], $width, $height, $quality, $file, $interlace);

		return new Image(new GD($file));
	}


	/**
	 * Returns a base color index for given rgb integer
	 * @param int $rgb
	 * @return int
	 */
	private function pixel2color($rgb) {

		if(false === ('-' . intval($rgb) == '-' . $rgb)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($rgb)), E_USER_ERROR);
		}

		$border = 40;
		$r = ($rgb >> 16) & 0xFF;
		$g = ($rgb >> 8) & 0xFF;
		$b = $rgb & 0xFF;

		if($r < $border && $g < $border && $b < $border) {
			$color = 0; //Black
		}
		elseif($r > (255 - $border) && $g > (255 - $border) && $b > (255 - $border)) {
			$color = 7; //White
		}
		else {

			$max = max([$r, $g, $b]);
			$min = min([$r, $g, $b]);
			
			if($r < $max / 2.5 && $g < $max / 2.5 && $b == $max) { $color = 1; } //Blue
			elseif($r < $max && $g == $max  && $b < $max / 1.4) { $color = 2; } //Green
			elseif($r == $max && $g < 105 && $r - $g < 51 && $g < $r && $b == $min) { $color = 11; } //Brown
			elseif($r == $max  && $g < 100 / 1.9 && $b < $max / 1.9) { $color = 3; } //Red
			elseif($r > $g && $g == $min && $g < 103 && $b > $min) { $color = 9; } 	//Purple
			elseif(($r > 50 && $r < 220 && ($r - $min < 35)) && ($g > 50 && $g < 220 && ($g - $min < 35)) && ($b > 50 && $b < 220) && ($b - $min < 35)) { $color = 10; } //Gray
			elseif($r == $max && $g > 99 && $g < 180 && $b == $min && $b < 55) { $color = 8; } //Orange
			elseif($r == $min) { $color = 4; } //Light blue
			elseif($b == $min) { $color = 5; } //Yellow
			elseif($g == $min) { $color = 6; } //Pink
		}
		
		return $color;
	}


	/**
	 * Applies a filter to current image
	 * @return sFire\System\Image
	 */
	private function imageFilter() {

		//Check if folder is writeable
		if(false === is_writable(dirname($this -> image['location']))) {
			trigger_error(sprintf('Directory "%s" should be writable', dirname($this -> image['location'])), E_USER_ERROR);
		}

		$image = imagecreatefromstring(file_get_contents($this -> image['location']));
		
		call_user_func_array('imagefilter', array_merge([$image], func_get_args()));

		switch(strtolower($this -> image['extension'])) {

			case 'bmp'	: imagewbmp($image, $this -> image['location']); break;
			case 'png'	: imagepng($image, $this -> image['location'], 9); break;
			case 'gif'	: imagegif($image, $this -> image['location']); break;
			default 	: imagejpeg($image, $this -> image['location'], 90); break;
		}

	    imagedestroy($image);

		return $this;
	}


	/**
	 * Validates and returns quality based on image extension
	 * @param int $quality
	 * @param string $extension
	 * @return int
	 */
	private function getQuality($quality, $extension) {

		if(false === ('-' . intval($quality) == '-' . $quality)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($quality)), E_USER_ERROR);
		}

		if(false === is_string($extension)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($extension)), E_USER_ERROR);
		}

		$quality = $quality < 0 || $quality > 100 ? 90 : intval($quality);

		if(strtolower($extension) === 'png' && $quality > 9) {
			$quality = floor(($quality - 1) / 10);
		}

		return $quality;
	}


	/**
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param int $new_width
	 * @param int $new_height
	 * @param int $quality
	 * @param string $file
	 * @param boolean $interlace
	 * @return boolean
	 */
	private function createImage($x, $y, $width, $height, $new_width, $new_height, $quality, $file, $interlace = false) {

		if(false === ('-' . intval($x) == '-' . $x)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($x)), E_USER_ERROR);
		}

		if(false === ('-' . intval($y) == '-' . $y)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($y)), E_USER_ERROR);
		}

		if(false === ('-' . intval($width) == '-' . $width)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($width)), E_USER_ERROR);
		}

		if(false === ('-' . intval($height) == '-' . $height)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($height)), E_USER_ERROR);
		}

		if(false === ('-' . intval($new_width) == '-' . $new_width)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($new_width)), E_USER_ERROR);
		}

		if(false === ('-' . intval($new_height) == '-' . $new_height)) {
			return trigger_error(sprintf('Argument 6 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($new_height)), E_USER_ERROR);
		}

		if(false === ('-' . intval($quality) == '-' . $quality)) {
			return trigger_error(sprintf('Argument 7 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($quality)), E_USER_ERROR);
		}

		if(null !== $file && false === is_string($file)) {
			return trigger_error(sprintf('Argument 8 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_bool($interlace)) {
			return trigger_error(sprintf('Argument 9 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($interlace)), E_USER_ERROR);
		}

		$image = imagecreatefromstring(file_get_contents($this -> image['location']));
		$new   = imagecreatetruecolor($new_width, $new_height);

		if(false === imagesavealpha($new, true)) {
			trigger_error('Could not set the flag to save full alpha channel', E_USER_ERROR);
		}

		if(false === imagealphablending($new, false)) {
			trigger_error('Could not set the blending mode', E_USER_ERROR);
		}

		if(false === imagefill($new, 0, 0, imagecolorallocate($new, 255, 255, 255))) {
			trigger_error('Could not flood fill the image', E_USER_ERROR);
		}

		if(false === imagecopyresampled($new, $image, 0, 0, $x, $y, $new_width, $new_height, $width, $height)) {
			trigger_error('Could not copy and resize part of an image with resampling', E_USER_ERROR);
		}

		if((true === $interlace && imageinterlace($image, true)) || false === $interlace) {

			imagedestroy($image);

			switch(strtolower($this -> image['extension'])) {

				case 'bmp'	: imagewbmp($new, $file); break;
				case 'png'	: imagepng($new, $file, $quality); break;
				case 'gif'	: imagegif($new, $file); break;
				default 	: imagejpeg($new, $file, $quality); break;
			}
		}
	}
}
?>