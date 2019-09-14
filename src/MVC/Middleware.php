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

	public function next() {

		$route = Router :: getRoute();
		MiddlewareContainer :: next($route -> getMatch());
	}
}
?>