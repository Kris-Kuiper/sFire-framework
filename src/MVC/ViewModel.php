<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\MVC;

use sFire\Config\Path;
use sFire\Routing\Router;
use sFire\MVC\ViewContainer;
use sFire\MVC\View;
use sFire\HTTP\Response;
use sFire\Application\Application;
use sFire\Template\Template;

class ViewModel {
	
	
	/**
	 * @var array $variables
	 */
	private $variables = [];


	/**
	 * @var string $identifier
	 */
	private $identifier;


	/**
	 * @var string $file
	 */
	private $file;


	/**
	 * @var sFire\MVC\View $view
	 */
	private $view;


	/**
	 * Contructor, generates unique identifier for this class
	 * @param string $file
	 * @param boolean $output
	 */
	public function __construct($file, $output = true, $contenttype = 'text/html', $charset = 'utf-8') {
		
		if(false === is_string($file)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === is_bool($output)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($output)), E_USER_ERROR);
		}

		if(false === is_string($contenttype)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($contenttype)), E_USER_ERROR);
		}

		if(false === is_string($charset)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($charset)), E_USER_ERROR);
		}
		
		$path 	   = Path :: get('modules') . Router :: getRoute() -> getModule() . DIRECTORY_SEPARATOR . Application :: get(['directory', 'view']);
		$viewfile  = str_replace('.', DIRECTORY_SEPARATOR, $file) . Application :: get(['extensions', 'view']);
		$file 	   = $path . $viewfile;
		
		//Check if viewfile exists
		if(false === file_exists($file)) {
			return trigger_error(sprintf('File "%s" passed to %s() does not exists', $file, __METHOD__), E_USER_ERROR);
		}

		$this -> setFile($file);
		$this -> setIdentifier(md5($file));
		$this -> createView();

		//Check if ViewContainer should output the viewfile automatically
		if(true === $output) {

			//Set the content type
			Response :: addHeader('Content-Type', sprintf('%s; charset=%s', $contenttype, $charset));

			//Add view to container
			ViewContainer :: load($this);
		}

		return $this;
	}


	/**
	 * Returns the unique identifier from this class
	 * @return string
	 */
	public function getIdentifier() {
		return $this -> identifier;
	}


	/**
	 * Getter for returning variables by key
	 * @param mixed $variable
	 * @return mixed
	 */
	public function getVariable($variable) {
		
		if(true === isset($this -> variables[$variable])) {
			return $this -> variables[$variable];
		}
	}


	/**
	 * Getter for returning all variables
	 * @return array
	 */
	public function getVariables() {
		return $this -> variables;
	}


	/**
	 * Assign variables to the current view
	 * @param string|array $key
	 * @param string $value
	 */
	public function assign($key, $value = null) {

		if(true === is_array($key)) {
			$this -> variables = array_merge($this -> variables, $key);
		}
		else {
			$this -> variables[$key] = $value;
		}

		return $this;
	}


	/**
	 * Returns the file
	 * @return string
	 */
	public function getFile() {
		return $this -> file;
	}


	/**
	 * Returns the View
	 * @param string $identifier
	 * @return sFire\MVC\View
	 */
	public function getView() {
		return $this -> view;
	}



	public function render() {

		$template = new Template($this);
		
		$template -> setDirectory(Path :: get('cache-template'));
		$template -> render();
		
		$file = $template -> getFile() -> entity() -> getBasepath();
		
		return $this -> getView() -> process($file) -> getOutput();
	}



	
	/**
	 * Creates new View
	 * @param string $identifier
	 */
	private function createView() {
		
		$this -> view = new View();
		$this -> view -> setViewmodel($this);
	}


	/**
	 * Sets the identifier
	 * @param string $identifier
	 */
	private function setIdentifier($identifier) {
		$this -> identifier = $identifier;
	}


	/**
	 * Sets the file
	 * @param string file
	 */
	private function setFile($file) {
		$this -> file = $file;
	}


	/**
	 * Sets the hash 
	 * @param string $hash
	 */
	private function setHash($hash) {
		$this -> hash = $hash;
	}


	/**
	 * Returns the hash
	 * @return string
	 */
	private function getHash() {
		return $this -> hash;
	}
}
?>