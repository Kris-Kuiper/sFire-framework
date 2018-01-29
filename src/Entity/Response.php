<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Entity;

use sFire\Entity\Entity;

final class Response extends Entity {


	/**
	 * @var array $headers
	 */
	private $headers = [];


	/**
	 * @var array $status
	 */
	private $status = [];


	/**
	 * @var string $body
	 */
	private $body;


	/**
	 * @var array $cookies
	 */
	private $cookies = [];


	/**
	 * @var string $response
	 */
	private $response;


	/**
	 * @var array $info
	 */
	private $info = [];

	
	/**
	 * Set headers
	 * @param array $headers
	 * @return sFire\Entity\Client
	 */		
	public function setHeaders($headers) {
		
		$this -> headers = $headers;
		return $this;
	}

	
	/**
	 * Set status
	 * @param array $status
	 * @return sFire\Entity\Client
	 */		
	public function setStatus($status) {
		
		$this -> status = $status;
		return $this;
	}


	/**
	 * Set cookies
	 * @param array $cookies
	 * @return sFire\Entity\Client
	 */		
	public function setCookies($cookies) {
		
		$this -> cookies = $cookies;
		return $this;
	}

	
	/**
	 * Set body
	 * @param string $body
	 * @return sFire\Entity\Client
	 */		
	public function setBody($body) {
		
		$this -> body = $body;
		return $this;
	}


	/**
	 * Set info
	 * @param string $info
	 * @return sFire\Entity\Client
	 */		
	public function setInfo($info) {
		
		$this -> info = $info;
		return $this;
	}


	/**
	 * Set response
	 * @param string $response
	 * @return sFire\Entity\Client
	 */		
	public function setResponse($response) {
		
		$this -> response = $response;
		return $this;
	}

	
	/**
	 * Return body
	* @return string
	*/	
	public function getBody() {
		return $this -> body;
	}

	
	/**
	 * Return headers
	 * @return array 
	 */		
	public function getHeaders() {
		return $this -> headers;
	}

	
	/**
	 * Return status
	 * @return array 
	 */		
	public function getStatus() {
		return $this -> status;
	}


	/**
	 * Return info
	 * @return array 
	 */		
	public function getInfo() {
		return $this -> info;
	}


	/**
	 * Return cookies
	 * @return array 
	 */		
	public function getCookies() {
		return $this -> cookies;
	}


	/**
	 * Return response
	 * @return array 
	 */		
	public function getResponse() {
		return $this -> response;
	}
}
?>