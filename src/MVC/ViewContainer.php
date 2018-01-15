<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (http://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\MVC;

use sFire\MVC\ViewModel;
use sFire\Template\Template;
use sFire\Config\Path;
use sFire\Application\Application;

class ViewContainer {
	

	/**
	 * @var array $views
	 */
	private static $views = [];


	/**
	 * @var array $variables
	 */
	private static $variables = [];
	

	/**
	 * Add or update existing viewmodel
	 * @param sFire\MVC\ViewModel $viewmodel
	 */
	public static function load(ViewModel $viewmodel) {
		static :: $views[$viewmodel -> getIdentifier()] = $viewmodel;
	}


	/**
	 * Unregister existing viewmodel
	 * @param sFire\MVC\ViewModel $viewmodel
	 */
	public static function unload(ViewModel $viewmodel) {

		if(true === isset(static :: $views[$viewmodel -> getIdentifier()])) {
			unset(static :: $views[$viewmodel -> getIdentifier()]);
		}
	}


	/**
	 * Returns all the viewmodels
	 * @return array
	 */
	public static function getViews() {
		return static :: $views;
	}


	/**
	 * Outputs parsed code
	 * @param sFire\MVC\ViewModel $viewmodel
	 */
	public static function output(ViewModel $viewmodel) {

		$viewmodel -> assign(static :: getVariables());

		$template = new Template($viewmodel);
		
		$template -> setDirectory(Path :: get('cache-template'));
		$template -> render();
		
		$file = $template -> getFile() -> entity() -> getBasepath();

		echo $viewmodel -> getView() -> process($file) -> getOutput();
	}


	/**
	 * Assign variables to the current view
	 * @param string|array $key
	 * @param string $value
	 */
	public static function assign($key, $value = null) {

		if(true === is_array($key)) {
			static :: $variables = array_merge(static :: $variables, $key);
		}
		else {
			static :: $variables[$key] = $value;
		}
	}


	/**
	 * Returns all the variables
	 * @return array
	 */
	private static function getVariables() {
		return static :: $variables;
	}
}
?>