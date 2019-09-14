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
	 * Add an array with HTTP methods route listener for combining get, post, put etc. to one route
	 * @param $methods array
	 * @param string $url
     * @param string $indentifier
	 * @return sFire\Routing\Extend\Route
	 */
	public function methods($methods, $url, $identifier) {

		if(false === is_array($methods)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($methods)), E_USER_ERROR);
		}

		foreach($methods as $index => $method) {

			if(false === is_string($method)) {
				return trigger_error(sprintf('Argument %s passed to %s() must be of the type string, "%s" given', $index, __METHOD__, gettype($method)), E_USER_ERROR);
			}
		}

		return $this -> method($methods, $url, $identifier);
	}


	/**
	 * Add middleware to a route
	 * @return sFire\Routing\Extend\Route
	 */
	public function middleware() {

		$middleware = func_get_args();

		foreach($middleware as $index => $item) {

			if(false === is_string($item)) {
				return trigger_error(sprintf('Argument %s passed to %s() must be of the type string, "%s" given', $index, __METHOD__, gettype($item)), E_USER_ERROR);
			}
		}

		$this -> attr['middleware'] = $middleware;

		return $this;
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
	public function assign($key, $value = null, $merge = true) {

		if(false === is_string($key) && false === is_array($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(null !== $value && false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(false === is_bool($merge)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($merge)), E_USER_ERROR);
		}

		if(true === is_string($key)) {
			$key = [$key => $value];
		}

		$group = Router :: getGroup();

		if(true === $merge && true === isset($group['assign'])) {
			$this -> attr['assign'] = $this -> arrayRecursiveMerge($key, $group['assign']);
		}
		else {
			$this -> attr['assign'] = $key;
		}

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
	 * Set or prepend the prefix
	 * @param string|null $url
	 * @param boolean $prepend
	 * @return sFire\Routing\Extend\Route
	 */
	public function prefix($url = null, $prepend = true) {

		if(null !== $url && false === is_string($url)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		if(false === is_bool($prepend)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($prepend)), E_USER_ERROR);
		}

		$prefix = $this -> getAttr('prefix');

		if(null !== $prefix) {

			if(true === $prefix['prepend'] && true === $prepend) {
				$url = $prefix['url'] . $url;
			}
		}

		$this -> attr['prefix'] = ['url' => $url, 'prepend' => $prepend];
		
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
	public function uses($identifier, $merge = true) {

		if(false === is_string($identifier) AND false === is_array($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(true === is_string($identifier)) {
			$identifier = [$identifier];
		}

		if(false === is_bool($merge)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($merge)), E_USER_ERROR);
		}

		foreach($identifier as $id) {

			if(false === is_string($id)) {
				return trigger_error(sprintf('One or more identifiers passed to the %s() method, are not of the type string, "%s" given', __METHOD__, gettype($id)), E_USER_ERROR);
			}
		}

		$this -> attr['uses'] = $identifier;

		$group = Router :: getGroup();

		if(true === $merge && true === isset($group['uses'])) {
			$this -> attr['uses'] = $this -> arrayRecursiveMerge($this -> attr['uses'], $group['uses']);
		}

		return $this;
	}
	

	/**
	 * Set the where
	 * @param string|array $key
	 * @param mixed $value
	 * @param boolean $merge
	 * @return sFire\Routing\Extend\Route
	 */
	public function where($key, $value = null, $merge = true) {

		if(false === is_string($key) && false === is_array($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(null !== $value && false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(false === is_bool($merge)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($merge)), E_USER_ERROR);
		}

		if(true === is_string($key)) {
			$key = [$key => $value];
		}

		$this -> attr['where'] = $key;

		$group = Router :: getGroup();

		if(true === $merge && true === isset($group['where'])) {
			$this -> attr['where'] = $this -> arrayRecursiveMerge($this -> attr['where'], $group['where']);
		}

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
	 * Set a new error route
	 * @param integer $type
	 * @param string $identifier
	 * @return sFire\Routing\Extend\Route
	 */
	public function error($type, $identifier, $default = false) {

		if(false === ('-' . intval($type) == '-' . $type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(false === is_bool($default)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($default)), E_USER_ERROR);
		}

		$this -> attr['method'] 	= 'any';
		$this -> attr['url'] 		= $identifier;
		$this -> attr['identifier'] = $identifier;

		$route = new Route();
		$route -> setAttr(array_merge(Router :: getGroup(), $this -> attr));
		$route -> viewable(false);

		Router :: addRoute($route);
		Router :: addErrorRoute($type, $route, $default);

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
     * Returns the method
     * @return null|string
     */
	public function getMethod() {
		return $this -> getAttr('method');
	}
	

	/**
     * Returns the assign
     * @return null|array
     */
	public function getAssign() {
		return $this -> getAttr('assign');
	}


	/**
     * Returns the middleware
     * @return null|array
     */
	public function getMiddleware() {
		return $this -> getAttr('middleware');
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
	 * Return a single params set with the assign/uses methods
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function getParam($key, $default = null) {

		$variables = $this -> getParams();

		if(true === isset($variables[$key])) {
			return $variables[$key];
		}


		return $default;
	}


	/**
	 * Return all params set with the assign/uses methods
	 * @return array
	 */
	public function getParams() {

		$uses 	= $this -> getAttr('uses');
		$assign = $this -> getAttr('assign') ? $this -> getAttr('assign') : [];
		$output = [];

		if(null !== $uses) {

			foreach($uses as $identifier) {
				$output = $this -> arrayRecursiveMerge($output, Router :: getRoute($identifier) -> getParams());
			}
		}

		$output = $this -> arrayRecursiveMerge($assign, $output);

		return $output;
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
	 * @param string $method
	 * @param string $url
	 * @param string $identifier
	 * @return sFire\Routing\Extend\Route
	 */
	private function method($method, $url, $identifier) {

		if(false === is_string($method) && false === is_array($method)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the method string or array, "%s" given', __METHOD__, gettype($method)), E_USER_ERROR);
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

		$this -> attr['method'] 	= $method;
		$this -> attr['url'] 		= $url;
		$this -> attr['identifier'] = $identifier;
		
		$route = new Route();
		$route -> setAttr(array_merge(Router :: getGroup(), $this -> attr));

		Router :: addRoute($route);

		return $route;
	}


	/**
	 * Recursive merge arrays
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	private function arrayRecursiveMerge($array1, $array2) {

		if(false === is_array($array1) || false === is_array($array2)) { 
			return $array2; 
		}

		foreach($array2 as $key => $value) {
			$array1[$key] = $this -> arrayRecursiveMerge(@$array1[$key], $value);
		}

		return $array1;
	}
}
?>
