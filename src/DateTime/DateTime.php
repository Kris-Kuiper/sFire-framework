<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\DateTime;

class DateTime extends \DateTime {


	/**
	 * @var string $format
	 */
	private $format;


	/**
	 * Saves the format for later use
	 * @param string $format
	 */
	public function saveFormat($format) {
		$this -> format = $format;
	}


	/**
	 * Converts datetime object to string with previously saved format
	 * @return string
	 */
	public function loadFormat() {
		return $this -> format($this -> format);
	}
}
?>