<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
  
namespace sFire\Entity;


class Result extends Entity {
	

	/**
	 * @var int $id
	 */
	private $id;


	/**
	 * @var boolean $success
	 */
	private $success = false;


	/**
	 * @var array $messages
	 */
	private $messages = [];


	/**
	 * @var array $errors
	 */
	private $errors = [];


	/**
	 * @var array $data
	 */
	private $data = [];


	/**
	 * @var array $filter
	 */
	private $filter = [];


	/**
	 * Returns the id
	 * @return int
	 */
	public function getId() {
		return $this -> id;
	}
	
	
	/**
	 * Sets the id
	 * @param int $id
	 * @return $this
	 */
	public function setId($id) {
		
		$this -> id = $id;
		return $this;
	}


	/**
	 * Returns the success
	 * @return boolean
	 */
	public function getSuccess() {
		return $this -> success;
	}
	
	
	/**
	 * Sets the success
	 * @param boolean $success
	 * @return $this
	 */
	public function setSuccess($success) {
		
		$this -> success = $success;
		return $this;
	}

	
	/**
	 * Returns the messages
	 * @return array
	 */
	public function getMessages() {
		return $this -> messages;
	}


	/**
	 * Sets the messages
	 * @param array $messages
	 * @return $this
	 */
	public function setMessages($messages) {
		
		$this -> messages = $messages;
		return $this;
	}


	/**
	 * Returns object from data
	 * @param mixed $key
	 * @param mixed $default
	 * @return mixed
	 */
	public function get($key = null, $default = null) {
		
		if(false === isset($this -> data[$key])) {
			return $default;
		}

		$result = $this -> data[$key];

		//If there are no filters to apply
		if(0 === count($this -> filter)) {
			return $result;
		}

		//If item is an array
		if(true === is_array($result)) {

			$included = $this -> filter;
			
			return array_filter($result, function($key) use ($included) { return true === in_array($key, $included); }, ARRAY_FILTER_USE_KEY);
		}


		//If item is an object
		if(true === is_object($result)) {

			$output = [];

			foreach($result as $key => $value) {

				if(true === in_array($key, $this -> filter)) {
					$output[$key] = $value;
				}
			}

			return (object) $output;
		}

		return $result;
	}


	/**
	 * Sets object in data
	 * @param mixed $key
	 * @param mixed $default
	 * @return $this
	 */
	public function set($key, $value = null) {

		$this -> data[$key] = $value;
		return $this;
	}


	/**
	 * Returns all the data
	 * @return mixed
	 */
	public function getData() {
		return $this -> data;
	}

	/**
	 */
	public function setData($data) {
		return $this -> data = $data;
	}


	/**
	 * Returns the errors
	 * @return array
	 */
	public function getErrors() {
		return $this -> errors;
	}


	/**
	 * Sets the errors
	 * @param array $errors
	 * @return $this
	 */
	public function setErrors($errors) {
		
		$this -> errors = $errors;
		return $this;
	}


	/**
	 * 
	 * @param apply whitelist filter
	 * @return $this
	 */
	public function filter($filter, $key = null) {

		if(false === is_array($filter)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($filter)), E_USER_ERROR);
  		}

		$this -> filter = $filter;
		return $this;
	}
}
?>
