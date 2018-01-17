<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\MVC;

use sFire\Config\Config;
use sFire\Entity\Method;
use sFire\Template\Template;
use sFire\Config\Path;
use sFire\Config\Files;
use sFire\Application\Application;

#MVC
final class MVC {


	/**
	 * Constructor
	 * @param Method $method
	 * @param array $matches
	 */
	public function __construct(Method $method, $matches = []) {

		//Validate if the params is an array
		if(false === is_array($matches)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($matches)), E_USER_ERROR);
		}

		//Validates if the current method has a module set
		if(null === $method -> getModule()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid module name, set with setModule() method in routes.php', $method -> getIdentifier()), E_USER_ERROR);
		}

		//Validates if the current method has a controller set
		if(null === $method -> getController()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid controller, set with setController() method in routes.php', $method -> getIdentifier()), E_USER_ERROR);
		}

		//Validates if the current method has a action set
		if(null === $method -> getAction()) {
			return trigger_error(sprintf('Route with identifier "%s" must contain a valid action method, set with setAction() method in routes.php', $method -> getIdentifier()), E_USER_ERROR);
		}

		if('' === $method -> getModule()) {
			$method -> setModule(key(Config :: all()));
		}

		$namespace = $this -> loadController($method -> getModule(), $method -> getController());

		if(true === class_exists($namespace)) {
			
			$controller = new $namespace;
			return $this -> executeController($controller, $method, $matches);
		}

		trigger_error(sprintf('Controller "%s" with module "%s" does not exists for "%s" as identifier in routes.php', $method -> getController(), $method -> getModule(), $method -> getIdentifier()), E_USER_ERROR);
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
	 * Executes default controller functions
	 * @param object $controller
	 * @param sFire\Entity\Method $method
	 * @param array $matches
	 */
	private function executeController($controller, Method $method, $matches) {

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
}
?>