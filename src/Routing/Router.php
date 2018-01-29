<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   https://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Routing;

use sFire\Routing\Routes;
use sFire\Routing\Extend\Route;
use sFire\Routing\Extend\Redirect;
use sFire\Routing\Extend\Forward;
use sFire\HTTP\Request;
use sFire\HTTP\Response;
use sFire\Utils\URLParser;
use sFire\MVC\MVC;

final class Router {


	/**
	 * @var sFire\Routing\Router $instance
	 */
	private static $instance;


	/**
	 * @var string|sFire\Utils\URLParser $url
	 */
	private static $url;


	/**
	 * @var array $routes
	 */
	public static $routes;


	/**
	 * @var sFire\Routing\Extend\Route $route
	 */
	private static $route;


	/**
	 * @var array $group
	 */
	private static $group = [];


	/**
	 * @var array $types
	 */
	private static $types = [

		'int' 			=> '\d+',
		'float' 		=> '\d+\.\d{1,}',
		'boolean' 		=> 'true|false|0|1',
		'string' 		=> '[^?]',
		'alphanumeric' 	=> '[a-zA-Z0-9]+'
	];


	/**
	 * Create and store new instance 
	 * @return sFire\Routing\Router
	 */
	public static function Instance() {

        if(null === static :: $instance) {
			
			$class = __CLASS__;
			static :: $instance = new $class();
		}

		return static :: $instance;
    }


    /**
     * Creates a new route and calls the dynamic given method if exists
     * @param string $method
     * @param array $arguments
     * @return sFire\Routing\Extend\Route
     */
    public static function __callstatic($method, $arguments) {

    	$route = new Route();

    	if(false === is_callable([$route, $method])) {
			return trigger_error(sprintf('Call to undefined method %s::%s', __CLASS__, $method), E_USER_ERROR);
    	}

    	call_user_func_array([$route, $method], $arguments);

    	return $route;
    }


    /**
	 * Returns the current URL
	 * @return sFire\Utils\URLParser
	 */
	public static function getUrl() {
		return static :: $url;
	}


	/**
	 * Set the current URL
	 * @param string|sFire\Utils\URLParser $url
	 * @return sFire\Utils\URLParser
	 */
	public static function setUrl($url) {

		if(false === is_string($url) && false === $url instanceof URLParser) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or instance of sFire\\Utils\\URLParser, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		if(true === is_string($url)) {
			$url = new URLParser($url);
		}

		static :: $url = $url;
		return $url;
	}


    /**
	 * Simulate an internal request based on a route identifier
	 * @param string $identifier
	 * @return sFire\Routing\Redirect
	 */
	public static function redirect($identifier) {
		return new Redirect($identifier);
	}


	/**
	 * Redirect the client to another url by given a route identifier, optional array with data and optional amount of time in seconds for the amount of time. Adds a header redirect if the amount of seconds is given or location if seconds equals 0 or null
	 * @param string $identifier
	 * @param array $data
	 * @param int|string|null $seconds
	 */
	public static function forward($identifier) {
		return new Forward($identifier);
	}


