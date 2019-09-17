<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   https://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Routing\Extend;

use sFire\Routing\Router;
use sFire\HTTP\Response;
use sFire\HTTP\Request;
use sFire\Utils\URLParser;

class Forward {


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
	 * @var int|string $seconds
	 */
	private $seconds;


	/**
	 * @var string $url
	 */
	private $url;


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
	 * @return sFire\Router\Forward
	 */
	public function params($params) {

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		$this -> params = $params;

		return $this;
	}


	/**
	 * Set the domain name with HTTP protocol
	 * @param string $domain
	 * @param string $protocol
	 * @return sFire\Router\Forward
	 */
	public function domain($domain, $protocol = null) {

		if(false === is_string($domain)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		//Check if identifier exists
		if(false === Router :: routeExists($this -> identifier, $domain)) {
			return trigger_error(sprintf('Identifier "%s" with domain "%s" does not exists', $this -> identifier, $domain), E_USER_ERROR);	
		}

		if(null !== $protocol && false === is_string($protocol)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($protocol)), E_USER_ERROR);
		}

		if(null === $protocol) {
			$protocol = Request :: getScheme();
		}

		$this -> domain = sprintf('%s://%s', $protocol, $domain);

		return $this;
	}


	/**
	 * Set the amount of seconds
	 * @param number $seconds
	 * @return sFire\Router\Forward
	 */
	public function seconds($seconds) {

		if(false === ('-' . intval($seconds) == '-' . $seconds)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($seconds)), E_USER_ERROR);
   		}

		$this -> seconds = $seconds;

		return $this;
	}
	

	/**
	 * Execute forward, with options HTTP status code
	 * @param integer $code
	 */
	public function execute($code = null) {

		if(null !== $code && false === ('-' . intval($code) == '-' . $code)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($code)), E_USER_ERROR);
		}

		$this -> setUrl();

		if(null !== $this -> seconds) {
			return Response :: addHeader('refresh', sprintf('%d;url=%s', $this -> seconds, $this -> url), $code);
		}

		Response :: addHeader('Location', $this -> url, $code);
	}


	/**
	 * Set the url based on the identifier and parameters
	 */
	private function setUrl() {

		$this -> url = Router :: url($this -> identifier, $this -> params, $this -> domain);

		$url = new URLParser($this -> url);

		if(null === $url -> getScheme() && strlen($this -> url) > 0) {
			$this -> url = '/' . $this -> url;
		}
	}
}