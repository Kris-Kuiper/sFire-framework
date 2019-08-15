<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Template;

class TemplateData {


	const VARIABLEREGEX = '\$[_a-z][_a-z0-9]*(\.[\$_a-z0-9]+)*([ ]*->[ ]*[_a-z][_a-z0-9]*(\((.*?)\))*(\.[\$_a-z0-9]+)*)*';


	/**
	 * @var array $for 
	 */
	public static $for = [];


	/**
	 * @var array $foreach 
	 */
	public static $foreach = [];


	/**
	 * @var array $localVariables 
	 */	
	public static $localVariables = [];
	

	/**
	 * @var sFire\MVC\Viewmodel $viewmodel 
	 */
	public static $viewmodel = null;
	

	/**
	 * @var array $templateFunctions 
	 */
	public static $templateFunctions = [];


	/**
	 * Register a new template function
	 * @param string $action
	 * @param object $closure
	 * @return sFire\MVC\Controller
	 */
	public static function register($action, $closure) {
		
		if(false === is_string($action)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($action)), E_USER_ERROR);
		}
		
		if(false === is_object($closure)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be callable object, "%s" given', __METHOD__, gettype($closure)), E_USER_ERROR);
		}

		static :: setTemplateFunctions($action, $closure);
	}


	/**
	 * Register a new template function
	 * @param string $action
	 * @param object $closure
	 */
	private static function setTemplateFunctions($action, $closure) {
		static :: $templateFunctions[$action] = $closure;
	}


	/**
	 * Returns all the user defined template functions
	 * @return array
	 */
	public static function getTemplateFunctions() {
		return static :: $templateFunctions;
	}
}
?>