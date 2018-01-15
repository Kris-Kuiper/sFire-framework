<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Router;

use sFire\Router\Router;

final class Domain {


	/**
	 * @var string $domain
	 */
	private $domain;


	/**
	 * @var string $module
	 */
	private $module;


	/**
	 * Constructor
	 * @param string $domain
	 * @param string $module
	 */
	public function __construct($domain = null, $module = null) {

		if(null !== $domain) {
			$this -> setDomain($domain);
		}

		if(null !== $module) {
			$this -> setModule($module);
		}
	}


	/**
	 * Set a new domain url
	 * @param string $domain
	 * @return sFire\Router\Domain
	 */
	public function setDomain($domain) {
		
		if(false === is_string($domain) && false === is_array($domain)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		$this -> domain = $domain;

		return $this;
	}


	/**
	 * Returns the domain
	 * @return string
	 */
	public function getDomain() {
		return $this -> domain;
	}


	/**
	 * Set a new module name for current domain
	 * @param string $module
	 * @return sFire\Router\Domain
	 */
	public function setModule($module) {
		
		if(false === is_string($module)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($module)), E_USER_ERROR);
		}
		
		$this -> module = $module;
		return $this;
	}


	/**
	 * Returns the module
	 * @return string
	 */
	public function getModule() {
		return $this -> module;
	}


	/**
     * Add a new ANY route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public function __call($type, $params) {

		if(false === is_callable([__NAMESPACE__ . '\Router', $type])) {
			return trigger_error(sprintf('Method "%s" does not exists', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(null === $this -> getDomain()) {
			return trigger_error('No domain was given. This needs to be set with the "setDomain" method first', E_USER_ERROR);
		}

		if(null === $this -> getModule()) {
			return trigger_error('No module name was given. This needs to be set with the "setModule" method first', E_USER_ERROR);
		}

		return call_user_func_array([__NAMESPACE__ . '\Router', $type], $params) -> setDomain($this -> getDomain()) -> setModule($this -> getModule());
	}
}
?>