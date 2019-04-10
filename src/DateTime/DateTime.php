<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
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


	/**
	 * Magic method for returning the date
	 * @return string
	 */
	public function __toString() {
		return $this -> loadFormat();
	}
}
?>