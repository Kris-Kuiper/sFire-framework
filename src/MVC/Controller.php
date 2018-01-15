<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
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
?>