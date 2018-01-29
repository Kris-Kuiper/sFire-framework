<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   https://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Routing\Extend;

use sFire\Routing\Router;

final class Route {

	
	/**
	 * @var array $attr
	 */
	private $attr = [];


	/**
     * Add a new GET route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function get($url, $identifier) {
		return $this -> method('get', $url, $identifier);
	}


	/**
     * Add a new POST route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function post($url, $identifier) {
		return $this -> method('post', $url, $identifier);
	}


	/**
     * Add a new DELETE route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function delete($url, $identifier) {
		return $this -> method('delete', $url, $identifier);
	}


	 /**
     * Add a new PUT route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function put($url, $identifier) {
		return $this -> method('put', $url, $identifier);
	}


	/**
     * Add a new HEAD route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function head($url, $identifier) {
		return $this -> method('head', $url, $identifier);
	}


	/**
     * Add a new CONNECT route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function connect($url, $identifier) {
		return $this -> method('connect', $url, $identifier);
	}


	/**
     * Add a new OPTIONS route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function options($url, $identifier) {
		return $this -> method('options', $url, $identifier);
	}


	/**
     * Add a new TRACE route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function trace($url, $identifier) {
		return $this -> method('trace', $url, $identifier);
	}


	/**
     * Add a new PATCH route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function patch($url, $identifier) {
		return $this -> method('patch', $url, $identifier);
	}


	/**
     * Add a new any route listener
     * @param string $url
     * @param string $indentifier
     * @return sFire\Routing\Extend\Route
     */
	public function any($url, $identifier) {
		return $this -> method('any', $url, $identifier);
	}


