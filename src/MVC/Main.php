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
use sFire\HTTP\Output;

Class Main extends Service {


	use MVCTrait;


	/**
	 * @var $output sFire\HTTP\Output
	 */
	private $output;


	/**
	 * Returns the current route
	 * @return 
	 */
	public function route($identifier = null) {

		if(null !== $identifier && false === is_string($identifier)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($identifier)), E_USER_ERROR);
		}

		if(null !== $identifier) {

			if(false === Router :: routeExists($identifier)) {
				return trigger_error(sprintf('Route identifier "%s" not found in routes.php', $identifier), E_USER_ERROR);
			}

			return Router :: getRoute($identifier);
		}

		return Router :: getRoute();
	}


	/**
	 * Returns an Output object to send data in a fixed format to the browser 
	 * @return sFire\HTTP\Output
	 */
	public function output() {

		if(null === $this -> output) {
			$this -> output = new Output();
		}

		return $this -> output;
	}
}