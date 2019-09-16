<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\MVC;

use sFire\Config\Config;
use sFire\Routing\Extend\Route;
use sFire\Template\Template;
use sFire\Config\Path;
use sFire\Config\Middleware;
use sFire\Config\Files;
use sFire\Application\Application;

#MVC
final class MVC {


	/**
	 * Constructor
	 * @param Route $route
	 * @param array $matches
	 */
	public function __construct(Route $route, $matches = []) {

		//Validate if the params is an array
		if(false === is_array($matches)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($matches)), E_USER_ERROR);
		}

		//Validates if the current method has a module set
		if(null === $route -> getModule()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid module name, set with module() method in routes.php', $route -> getIdentifier()), E_USER_ERROR);
		}

		//Validates if the current method has a controller set
		if(null === $route -> getController()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid controller, set with controller() method in routes.php', $route -> getIdentifier()), E_USER_ERROR);
		}

		//Validates if the current method has a action set
		if(null === $route -> getAction()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid action method, set with action() method in routes.php', $route -> getIdentifier()), E_USER_ERROR);
		}

		if('' === $route -> getModule()) {
			$route -> setModule(key(Config :: all()));
		}
		
		//Intialise Middleware Container
		MiddlewareContainer :: modus('before');
		MiddlewareContainer :: matches($matches);
		
		//Preload all middleware 
		$this -> preloadMiddleware($route);

		//Execute all before methods from all middleware
		MiddlewareContainer :: next();
		
		//Check if all the before middleware has been executed and that the last middleware want's to go to the controller by calling the next method
		if(false === MiddlewareContainer :: isEmpty()) {
			return;
		}

		//Load the controller
		$namespace = $this -> loadController($route -> getModule(), $route -> getController());

		if(true === class_exists($namespace)) {
			
			$controller = new $namespace;

			//Execute the controller
			$this -> executeController($controller, $route, $matches);

			//Execute the after middleware
			MiddlewareContainer :: modus('after');
			MiddlewareContainer :: next();

			return ;
		}

		trigger_error(sprintf('Controller "%s" with module "%s" does not exists for "%s" as identifier in routes.php', $route -> getController(), $route -> getModule(), $route -> getIdentifier()), E_USER_ERROR);
	}


	/**
	 * Loads controller by module name and controller name
	 * @param string $module
	 * @param string $controller
	 * @return string
	 */
	public function loadController($module, $controller) {

		if(false === is_string($module)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($module)), E_USER_ERROR);
		}

		if(false === is_string($controller)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($controller)), E_USER_ERROR);
		}

		$folders 	= explode('.', $controller); //Convert dots to directory separators
		$amount 	= count($folders) - 1;
		$namespace	= '';

		foreach($folders as $index => $folder) {

			if($amount === $index) {
				
				$controller = Application :: get(['prefix', 'controller']) . $folder;
				$namespace .= $controller;

				break;
			}

			$namespace 	.= $folder . DIRECTORY_SEPARATOR;
		}

		$controller = str_replace(DIRECTORY_SEPARATOR, '\\', $module . DIRECTORY_SEPARATOR . Application :: get(['directory', 'controller']) . $namespace);
			
		return $controller;
	}


	/**
	 * Loads middleware by module name and middleware name
	 * @param string $module
	 * @param string $middelware
	 * @return string
	 */
	public function loadMiddleware($module, $middelware) {

		if(false === is_string($module)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($module)), E_USER_ERROR);
		}

		if(false === is_string($middelware)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($middelware)), E_USER_ERROR);
		}

		$folders 	= explode('.', $middelware); //Convert dots to directory separators
		$amount 	= count($folders) - 1;
		$namespace	= '';

		foreach($folders as $index => $folder) {

			if($amount === $index) {
				
				$middleware = Application :: get(['prefix', 'middleware']) . $folder;
				$namespace .= $middleware;

				break;
			}

			$namespace 	.= $folder . DIRECTORY_SEPARATOR;
		}

		$middleware = str_replace(DIRECTORY_SEPARATOR, '\\', $module . DIRECTORY_SEPARATOR . Application :: get(['directory', 'middleware']) . $namespace);

		return $middleware;
	}


	/**
	 * Executes default controller functions
	 * @param object $controller
	 * @param sFire\Routing\Extend\Route $method
	 * @param array $matches
	 */
	private function executeController($controller, Route $method, $matches) {

		if(false === is_object($controller)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type object, "%s" given', __METHOD__, gettype($controller)), E_USER_ERROR);
		}

		if(false === is_array($matches)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($matches)), E_USER_ERROR);
		}

		//Load the module dispatch file if exists and readable
		$this -> loadBoot($method -> getModule());

		//Execute start method if controller supports it
		if(true === is_callable([$controller, '__start'])) {
			call_user_func_array([$controller, '__start'], []);
		}

		//Execute main function
		$action = Application :: get(['prefix', 'action']) . $method -> getAction();

		//Trigger error if main function does not exists
		if(false === is_callable([$controller, $action])) {
			return trigger_error(sprintf('Method "%s" does not exists in "%s" controller', Application :: get(['prefix', 'action']) . ucfirst($method -> getAction()), $method -> getController()), E_USER_ERROR);
		}

		call_user_func_array([$controller, $action], $matches);

		//Execute end method if controller supports it
		if(true === is_callable([$controller, '__end'])) {
			call_user_func_array([$controller, '__end'], []);
		}
	}


	/**
	 * Load the module boot file if exists and readable
	 * @param string $module
	 */
	private function loadBoot($module) {

		if(false === is_string($module)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($module)), E_USER_ERROR);
		}
		
		$boot = Path :: get('modules') . $module . DIRECTORY_SEPARATOR . Files :: get('boot');

		if(true === is_readable($boot)) {
			require_once($boot);
		}
	}


	/**
	 * Loads all the middleware classes and saves them for later use for a single route
	 * @param sFire\Routing\Extend\Route $method
	 * @return void
	 */
	private function preloadMiddleware($route) {

		$middleware = $route -> getMiddleware();

		if(null === $middleware) {
			return;
		}
		
		$before = [];
		$after = [];

		foreach($middleware as $class) {

			$namespace = $this -> loadMiddleware($route -> getModule(), $class);

			if(false === class_exists($namespace)) {
				return trigger_error(sprintf('Middleware "%s" with module "%s" does not exists for "%s" as identifier in routes.php', $class, $route -> getModule(), $route -> getIdentifier()), E_USER_ERROR);
			}

			MiddlewareContainer :: add($namespace, 'before');
			MiddlewareContainer :: add($namespace, 'after');
		}
	}
}