    /**
	 * Converts a route identifier to a string url, optional data array and optional domain
	 * @param string $identifier
	 * @param array $data
	 * @param string $domain
	 * @return string
	 */
	public static function url($identifier, $data = null, $domain = null) {

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(false === static :: routeExists($identifier)) {
			return trigger_error(sprintf('Route identifier "%s" not found in routes.php', $identifier), E_USER_ERROR);
		}

		if(null !== $data && false === is_array($data)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		if(null !== $domain && false === is_string($domain)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		if(null === $data) {
			$data = [];
		}

		$route 		= static :: getRoute($identifier);
		$url 		= $route -> getUrl();
		$parameters = 0;
		$optionals  = 0;

		if(preg_match_all('#\{(.*?)(\?)?\}#i', $url, $matches)) {
			
			foreach($matches[2] as $optional) {

				if('?' === $optional) {

					$optionals++;
					continue;
				}
				
				$parameters++;
			}

			if(count($data) < $parameters) {
				return trigger_error(sprintf('Incorrect number of parameters given for Router url with identifier "%s". Expecting %s, got %s with %s optional', $route -> getIdentifier(), count($matches[2]), count($data), $optionals), E_USER_ERROR);
			}

			foreach($matches[0] as $index => $match) {
				
				$match 		= $matches[0][$index];
				$type 		= $matches[1][$index];
				$optional 	= $matches[2][$index];
				$where 		= $route -> getWhere();
				$replace 	= static :: $types['string'];

				if(true === isset(static :: $types[$type])) {
					$replace = static :: $types[$type];
				}

				if(true === is_array($where)) {
						
					foreach($where as $key => $regex) {
						
						if($key === $type) {

							$replace =$regex;
							break;
						}
					}
				}

				if(true === isset($data[$index])) {

					if(false === is_string($data[$index]) && false === is_numeric($data[$index])) {
						return trigger_error(sprintf('Router url width id "%s" expects parameters to be a String', $method -> getIdentifier()));
					}

					if(preg_match('#' . $replace . '#i', $data[$index])) {
						$url = preg_replace('#'. preg_quote($match) .'#i', $data[$index], $url, 1);
					}
					else {
						return trigger_error(sprintf('Parameters given to router url width id "%s" do not match. Trying to match regular expression pattern "%s" with "%s" as subject', $method -> getIdentifier(), $replace, $data[$index]));
					}
				}
				elseif('' !== $optional) {
					$url = preg_replace('#'. '/?' . preg_quote($match) .'#i', '', $url, 1);
				}
			}
		}

		if(null !== $domain && false === static :: domainExists($domain)) {
			return trigger_error(sprintf('Domain "%s" does not exists in routes.php', $domain), E_USER_ERROR);
		}

		return (null !== $domain ? rtrim($domain, '/') . '/' : '') . $url;
	}


    /**
     * Check if a Route exists by identifier with optional domain
     * @param string $identifier
     * @param string|null $domain
     * @return boolean
     */
    public static function routeExists($identifier, $domain = null) {

    	if(false === is_string($identifier)) {
    		return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
    	}

    	if(null !== $domain && false === is_string($domain)) {
    		return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
    	}

    	$routes = static :: getRoutes();

    	if(false === isset($routes[$identifier])) {
    		return false;
    	}

    	if(null !== $domain) {
    		return static :: domainExists($domain);
    	}

    	return true;
    }


    /**
	 * Checks if a given domain exists
	 * @param string $domain
	 * @return boolean
	 */
	public static function domainExists($domain) {

		if(false === is_string($domain)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($domain)), E_USER_ERROR);
		}

		$routes = static :: getRoutes();

		foreach($routes as $route) {

			$domains = $route -> getDomain();

			foreach($domains as $url) {

				if(preg_match('#'. str_replace('#', '\#', $url) .'#', $domain) || $url === $domain) {
					return true;
				}
			}
		}

		return false;
	}


	/**
     * Add or update a new or current route based on identifier
     * @param sFire\Routing\Extend\Route $route
     */
	public static function addRoute(Route $route) {
		static :: $routes[$route -> getIdentifier()] = $route;
	}


	/**
	 * Execute the routing
	 */
	public static function execute() {

		static :: request();
		static :: format();
		return static :: work();
	}


	/**
	 * Gets the current route or a route based on identifier
	 * @param null|string $identifier
	 * @return sFire\Routing\Extend\Method
	 */
	public static function getRoute($identifier = null) {

		if(null !== $identifier && false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(null !== $identifier) {

			if(false === static :: routeExists($identifier)) {
				return trigger_error(sprintf('Route identifier "%s" not found in routes.php', $identifier), E_USER_ERROR);
			}

			return static :: $routes[$identifier];
		}

		return static :: $route;
	}


	/**
	 * Add a new group element to the stack
	 * @param array $group
	 */
	public static function addGroup($group) {
		
		if(false === is_array($group)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($group)), E_USER_ERROR);
		}

