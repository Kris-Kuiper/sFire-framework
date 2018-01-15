<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   https://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Router;

use sFire\Router\Router;
use sFire\HTTP\Request;

class Redirect {


	/**
	 * @var string $identifier
	 */
	private $identifier;


	/**
	 * @var array $params
	 */
	private $params;
	

	/**
	 * @var string $domain
	 */
	private $domain;


	/**
	 * Constructor
	 * @param string $identifier
	 */
	public function __construct($identifier) {

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		//Check if identifier exists
		if(false === Router :: routeExists($identifier)) {
			return trigger_error(sprintf('Identifier "%s" does not exists', $identifier), E_USER_ERROR);	
		}

		$this -> identifier = $identifier;
	}


	/**
	 * Set the parameters
	 * @param array $param
	 * @return sFire\Router\Redirect
	 */
	public function params($params) {

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		$this -> params = $params;

		return $this;
	}


	/**
	 * Set the domain
	 * @param string $domain
	 * @return sFire\Router\Redirect
	 */
	public function domain($domain) {

		if(false === is_string($domain)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		//Check if identifier exists
		if(false === Router :: routeExists($this -> identifier, $domain)) {
			return trigger_error(sprintf('Identifier "%s" with domain "%s" does not exists', $this -> identifier, $domain), E_USER_ERROR);	
		}

		$this -> domain = $domain;

		return $this;
	}


	/**
	 * Simulate GET method
	 * @return sFire\Router\Redirect
	 */
	public function get() {
		return $this -> redirect('get');
	}


	/**
	 * Simulate POST method
	 * @return sFire\Router\Redirect
	 */
	public function post() {
		return $this -> redirect('post');
	}


	/**
	 * Simulate PUT method
	 * @return sFire\Router\Redirect
	 */
	public function put() {
		return $this -> redirect('put');
	}


	/**
	 * Simulate DELETE method
	 * @return sFire\Router\Redirect
	 */
	public function delete() {
		return $this -> redirect('delete');
	}


	/**
	 * Simulate OPTIONS method
	 * @return sFire\Router\Redirect
	 */
	public function options() {
		return $this -> redirect('options');
	}


	/**
	 * Simulate PATCH method
	 * @return sFire\Router\Redirect
	 */
	public function patch() {
		return $this -> redirect('patch');
	}


	/**
	 * Simulate CONNECT method
	 * @return sFire\Router\Redirect
	 */
	public function connect() {
		return $this -> redirect('connect');
	}


	/**
	 * Simulate TRACE method
	 * @return sFire\Router\Redirect
	 */
	public function trace() {
		return $this -> redirect('trace');
	}


	/**
	 * Simulate HEAD method
	 * @return sFire\Router\Redirect
	 */
	public function head() {
		return $this -> redirect('head');
	}


	/**
	 * Execute redirect
	 * @param string $method
	 */
	private function redirect($method) {

		$this -> setUrl();
		$this -> setDomain();
		$this -> setMethod($method);

		return Router :: execute();
	}


	/**
	 * Set the url based on the identifier and parameters
	 */
	private function setUrl() {

		$url = Router :: url($this -> identifier, $this -> params);

		if(strlen($url) > 0) {
			$url = '/' . $url;
		}

		Request :: setURI($url);
	}


	/**
	 * Set the domain
	 */
	private function setDomain() {

		if(null !== $this -> domain) {
			Request :: setHost($this -> domain);
		}
	}


	/**
	 * Set the method
	 * @param string $method
	 */
	private function setMethod($method) {
		Request :: setMethod($method);
	}
}
?>