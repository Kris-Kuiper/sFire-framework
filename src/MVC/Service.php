<?php 
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\MVC;

use sFire\Routing\Router;
use sFire\Config\Config;
use sFire\Application\Application;

class Service {
	

	/**
	 * @var array $services
	 */
	private $services = [];


	/**
	 * Service locator
	 * @param string $name
	 * @return Object
	 */
	protected function service($name) {

		if(false === is_string($name)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(true === ServiceContainer :: has($name)) {
			return ServiceContainer :: get($name);
		}

		$function = Application :: get(['services', $name], false);

		if('object' === gettype($function)) {
			
			$func = $function();

			ServiceContainer :: add($name, $func);
			return $func;
		}

		return trigger_error(sprintf('Argument 1 "%s" passed to %s() is not a known service', $name, __METHOD__), E_USER_ERROR);
	}


	/**
	 * Returns the config for the current module
	 * @param array $data
	 * @return mixed
	 */
	protected function config($data = []) {

		if(null === $data) {
			$data = [];
		}

		if(false === is_array($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		$module = Router :: getRoute() -> getModule();
		
		return Config :: get(array_merge([$module], $data));
	}
}