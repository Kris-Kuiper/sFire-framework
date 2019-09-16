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
use sFire\Template\TemplateData;


class Controller extends Main {


	/**
	 * Register a new template function
	 * @param string $action
	 * @param object $closure
	 * @return sFire\MVC\Controller
	 */
	public function template($action, $closure) {

		TemplateData :: register($action, $closure);
		return $this;
	}
}