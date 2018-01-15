<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (http://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Entity;

use sFire\Entity\Entity;

final class Method extends Entity {


	/**
	 * @var string $url
	 */
	private $url;


	/**
	 * @var string $method
	 */
	private $method;


	/**
	 * @var string $type
	 */
	private $type;


	/**
	 * @var boolean $viewable
	 */
	private $viewable;


	/**
	 * @var string $identifier
	 */
	private $identifier;


	/**
	 * @var string $controller
	 */
	private $controller;


	/**
	 * @var string $action
	 */
	private $action;


	/**
	 * @var string $match
	 */
	private $match;


	/**
	 * @var string $module
	 */
	private $module;


	/**
	 * @var array $where
	 */
	private $where = [];


	/**
	 * @var array $variables
	 */
	private $variables = [];


	/**
	 * @var array $domain
	 */
	private $domain = [];


	/**
	 * @var boolean $strict
	 */
	private $strict = false;


	/**
     * Sets the url
     * @param string $url
     * @return sFire\Entity\Method
     */
	public function setUrl($url = null) {
		
		$this -> url = $url;
		return $this;
	}

	
	/**
     * Returns the url
     * @return string
     */
	public function getUrl() {
		return $this -> url;
	}


	/**
     * Sets the match
     * @param string $match
     * @return sFire\Entity\Method
     */
	public function setMatch($match = null) {
		
		$this -> match = $match;
		return $this;
	}

	
	/**
     * Returns the match
     * @return string
     */
	public function getMatch() {
		return $this -> match;
	}


	/**
     * Sets the module
     * @param string $module
     * @return sFire\Entity\Method
     */
	public function setModule($module = null) {
		
		$this -> module = $module;
		return $this;
	}

	
	/**
     * Returns the module
     * @return string
     */
	public function getModule() {
		return $this -> module;
	}


	/**
     * Sets the method
     * @param string $method
     * @return sFire\Entity\Method
     */
	public function setMethod($method) {
		
		$this -> method = $method;
		return $this;
	}

	
	/**
     * Returns the method
     * @return string
     */
	public function getMethod() {
		return $this -> method;
	}


	/**
     * Sets the type
     * @param string $type
     * @return sFire\Entity\Method
     */
	public function setType($type) {
		
		$this -> type = $type;
		return $this;
	}

	
	/**
     * Returns the type
     * @return string
     */
	public function getType() {
		return $this -> type;
	}
	

	/**
     * Sets the identifier
     * @param string $identifier
     * @return sFire\Entity\Method
     */
	public function setIdentifier($identifier) {
		
		$this -> identifier = $identifier;
		return $this;
	}
	
	
	/**
     * Returns the identifier
     * @return string
     */
	public function getIdentifier() {
		return $this -> identifier;
	}


	/**
     * Sets the where
     * @param string $match
     * @param string $regex
     * @return sFire\Entity\Method
     */
	public function setWhere($matches, $regex) {

		if(false === is_array($matches)) {
			$matches = [$matches => $regex];
		}

		foreach($matches as $match => $regex) {
			$this -> where[] = ['match' => $match, 'regex' => $regex];
		}
		
		return $this;
	}
	
	
	/**
     * Returns the where
     * @return string
     */
	public function getWhere() {
		return $this -> where;
	}


	/**
     * Sets strict
     * @param boolean $strict
     * @return sFire\Entity\Method
     */
	public function setStrict($strict) {

		$this -> strict = $strict;
		return $this;
	}
	
	
	/**
     * Returns strict
     * @return boolean
     */
	public function getStrict() {
		return $this -> strict;
	}


	/**
     * Sets the controller
     * @param string $controller
     * @return sFire\Entity\Method
     */
	public function setController($controller) {
		
		$this -> controller = $controller;
		return $this;
	}
	
	
	/**
     * Returns the controller
     * @return string
     */
	public function getController() {
		return $this -> controller;
	}

	/**
     * Sets the action
     * @param string $action
     * @return sFire\Entity\Method
     */
	public function setAction($action) {
		
		$this -> action = $action;
		return $this;
	}
	
	
	/**
     * Returns the action
     * @return string
     */
	public function getAction() {
		return $this -> action;
	}


	/**
     * Sets viewable
     * @param boolean $viewable
     * @return sFire\Entity\Method
     */
	public function setViewable($viewable) {
		
		$this -> viewable = $viewable;
		return $this;
	}

	
	/**
     * Returns viewable
     * @return boolean
     */
	public function getViewable() {
		return $this -> viewable;
	}


	/**
	 * Returns if router is viewable
	 * @return boolean
	 */
	public function isViewable() {
		return $this -> viewable;
	}


	/**
     * Sets the variables
     * @param string $variables
     * @return sFire\Entity\Method
     */
	public function setVariables($variables) {

		$this -> variables = $variables;
		return $this;
	}
	
	
	/**
     * Returns the variables
     * @return string
     */
	public function getVariables() {
		return $this -> variables;
	}


	/**
     * Sets the domain
     * @param string|array $domain
     * @return sFire\Entity\Method
     */
	public function setDomain($domain) {

		if(true === is_string($domain)) {
			$domain = [$domain];
		}

		$this -> domain = $domain;
		return $this;
	}
	
	
	/**
     * Returns the domain
     * @return array
     */
	public function getDomain() {
		return $this -> domain;
	}
}
?>