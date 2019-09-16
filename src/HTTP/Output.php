<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\HTTP;

use sFire\HTTP\Response;
use sFire\Entity\Result;

class Output extends Result {


	/**
	 * @var int $httpcode
	 */
	private $httpcode = 200;


	/**
	 * Magic method to convert to JSON
	 */
	public function __toString() {

		Response :: addHeader('Content-type', 'application/json');
		return $this -> toJson();
	}


	/**
	 * Returns the httpcode
	 * @return array
	 */
	public function getHttpCode() {
		return $this -> httpcode;
	}


	/**
	 * Sets the httpcode
	 * @param integer $httpcode
	 * @return $this
	 */
	public function setHttpCode($httpcode) {
		
		Response :: setStatus($httpcode);

		$this -> httpcode = $httpcode;
		return $this;
	}
}