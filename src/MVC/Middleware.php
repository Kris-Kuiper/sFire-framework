<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\MVC;

use sFire\MVC\Main;
use sFire\Routing\Router;
use sFire\MVC\MiddlewareContainer;

class Middleware extends Main {


	/**
	 * When called, the next middleware will be called. If there is none left, it will continue to load the controller for route
	 */
	public function next() {

		$route = Router :: getRoute();
		MiddlewareContainer :: next($route -> getMatch());
	}
}