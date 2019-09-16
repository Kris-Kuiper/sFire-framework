<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Captcha;

use sFire\System\File;
use sFire\Hash\Token;
use sFire\HTTP\Response;

final class Captcha  {


	/**
	 * @var sFire\System\File $image
	 */
	private $image;


	/**
	 * @var sFire\System\File $font
	 */
	private $font;


	/**
	 * @var array $fontsize
	 */
	private $fontsize = ['min' => 15, 'max' => 20];


	/**
	 * @var array $angle
	 */
	private $angle = ['min' => -30, 'max' => 30];


	/**
	 * @var array $color
	 */
	private $color = ['r' => 0, 'g' => 0, 'b' => 0];


	/**
	 * @var string $text
	 */
	private $text;


	/**
	 * Constructor
	 * @return sFire\Captcha\Captcha
	 */
	public function __construct() {

		if(false === function_exists('imagettfbbox')) {
			return trigger_error('Function imagettfbbox should be enabled to use the Captcha class', E_USER_ERROR);
		}
	}
	

	/**
	 * Set image by giving a file path as image
	 * @param string $image
	 * @return sFire\Captcha\Captcha
	 */
	public function setImage($image) {

		if(false === is_string($image)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($image)), E_USER_ERROR);
		}

		$file = new File($image);

		if(false === $file -> exists()) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be an existing image', __METHOD__), E_USER_ERROR);
		}

		if(false === in_array(exif_imagetype($image), [IMAGETYPE_JPEG, IMAGETYPE_PNG])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be an valid image (jpg, jpeg or png)', __METHOD__), E_USER_ERROR);
		}

		$this -> image = $file;

		return $this;
	}


	/**
	 * Set the font by giving a file path as font
	 * @param string $font
	 * @return sFire\Captcha\Captcha 
	 */
	public function setFont($font) {

		if(false === is_string($font)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($font)), E_USER_ERROR);
		}

		$file = new File($font);

		if(false === $file -> exists()) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be an existing font', __METHOD__), E_USER_ERROR);
		}

		$this -> font = $file;

		return $this;
	}


	/**
	 * Set the color in RGB or Hex format (with or without #)
	 * @param string|int $r
	 * @param int $g
	 * @param int $b
	 * @return sFire\Captcha\Captcha 
	 */
	public function setFontColor($r, $g = null, $b = null) {
		
		if(true === is_string($r) && preg_match('/^#?([A-Fa-f0-9]{6})$/', $r, $color)) {
			list($r, $g, $b) = sscanf($color[1], '%02x%02x%02x');
		}

		if(false === ('-' . intval($r) == '-' . $r)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer or a valid hex color with 6 characters (# not included)', __METHOD__), E_USER_ERROR);
		}

		if(false === ('-' . intval($g) == '-' . $g)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($g)), E_USER_ERROR);
		}

		if(false === ('-' . intval($b) == '-' . $b)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($b)), E_USER_ERROR);
		}

		$this -> color = ['r' => $r, 'g' => $g, 'b' => $b];
	}


	/**
	 * Set the fontsize min. and max. If no max. is set, max. will be the same as min.
	 * @param int $min
	 * @param int $max
	 * @return sFire\Captcha\Captcha 
	 */
	public function setFontSize($min, $max = null) {

		if(false === ('-' . intval($min) == '-' . $min)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($min)), E_USER_ERROR);
		}

		if(null !== $max && false === ('-' . intval($max) == '-' . $max)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($max)), E_USER_ERROR);
		}

		$this -> fontsize = ['min' => $min, 'max' => ($max !== null ? $max : $min)];

		return $this;
	}


	/**
	 * Set the angle min. and max. If no max. is set, max. will be the same as min.
	 * @param int $min
	 * @param int $max
	 * @return sFire\Captcha\Captcha 
	 */
	public function setAngle($min, $max = null) {

		if(false === ('-' . intval($min) == '-' . $min)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($min)), E_USER_ERROR);
		}

		if(null !== $max && false === ('-' . intval($max) == '-' . $max)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($max)), E_USER_ERROR);
		}

		$this -> angle = ['min' => $min, 'max' => ($max !== null ? $max : $min)];

		return $this;
	}


	/**
	 * Set the text
	 * @param string $text
	 * @return sFire\Captcha\Captcha 
	 */
	public function setText($text) {

		if(false === is_string($text)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($text)), E_USER_ERROR);
		}

		$this -> text = $text;

		return $this;
	}


	/**
	 * Returns the font
	 * @return sFire\System\File
	 */
	public function getFont() {
		return $this -> font;
	}


	/**
	 * Returns the fontsize
	 * @return int
	 */
	public function getFontsize() {
		return $this -> fontsize;
	}


	/**
	 * Returns the angle
	 * @return array
	 */
	public function getAngle() {
		return $this -> angle;
	}


	/**
	 * Returns the font
	 * @return sFire\System\File
	 */
	public function getImage() {
		return $this -> image;
	}


	/**
	 * Returns the color in RGB format
	 * @return array
	 */
	public function getFontColor() {
		return $this -> color;
	}


	/**
	 * Returns the text
	 * @return string 
	 */
	public function getText() {
		return $this -> text;
	}


	/**
	 * Generates a random captcha value (without known characters that are similar like o, O and 0)
	 * @param int $length
	 * @return string
	 */
	public function generateText($length = 5) {
		
		if(false === ('-' . intval($length) == '-' . $length)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($length)), E_USER_ERROR);
		}

		$caseinsensitive = ['a', 'b', 'c', 'd', 'e', 'f', 'h', 'j', 'k', 'm', 'n', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y'];
		$casesensitive 	 = ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'J', 'K', 'M', 'N', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y'];
		$numbers_arr 	 = [3, 4, 5, 6, 7, 8, 9];
		$array 			 = array_merge(array_merge($caseinsensitive, $casesensitive), $numbers_arr);
		$str 			 = '';

		for($i = 0; $i < $length; $i++) {
			$str .= $array[array_rand($array, 1)];
		}

		return $str;
	}


	/**
	 * Generates the captcha image and save it to disk or display directly
	 * @param string $file
	 */
	public function generate($file = null) {

		if(null !== $file && false === is_string($file)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
        }

		if(null === $this -> image) {
			return trigger_error('Can not generate captcha image without valid background image. Set background image with setImage() method', E_USER_ERROR);
		}

		if(null === $this -> font) {
			return trigger_error('Can not generate captcha image without valid font. Set font with setFont() method', E_USER_ERROR);
		}

		//Generate and set text if not already set
		if(null === $this -> getText()) {
			$this -> setText($this -> generateText());
		}

		$text 	= $this -> getText();
		$image 	= $this -> image -> entity();
		$font 	= $this -> font -> entity();

		//Add image header
		if(null === $file) {
			Response :: addHeader('Content-type', $this -> image -> getMime());
		}
		else {
			
			$file = new File($file);

			if(false === in_array($file -> entity() -> getExtension(), ['png', 'jpg', 'jpeg'])) {
				return trigger_error(sprintf('Argument 1 passed to %s() must have the jpg, jpeg or png extension, "%s" given', __METHOD__, $file -> entity() -> getExtension()), E_USER_ERROR);
			}
		}

		//Create image
		switch(strtolower($image -> getExtension())) {
			
			case 'png' 	: $img = imagecreatefrompng($image -> getBasepath()); break;
			default 	: $img = imagecreatefromjpeg($image -> getBasepath());
		}

		//Generate output object
		$output = [];

		for($i = 0; $i < strlen($text); $i++) {

			$fontsize 	= rand($this -> fontsize['min'], $this -> fontsize['max']);
			$angle 		= rand($this -> angle['min'], $this -> angle['max']);
			$tb 		= imagettfbbox($fontsize, $angle, $font -> getBasepath(), $text[$i]);
			$color 		= imagecolorallocate($img, $this -> color['r'], $this -> color['g'], $this -> color['b']);
			$width 		= $image -> getWidth() / strlen($text);
			$width 		= $width < 2 ? 3 : $width;
			$tries 		= 0;

			if($tb[2] >= $width && $tries < 20) {

				$this -> fontsize['min']--;
				$i--;
				$tries++;

				continue;
			}

			$output[] 	= [

				'text' 		=> $text[$i], 
				'width' 	=> $tb[2], 
				'height' 	=> $tb[1],
				'angle'		=> $angle,
				'fontsize' 	=> $fontsize, 
				'color' 	=> $color
			];
		}

		foreach($output as $index => $char) {

			$x = $width * $index + rand(0, $width - $char['width']);
			$y = rand($char['fontsize'], $image -> getHeight() - $char['height']);
			
			imagettftext($img, $char['fontsize'], $char['angle'], $x, $y, $char['color'], $font -> getBasepath(), $char['text']);
		}
		
		$extension 	= $file !== null ? $file -> entity() -> getExtension() : $image -> getExtension();
		$file 		= $file !== null ? $file -> entity() -> getBasepath() : null;

		//Show image
		switch(strtolower($extension)) {
			
			case 'png' 	: imagepng($img, $file); break;
			default 	: imagejpeg($img, $file);
		}
	}
}