	/**
     * Sets the match
     * @param string $match
     * @return sFire\Routing\Extend\Route
     */
	public function setMatch($match) {

		if(false === is_string($match)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($match)), E_USER_ERROR);
		}

		$this -> attr['match'] = $match;

		return $this;
	}

	
	/**
     * Returns the match
     * @return string
     */
	public function getMatch() {
		return $this -> getAttr('match');
	}


	/**
     * Sets viewable
     * @param boolean $viewable
     * @return sFire\Routing\Extend\Route
     */
	public function setViewable($viewable) {
		
		if(false === is_bool($viewable)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type bool, "%s" given', __METHOD__, gettype($viewable)), E_USER_ERROR);
		}

		$this -> attr['viewable'] = $viewable;

		return $this;
	}

	
	/**
	 * Returns if router is viewable
	 * @return boolean
	 */
	public function isViewable() {
		return $this -> getAttr('viewable') ? true : false;
	}


	/**
	 * Set the action
	 * @param string $action
	 * @return sFire\Routing\Extend\Route
	 */
	public function action($action) {

		if(false === is_string($action)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($action)), E_USER_ERROR);
		}

		$this -> attr['action'] = $action;

		return $this;
	}


	/**
	 * Set the assign
	 * @param string|array $key
	 * @param mixed $value
	 * @return sFire\Routing\Extend\Route
	 */
	public function assign($key, $value = null) {

		if(false === is_string($key) && false === is_array($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(null !== $value && false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(true === is_string($key)) {
			$key = [$key => $value];
		}

		$this -> attr['assign'] = $key;

		return $this;
	}
	

	/**
	 * Set the module
	 * @param string $module
	 * @return sFire\Routing\Extend\Route
	 */
	public function module($module) {

		if(false === is_string($module)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($module)), E_USER_ERROR);
		}

		$this -> attr['module'] = $module;

		return $this;
	}
	

	/**
	 * Set the controller
	 * @param string $controller
	 * @return sFire\Routing\Extend\Route
	 */
	public function controller($controller) {

		if(false === is_string($controller)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($controller)), E_USER_ERROR);
		}

		$this -> attr['controller'] = $controller;

		return $this;
	}
	

	/**
	 * Set the domain
	 * @param string|array $domain
	 * @return sFire\Routing\Extend\Route
	 */
	public function domain($domain) {

		if(false === is_string($domain) && false === is_array($domain)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		if(true === is_string($domain)) {
			$domain = [$domain];
		}

		$this -> attr['domain'] = $domain;

		return $this;
	}
	

	/**
	 * Set the prefix
	 * @param string $prefix
	 * @return sFire\Routing\Extend\Route
	 */
	public function prefix($prefix) {

		if(false === is_string($prefix)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($prefix)), E_USER_ERROR);
		}

		$this -> attr['prefix'] = $prefix;

		return $this;
	}
	

	/**
	 * Set viewable
	 * @param boolean $viewable
	 * @return sFire\Routing\Extend\Route
	 */
	public function viewable($viewable) {

		if(false === is_bool($viewable)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($viewable)), E_USER_ERROR);
		}

		$this -> attr['viewable'] = $viewable;

		return $this;
	}
	

	/**
	 * Set strict mode
	 * @param boolean $strict
	 * @return sFire\Routing\Extend\Route
	 */
	public function strict($strict) {

		if(false === is_bool($strict)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($strict)), E_USER_ERROR);
		}

		$this -> attr['strict'] = $strict;

		return $this;
	}
	

	/**
	 * Set the uses
	 * @param string|array $identifier
	 * @return sFire\Routing\Extend\Route
	 */
	public function uses($identifier) {

		if(false === is_string($identifier) AND false === is_array($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(true === is_string($identifier)) {
			$identifier = [$identifier];
		}

		foreach($identifier as $id) {

			if(false === is_string($id)) {
				return trigger_error(sprintf('One or more identifiers passed to the %s() method, are not of the type string, "%s" given', __METHOD__, gettype($id)), E_USER_ERROR);
			}
		}

		$this -> attr['uses'] = $uses;

		return $this;
	}
	

	/**
	 * Set the where
	 * @param string|array $where
	 * @return sFire\Routing\Extend\Route
	 */
	public function where($key, $value = null) {

		if(false === is_string($key) && false === is_array($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(null !== $value && false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(true === is_string($key)) {
			$key = [$key => $value];
		}

		$this -> attr['where'] = $key;

		return $this;
	}


	/**
	 * Start a new group for adding routes
	 * @param closure $closure
     * @return sFire\Routing\Extend\Route
	 */
	public function group($closure) {

		if(false === is_callable($closure)) {
			return trigger_error(sprintf('Argument 1 passed to %s() is not a callable function', __METHOD__), E_USER_ERROR);
		}

		Router :: addGroup($this -> attr);

		$route = new Route();
		$route -> setAttr(Router :: getGroup());
		$closure($route);

		Router :: closeGroup();

		$this -> attr = [];

		return $route;
	}


	/**
     * Returns the module
     * @return null|string
     */
	public function getModule() {
		return $this -> getAttr('module');
	}


	/**
     * Returns strict mode
     * @return null|boolean
     */
	public function getStrict() {
		return $this -> getAttr('strict');
	}
	

	/**
     * Returns the controller
     * @return null|string
     */
	public function getController() {
		return $this -> getAttr('controller');
	}
	

	/**
     * Returns the action
     * @return null|string
     */
	public function getAction() {
		return $this -> getAttr('action');
	}
	

	/**
     * Returns viewable
     * @return null|boolean
     */
	public function getViewable() {
		return $this -> getAttr('viewable');
	}
	

	/**
     * Returns the domain
     * @return null|array
     */
	public function getDomain() {
		return $this -> getAttr('domain');
	}
	

	/**
     * Returns the prefix
     * @return null|array
     */
	public function getPrefix() {
		return $this -> getAttr('prefix');
	}
	

	/**
     * Returns the identifier
     * @return null|string
     */
	public function getIdentifier() {
		return $this -> getAttr('identifier');
	}
	

	/**
     * Returns the url
     * @return null|string
     */
	public function getUrl() {
		return $this -> getAttr('url');
	}
	

	/**
     * Returns the type
     * @return null|string
     */
	public function getType() {
		return $this -> getAttr('type');
	}
	

	/**
     * Returns the assign
     * @return null|array
     */
	public function getAssign() {
		return $this -> getAttr('assign');
	}
	

	/**
     * Returns the uses
     * @return null|array
     */
	public function getUses() {
		return $this -> getAttr('uses');
	}
	

	/**
     * Returns the where
     * @return null|array
     */
	public function getWhere() {
		return $this -> getAttr('where');
	}
	

	/**
	 * Retrieve a value from the attr array
	 * @param string $type
	 * @return mixed
	 */
	private function getAttr($type) {

		if(true === isset($this -> attr[$type])) {
			return $this -> attr[$type];
		}

		return null;
	}


	/**
	 * Set all the attributes
	 * @param array $attr
	 */
	public function setAttr($attr) {

		if(false === is_array($attr)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($attr)), E_USER_ERROR);
		}

		$this -> attr = $attr;
	}


	/**
	 * Sets a new route method
	 * @param string $type
	 * @param string $url
	 * @param string $identifier
	 * @return sFire\Routing\Extend\Route
	 */
	private function method($type, $url, $identifier) {

		if(false === is_string($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false === is_string($url)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(true === Router :: routeExists($identifier)) {
			return trigger_error(sprintf('Route with identifier "%s" already exists', $identifier), E_USER_ERROR);
		}

		$this -> attr['type'] 		= $type;
		$this -> attr['url'] 		= $url;
		$this -> attr['identifier'] = $identifier;

		$route = new Route();
		$route -> setAttr(array_merge(Router :: getGroup(), $this -> attr));

		Router :: addRoute($route);

		return $route;
	}
}
?>