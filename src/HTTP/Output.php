<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\HTTP;

use sFire\HTTP\Response;
use sFire\Entity\Entity;
use sFire\HTTP\Data;

class Output extends Entity {

	
	/**
	 * @var int $id
	 */
	private $id;


	/**
	 * @var int $httpcode
	 */
	private $httpcode = 200;
	
	
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
	 * @var sFire\Container\Container $data
	 */
	private $data;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> data = new Data();
	}


	/**
	 * Resets the output object
	 * @return $this
	 */
	public function reset() {

		$this -> id 		= null;
		$this -> httpcode 	= 200;
		$this -> success 	= false;
		$this -> messages 	= [];
		$this -> errors 	= [];

		//Delete all data from Data container
		$this -> data -> flush();

		return $this;
	}


	/**
	 * Magic method to convert to JSON
	 */
	public function __toString() {

		Response :: addHeader('Content-type', 'application/json');
		return $this -> json();
	}


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
		
		if(null !== $key) {
			return $this -> data -> get($key, $default);
		}

		return null;
	}


	/**
	 * Sets object in data
	 * @param mixed $key
	 * @param mixed $default
	 * @return $this
	 */
	public function set($key, $value = null) {

		$this -> data -> set($key, $value);
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
	 * Outputs to JSON
	 */
	public function json() {
		return $this -> toJson();
	}


	/**
	 * Outputs to Array
	 */
	public function array() {
		return $this -> toArray();
	}


	/**
	 * Outputs to Object
	 */
	public function object() {
		return $this -> toObject();
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
	 * Returns the httpcode
	 * @return array
	 */
	public function getHttpCode() {
		return $this -> httpcode;
	}


	/**
	 * Sets the httpcode
	 * @param integer $httpcode
	 * @return $this
	 */
	public function setHttpCode($httpcode) {
		
		Response :: setStatus($httpcode);

		$this -> httpcode = $httpcode;
		return $this;
	}
}
?>