		static :: $group[] = $group;
	}


	/**
	 * Remove last group element from the stack
	 */
	public static function closeGroup() {
		array_pop(static :: $group);

	}


	/**
	 * Return all the groups as one merged array
	 * @return array
	 */
	public static function getGroup() {
		
		$attr = [];

		foreach(static :: $group as $group) {
			$attr = static :: arrayRecursiveMerge($attr, $group);
		}

		return $attr;
	}


	/**
	 * Recursive merge arrays
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	private static function arrayRecursiveMerge($array1, $array2) {

		if(false === is_array($array1) || false === is_array($array2)) { 
			return $array2; 
		}

		foreach($array2 as $key => $value) {
			$array1[$key] = static :: arrayRecursiveMerge(@$array1[$key], $value);
		}

		return $array1;
	}


	/**
	 * Try to add the request uri
	 */
	private static function request() {

		if($uri = Request :: getUri()) {

			$url = '';

			if(!($scheme = Request :: getScheme())) {
				
				$scheme = 'php';
				Request :: setScheme($scheme);
			}

			$url .= $scheme . '://';

			if($host = Request :: getHost()) {
				$url .= $host;
			}

			$url .= $uri;

			static :: $url = new URLParser($url);
		}
	}


	/**
	 * Formats all the routes urls to a regex pattern that sFire understands to match the current url to the route url
	 */
	private static function format() {

		$routes = static :: getRoutes();

		foreach($routes as $identifier => $route) {

			//Prepend prefix for url
			$url = $route -> getPrefix() . preg_quote($route -> getUrl(), '/'); 

			if(preg_match_all('#(\\\/)*\\\{([a-zA-Z0-9_.]+)(\\\\\?)?\\\}#i', $url, $matches)) {
				
				foreach($matches[0] as $index => $match) {

					$match 		= $matches[0][$index];
					$slash 		= $matches[1][$index];
					$type 		= $matches[2][$index];
					$optional 	= $matches[3][$index];
					$where 		= $route -> getWhere();
					$variable 	= '';
					$typefound 	= false;

					if('' !== $slash) {
						$variable .= '\/';
					}

					if(true === is_array($where)) {
						
						foreach($where as $key => $regex) {
							
							if($key === $type) {
								
								$variable .= $regex;
								$typefound = true;
								break;
							}
						}
					}

					if(false === $typefound) {

						if(isset(static :: $types[$type])) {
							$variable .= static :: $types[$type];
						}
						else {
							$variable .= static :: $types['string'];	
						}
					}

					if('' !== $optional) {
						$variable = '('. $variable .')?';
					}
					else {
						$variable = '('. $variable .')';
					}
					
					$url = preg_replace('#' . preg_quote($match, '/') . '#', $variable, $url, 1);
				}
			}

			$route -> setMatch($url);
		}
	}


	/**
	 * Try to match the current request uri to a route and execute the controller of the matched route. Returns true if match is found and false if not.
	 * @return boolean
	 */
	private static function work() {

		$domain = null;

		if($request = static :: getUrl()) {

			$routes = static :: getRoutes();

			foreach($routes as $identifier => $route) {
				
				$domains = $route -> getDomain();
				$found 	 = false;

				if(null === $domains || 0 === count($domains)) {
					$found = true;
				}

				if(true === is_array($domains)) {

					foreach($domains as $domain) {

						if(1 === preg_match('#'. str_replace('#', '\#', $domain) .'#', Request :: parseUrl('host'))) {
							
							$found  = true;
							$domain = $domain;

							break;
						}
					}
				}

				if(true === $found) {

					if(Request :: getMethod() !== null && Request :: getMethod() !== $route -> getType() && $route -> getType() !== 'any') {
						continue;
					}

					$path = $request -> trimPath();

					if(true === $route -> getStrict()) {

						if(null !== $request -> getQuery()) {
							$path .= '?' . $request -> getQuery();
						}
					}

					if(preg_match('#^'. str_replace('#', '\#', $route -> getMatch()) .'$#', $path, $match)) {

						array_shift($match);

						$match = array_map(function($tmp) {
							return ltrim($tmp, '\/');
						}, $match);

						$match = array_unique($match);
						
						static :: setRoute($route);

						//Check if route is viewable
						if(true === $route -> isViewable() || null === $route -> getViewable()) {
							
							new MVC($route, $match);
							return true;
						}

						break;
					}
				}
			}
		}

		//Trigger 404 if route exists
		if(true === static :: routeExists('404', $domain)) {

			$route = static :: getRoute('404');
			$route -> setViewable(true);

			static :: redirect('404', null, null, $domain) -> get();

			return true;
		}

		//Else set HTTP status 404
		Response :: setStatus(404);

		return false;
	}


	/**
	 * Sets the current route 
	 * @param sFire\Routing\Extend\Route $route
	 */
	private static function setRoute(Route $route) {
		static :: $route = $route;
	}


	/**
	 * Returns an array with all the routes
	 * @return array
	 */
	private static function getRoutes() {
		return static :: $routes;
	}
}
?>