<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   https://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Router;

use sFire\Entity\Method;
use sFire\HTTP\Request;
use sFire\HTTP\Response;
use sFire\Utils\URLParser;
use sFire\MVC\MVC;
use sFire\Router\Redirect;
use sFire\Router\Forward;

final class Router {


	/**
	 * @var sFire\Router\Router $instance
	 */
	private static $instance;


	/**
	 * @var sFire\Entity\Method $method
	 */
	private static $method;


	/**
	 * @var array $methods
	 */
	private static $methods = [];


	/**
	 * @var string|sFire\Utils\URLParser $url
	 */
	private static $url;


	/**
	 * @var array $params
	 */
	private static $params = [];


	/**
	 * @var array $types
	 */
	private static $types = [

		'int' 			=> '\d+',
		'float' 		=> '\d+\.\d{1,}',
		'boolean' 		=> 'true|false|0|1',
		'string' 		=> '.*?',
		'alphanumeric' 	=> '[a-zA-Z0-9]+'
	];


	/**
	 * Create and store new instance 
	 * @return sFire\Router\Router
	 */
	public static function Instance() {

        if(null === static :: $instance) {
			
			$class = __CLASS__;
			static :: $instance = new $class();
		}

		return static :: $instance;
    }


    /**
     * Add a new GET route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function get($url, $identifier, $closure = false) {
		return static :: method('get', $url, $identifier, $closure);
	}


	/**
     * Add a new POST route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function post($url, $identifier, $closure = false) {
		return static :: method('post', $url, $identifier, $closure);
	}


	/**
     * Add a new DELETE route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function delete($url, $identifier, $closure = false) {
		return static :: method('delete', $url, $identifier, $closure);
	}


	 /**
     * Add a new PUT route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function put($url, $identifier, $closure = false) {
		return static :: method('put', $url, $identifier, $closure);
	}


	/**
     * Add a new HEAD route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function head($url, $identifier, $closure = false) {
		return static :: method('head', $url, $identifier, $closure);
	}


	/**
     * Add a new CONNECT route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function connect($url, $identifier, $closure = false) {
		return static :: method('connect', $url, $identifier, $closure);
	}


	/**
     * Add a new OPTIONS route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function options($url, $identifier, $closure = false) {
		return static :: method('options', $url, $identifier, $closure);
	}


	/**
     * Add a new TRACE route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function trace($url, $identifier, $closure = false) {
		return static :: method('trace', $url, $identifier, $closure);
	}


	/**
     * Add a new PATCH route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function patch($url, $identifier, $closure = false) {
		return static :: method('patch', $url, $identifier, $closure);
	}


	/**
     * Add a new ANY route listener
     * @param string $url
     * @param string $indentifier
     * @param closure $closure
     * @return null
     */
	public static function any($url, $identifier, $closure = false) {
		return static :: method('any', $url, $identifier, $closure);
	}


	/**
	 * Set the params
	 * @param array $params
	 */
	public static function setParams($params) {

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		static :: $params = $params;
	}


	/**
	 * Retrieves the params
	 * @return array
	 */
	public static function getParams() {
		return static :: $params;
	}


	/**
	 * Simulate an internal request based on an route identifier
	 * @param string $identifier
	 * @return sFire\Router\Redirect
	 */
	public static function redirect($identifier) {
		return new Redirect($identifier);
	}


	/**
	 * Redirect the client to another url by given the route identifier, optional array with data and optional amount of time in seconds for the amount of time. Adds a header redirect if the amount of seconds is given or location if seconds equals 0 or null
	 * @param string $identifier
	 * @param array $data
	 * @param int|string|null $seconds
	 */
	public static function forward($identifier, $data = [], $seconds = null) {
		return new Forward($identifier);
	}

	
	/**
	 * Converts a route identifier to a string url
	 * @param string $identifier
	 * @param array $data
	 * @return string
	 */
	public static function url($identifier, $data = null, $domain = null) {

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(false === isset(static :: $methods[$identifier])) {
			return trigger_error(sprintf('Route identifier "%s" not found in routes.php', $identifier), E_USER_ERROR);
		}

		if(false === is_array($data)) {
			
			$data = func_get_args();
			array_shift($data);
		}

		$method 	= static :: $methods[$identifier];
		$url 		= $method -> getUrl();
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
				return trigger_error(sprintf('Incorrect number of parameters given for Router url with identifier "%s". Expecting %s, got %s with %s optional', $method -> getIdentifier(), count($matches[2]), count($data), $optionals), E_USER_ERROR);
			}

