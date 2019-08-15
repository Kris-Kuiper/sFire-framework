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
use sFire\MVC\ViewModel;
use sFire\Config\Path;
use sFire\Translation\Translation;
use sFire\Form\Form;
use sFire\Template\TemplateData;
use sFire\Application\Application;
use sFire\Escaper\Escape;

class View {
	
	use MVCTrait;

	
	/**
	 * @var string $output
	 */
	private $output;

	
	/**
	 * @var sFire\MVC\ViewModel $viewmodel
	 */
	private $viewmodel;


	/**
	 * Getter for returning variables
	 * @param mixed $variable
	 * @return mixed
	 */
	public function __get($variable) {

		if(null !== $this -> viewmodel) {
			return $this -> viewmodel -> getVariable($variable);
		}
	}


	/**
	 * Set Viewmodel
	 * @param sFire\MVC\ViewMode $viewmodel
	 */
	public function setViewmodel(ViewModel $viewmodel) {
		$this -> viewmodel = $viewmodel;
	}


	/**
	 * Returns current Viewmodel
	 * @return sFire\MVC\ViewMode
	 */
	public function getViewmodel() {
		return $this -> viewmodel;
	}


	/**
	 * Converts template code to output string
	 * @param string $file
	 * @return sFire\MVC\View
	 */
	public function process($file) {

		ob_start();
		extract($this -> viewmodel -> getVariables(), EXTR_REFS);
		include($file);
		$this -> output = ob_get_clean();

		return $this;
	}


	/**
	 * Returns the parsed template code as string
	 * @return string
	 */
	public function getOutput() {
		return $this -> output;
	}


	/**
	 * Escapes HTML
	 * @param string $string
	 * @return string
	 */
	public function escapeHtml($string, $encoding = 'utf-8') {
		
		$current = Escape :: getEncoding();
		Escape :: setEncoding($encoding);
		$result =  Escape :: html($string);
		Escape :: setEncoding($current);

		return $result;
	}


	/**
	 * Escapes HTML attribute
	 * @param string $string
	 * @return string
	 */
	public function escapeAttr($string, $encoding = 'utf-8') {
		
		$current = Escape :: getEncoding();
		Escape :: setEncoding($encoding);
		$result =  Escape :: attr($string);
		Escape :: setEncoding($current);

		return $result;
	}


	/**
     * Escape a string for the Javascript context.
     * @param string $string
     * @return string
     */
	public function escapeJs($string, $encoding = 'utf-8') {
		
		$current = Escape :: getEncoding();
		Escape :: setEncoding($encoding);
		$result =  Escape :: js($string);
		Escape :: setEncoding($current);

		return $result;
	}

	
	/**
     * Escape a string for the CSS context.
     * @param string $string
     * @return string
     */	
	public function escapeCss($string, $encoding = 'utf-8') {
		
		$current = Escape :: getEncoding();
		Escape :: setEncoding($encoding);
		$result =  Escape :: css($string);
		Escape :: setEncoding($current);

		return $result;
	}


	/**
     * Escape a string for the URI or Parameter contexts. This should not be used to escape an entire URI - only a subcomponent being inserted.
     * @param string $string
     * @return string
     */
	public function escapeUrl($string, $encoding = 'utf-8') {
		
		$current = Escape :: getEncoding();
		Escape :: setEncoding($encoding);
		$result =  Escape :: url($string);
		Escape :: setEncoding($current);

		return $result;
	}


	/**
     * Escape a string with a given type (url, css, js, attr or html)
     * @param string $string
     * @return string
     */
	public function escape($string, $type = 'html', $encoding = 'utf-8') {

		switch($type) {

			case 'url'  : return $this -> escapeUrl($string, $encoding); break;
			case 'css'  : return $this -> escapeCss($string, $encoding); break;
			case 'js'   : return $this -> escapeJs($string, $encoding); break;
			case 'attr' : return $this -> escapeAttr($string, $encoding); break;
			
			default : return $this -> escapeHtml($string, $encoding); break;
		}
	}


	/**
	 * Calls the parse function
	 * @param string $template
	 * @return
	 */
	protected function partial($file) {

		$viewmodel = new ViewModel($file);
		$viewmodel -> assign($this -> viewmodel -> getVariables());

		ViewContainer :: output($viewmodel);
	}


	/**
	 * Calls the url function of the sFire\Routing\Router
	 * @param string $identifier
	 * @return string
	 */
	protected function router($identifier) {
		return call_user_func_array([Router :: Instance(), 'url'], func_get_args());
	}


	/**
	 * Calls the url function of the sFire\Routing\Router
	 * @param string $identifier
	 * @return string
	 */
	protected function form($type, $name = null, $value = null) {
		
		$form = new Form();
		return $form -> {$type}($name, $value);
	}


	/**
	 * Calls the translate function of the sFire\Translation\Translate
	 * @param string $key
	 * @return string
	 */
	protected function translate($key, $params = null, $language = null) {
		return Translation :: translate($key, $params, $language, $this -> getViewmodel());
	}


	/**
	 * Execute user defined template function
	 * @param string $action
	 * @param array $params
	 * @return mixed
	 */
	protected function template($action, $params = null) {
		
		if(false === is_string($action)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($action)), E_USER_ERROR);
		}
		
		if(null !== $params && false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		$functions = TemplateData :: getTemplateFunctions();

		if(false === isset($functions[$action])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid callable template function, "%s" given', __METHOD__, $action), E_USER_ERROR);
		}

		return call_user_func_array($functions[$action], $params);
	}
}
?>