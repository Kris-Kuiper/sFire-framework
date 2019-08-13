<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Image;

use sFire\Image\Color;

class Image {
    

	/**
	 * @param int 
	 */
	const FLIP_HORIZONTAL = 1;
	

	/**
	 * @param int 
	 */
	const FLIP_VERTICAL = 2;


	/**
	 * @param int 
	 */
	const FLIP_BOTH = 3;


    /**
     * @param string $driver
     */
    private $driver;


    /**
     * @param mixed $driverInstance
     */
    private $driverInstance;


    /**
     * @param resource $image
     */
    private $image;


    /**
     * @param string $extension
     */
    private $extension;


    /**
     * @param array $commands
     */
    private $commands = [];


    /**
	 * Sets the driver
	 * @param string $driver
	 * @return sFire\Image\Image
	 */
    public function setDriver($driver) {

    	if(false === is_string($driver)) {
    		return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($driver)), E_USER_ERROR);
    	}

    	$driver = __NAMESPACE__ . '\\Driver\\' . $driver;

    	if(false === class_exists($driver)) {
    		return trigger_error(sprintf('Driver "%s" does not exists', $driver), E_USER_ERROR);
    	}

        $this -> driver = $driver;

        return $this;
    }


    /**
	 * Set a new image
	 * @param string $image
	 * @return sFire\Image\Image
	 */
	public function setImage($file) {

		if(false === is_string($file)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_file($file)) {
			return trigger_error(sprintf('File "%s" passed to %s() does not exists', $file, __METHOD__), E_USER_ERROR);
		}

		if(false === is_readable($file)) {
			return trigger_error(sprintf('File "%s" passed to %s() is not readable', $file, __METHOD__), E_USER_ERROR);
		}

		$info = @getimagesize($file);

		if(false === is_array($info) || count($info) < 3) {
			return trigger_error(sprintf('File "%s" passed to %s() is not an image', $file, __METHOD__), E_USER_ERROR);
		}

		$this -> image 	 	= @imagecreatefromstring(file_get_contents($file));
		$this -> extension 	= pathinfo($file, PATHINFO_EXTENSION);

		return $this;
	}


	/**
	 * Execute all commands and save the image to an optional new file giving with the $file parameter
	 * @param string $file
	 * @param int $quality
	 * @return boolean
	 */
	public function save($file = null, $quality = 90) {

		if(null !== $file && false === is_string($file)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(null !== $file && false === is_writable(dirname($file))) {
			return trigger_error(sprintf('File in "%s" directory is not writable', dirname($file)), E_USER_ERROR);
		}

		if(false === ('-' . intval($quality) == '-' . $quality)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($quality)), E_USER_ERROR);
		}

		if($quality < 0 || $quality > 100) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be between 0 and 100, "%s" given', __METHOD__, $quality), E_USER_ERROR);
		}

		$extension = (null !== $file) ? pathinfo($file, PATHINFO_EXTENSION) : $this -> extension;

		return $this -> call() -> save($this -> commands, $extension, $this -> getQuality($quality, $extension), $file);
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

		return $this -> call() -> getHexColors($limit, $round);
	}


	/**
	 * Returns an array with base colors and their percentages on the found colors of an image
	 * @param integer $limit
	 * @return array
	 */
	public function getBaseColors($limit = 10) {

		if(false === ('-' . intval($limit) == '-' . $limit)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($limit)), E_USER_ERROR);
		}

		return $this -> call() -> getBaseColors($limit);
	}


	/**
	 * Give current image a negative filter
	 * @return sFire\Image\Image
	 */
	public function negate() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a higher or lower contrast
	 * @param int $level
	 * @return sFire\Image\Image
	 */
	public function contrast($level = 50) {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a higher or lower brightness
	 * @param int $level
	 * @return sFire\Image\Image
	 */
	public function brightness($level = 50) {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a grayscale filter
	 * @return sFire\Image\Image
	 */
	public function grayscale() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a edgedetect filter
	 * @return sFire\Image\Image
	 */
	public function edgedetect() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a emboss filter
	 * @return sFire\Image\Image
	 */
	public function emboss() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a gaussian blur filter
	 * @return sFire\Image\Image
	 */
	public function gaussianblur() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a selective blur filter
	 * @return sFire\Image\Image
	 */
	public function selectiveblur() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a mean removal filter
	 * @return sFire\Image\Image
	 */
	public function meanremoval() {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a colorize filter
	 * @param int $r
	 * @param int $g
	 * @param int $b
	 * @param int $alpha
	 * @return sFire\Image\Image
	 */
	public function colorize($r, $g, $b, $alpha) {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a smooth filter
	 * @param int $level
	 * @return sFire\Image\Image
	 */
	public function smooth($level = 50) {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Give current image a pixelate filter
	 * @param int $blocksize
	 * @param int $effect
	 * @return sFire\Image\Image
	 */
	public function pixelate($blocksize = 5, $effect = 50) {
		
		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Flip the current image horizontal, vertical or both
	 * @param int $mode
	 * @return sFire\Image\Image
	 */
	public function flip($mode = self :: FLIP_HORIZONTAL) {
		
		if(false === in_array($mode, [self :: FLIP_HORIZONTAL, self :: FLIP_VERTICAL, self :: FLIP_BOTH])) {
			return trigger_error(sprintf('Unknown mode passed %s(), "%s" given', __METHOD__, gettype($mode)), E_USER_ERROR);
		}

		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


    /**
	 * Crop an image
	 * @param int $x
	 * @param int $y
	 * @param int $width
	 * @param int $height
	 * @param boolean $interlace
	 * @return sFire\Image\Image
	 */
	public function crop($x, $y, $width, $height, $interlace = false) {

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

		if(false === is_bool($interlace)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($interlace)), E_USER_ERROR);
		}

		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


	/**
	 * Resize an image
	 * @param int $width
	 * @param int $height
	 * @param boolean $ratio
	 * @param boolean $interlace
	 * @return sFire\Image\Image
	 */
	public function resize($width, $height, $ratio = false, $interlace = false) {

		if(false === ('-' . intval($width) == '-' . $width)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($width)), E_USER_ERROR);
		}

		if(false === ('-' . intval($height) == '-' . $height)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($height)), E_USER_ERROR);
		}

		if(null !== $ratio && false === is_bool($ratio)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($ratio)), E_USER_ERROR);
		}

		if(false === is_bool($interlace)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($interlace)), E_USER_ERROR);
		}

		$this -> add(__FUNCTION__, func_get_args());

		return $this;
	}


	/**
	 * Rotates an image with a given angle
	 * @param int $degrees
	 * @param int $bgcolor
	 * @param int $transparent
	 * @return sFire\Image\Image
	 */
	public function rotate($degrees, $bgcolor = 0, $transparent = 0) {

		if(false === ('-' . intval($degrees) == '-' . $degrees)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($degrees)), E_USER_ERROR);
		}

		if(false === ('-' . intval($bgcolor) == '-' . $bgcolor)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($bgcolor)), E_USER_ERROR);
		}

		if(false === ('-' . intval($transparent) == '-' . $transparent)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($transparent)), E_USER_ERROR);
		}

		$this -> add(__FUNCTION__, func_get_args());
		return $this;
	}


    /**
     * Adds a new command to stack
     * @param string $method
     */
    private function add($method, $params) {
    	$this -> commands[] = (object) ['method' => $method, 'params' => $params];
    }


    /**
     * Check if image and driver is set and returns the driver
     * @return mixed
     */
    private function call() {

        if(null === $this -> driver) {
            return trigger_error('Driver is not set. Set the driver with the setDriver() method', E_USER_ERROR);
        }

        if(null === $this -> image) {
        	return trigger_error('Image has not been set. Set the image with the setImage() method', E_USER_ERROR);
        }

        if(null === $this -> driverInstance) {
        	$this -> driverInstance = new $this -> driver($this -> image);
        }

        return $this -> driverInstance;
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

		if(strtolower($extension) === 'png') {
			$quality = min(9, floor((100 - $quality) / 10));
		}

		return $quality;
	}
}
?>