			foreach($matches[0] as $index => $match) {
				
				$match 		= $matches[0][$index];
				$type 		= $matches[1][$index];
				$optional 	= $matches[2][$index];
				$where 		= $method -> getWhere();
				$replace 	= static :: $types['string'];

				if(true === isset(static :: $types[$type])) {
					$replace = static :: $types[$type];
				}

				if(true === is_array($where)) {
					
					foreach($where as $regex) {
						
						if($regex['match'] === $type) {
							
							$replace = $regex['regex'];
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

		return (null !== $domain ? rtrim($domain, '/') . '/' : '') . $url;
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
	 * Gets the current route method
	 * @return Method
	 */
	public static function getCurrentRoute() {
		return static :: $method;
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
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or instance of sFire\\HTTP\\URLParser, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		return static :: $url = $url;
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

		if(false === isset(static :: $methods[$identifier])) {
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

		foreach(static :: $methods as $method) {

			$domains = $method -> getDomain();

			foreach($domains as $url) {

				if(preg_match('#'. str_replace('#', '\#', $url) .'#', $domain)) {
					return true;
				}
			}
		}

		return false;
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
	 * Set a new route (method) and executes an optional closure to set the method properties
	 * @param string $type
	 * @param string $url
	 * @param string $identifier
	 * @param closure $closure
	 * @return sFire\Entity\Method
	 */
	private static function method($type, $url, $identifier, $closure) {

		if(false === is_string($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false === is_string($url)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(true === isset(static :: $methods[$identifier])) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be a unique identifier. "%s" is already defined as a route in routes.php', __METHOD__, $identifier), E_USER_ERROR);
		}

		$method = new Method();
		$method -> setUrl($url) -> setIdentifier($identifier) -> setMethod($type);

		if(gettype($closure) == 'object') {
			call_user_func($closure, $method);
		}

		static :: $methods[$identifier] = $method;

		return $method;
	}


	/**
	 * Formats all the routes to objects
	 */
	private static function format() {
		
		foreach(static :: $methods as $index => $method) {

			$url = preg_quote($method -> getUrl(), '/'); 

			if(preg_match_all('#(\\\/)*\\\{([a-zA-Z0-9_.]+)(\\\\\?)?\\\}#i', $url, $matches)) {
				
				foreach($matches[0] as $index => $match) {

					$match 		= $matches[0][$index];
					$slash 		= $matches[1][$index];
					$type 		= $matches[2][$index];
					$optional 	= $matches[3][$index];
					$where 		= $method -> getWhere();
					$variable 	= '';
					$typefound 	= false;

					if('' !== $slash) {
						$variable .= '\/';
					}

					if(true === is_array($where)) {
						
						foreach($where as $regex) {
							
							if($regex['match'] === $type) {
								
								$variable .= $regex['regex'];
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

			$method -> setMatch($url);
		}
	}


	/**
	 * Try to match the current request uri to a route and execute the controller of the matched route. Returns true if match is found and false if not.
	 * @return boolean
	 */
	private static function work() {

		$domain = null;

		if($request = static :: getUrl()) {

			foreach(static :: $methods as $index => $method) {
				
				$domains = $method -> getDomain();
				$found 	 = false;

				if(0 === count($domains)) {
					$found = true;
				}

				foreach($domains as $domain) {

					if(preg_match('#'. str_replace('#', '\#', $domain) .'#', Request :: parseUrl('host'))) {
						
						$found  = true;
						$domain = $domain;

						break;
					}
				}

				if(true === $found) {

					if(Request :: getMethod() !== null && Request :: getMethod() !== $method -> getMethod() && $method -> getMethod() !== 'any') {
						continue;
					}

					$path = $request -> trimPath();

					if(true === $method -> getStrict()) {

						if(null !== $request -> getQuery()) {
							$path .= '?' . $request -> getQuery();
						}
					}

					if(preg_match('#^'. str_replace('#', '\#', $method -> getMatch()) .'$#', $path, $match)) {

						array_shift($match);

						$match = array_map(function($tmp) {
							return ltrim($tmp, '\/');
						}, $match);

						$match = array_unique($match);
						
						static :: setCurrentRoute($method);
						static :: setParams($match);

						//Check if route is viewable
						if(true === $method -> isViewable() || null === $method -> isViewable()) {
							
							new MVC($method, $match);
							return true;
						}

						break;
					}
				}
			}
		}

		//Trigger 404 if route exists
		if(true === static :: routeExists('404', $domain)) {

			$method = static :: getRouteMethod('404');
			$method -> setViewable(true);

			static :: redirect('404', null, null, $domain);

			return true;
		}

		//Else set HTTP status 404
		Response :: setStatus(404);

		return false;
	}


	/**
	 * Returns the route method by identifier
	 * @param string $identifier
	 * @return mixed
	 */
	private static function getRouteMethod($identifier) {

		if(false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(true === isset(static :: $methods[$identifier])) {
			return static :: $methods[$identifier];
		}
	}


	/**
	 * Sets the current route method 
	 * @param sFire\Entity\Method $method
	 */
	private static function setCurrentRoute(Method $method) {
		static :: $method = $method;
	}
}
?>