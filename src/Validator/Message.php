<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator;


trait Message {

	
	/**
	 * @var array $fieldnames
	 */
	private static $fieldnames = [];


	/**
	 * @var array $messages
	 */
	private static $messages = [];


	/**
	 * @var array $errors
	 */
	private static $errors = ['short' => [], 'full' => []];


	/**
	 * @var sFire\Validator\Message $instance
	 */
	private static $instance;


	/**
	 * @var array $custom
	 */
	private static $custom = [];

	
    /**
	 * Catch all for adding new custom messages. Adds a new message with optional params
	 * @param string $rule
	 * @param array $params
	 * @return sFire\Validator\Message
	 */
    public function __call($rule, $params) {

    	if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

    	if(false === static :: isRule($rule)) {
			return trigger_error(sprintf('Rule "%s" used in %s() is not a valid rule', $rule, __METHOD__), E_USER_ERROR);
    	}

    	static :: addMessage($rule, $params);

    	return static :: Instance();
    }


    /**
	 * Create and store new instance 
	 * @return mixed
	 */
	public static function Instance() {

        if(null === static :: $instance) {
			
			$class = __CLASS__;
			static :: $instance = new $class();
		}

		return static :: $instance;
    }


    /**
	 * Sets the current fieldname(s)
	 * @param array $fieldnames
	 * @return sFire\Validator\Message
	 */
	public static function setFieldnames($fieldnames) {
		
		if(false === is_array($fieldnames)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($fieldnames)), E_USER_ERROR);
		}

		static :: $fieldnames = $fieldnames;

		return static :: Instance();
	}


	/**
	 * Returns the current fieldnames
	 * @return array
	 */
	public static function getFieldnames() {
		return static :: $fieldnames;
	}


	/**
	 * Sets an error for the current field
	 * @param string $prefix
	 * @param string $field
	 * @param string $error
	 * @return sFire\Validator\Message
	 */
	public static function setError($prefix = null, $field, $error) {

		if(null !== $prefix && false === is_string($prefix)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($prefix)), E_USER_ERROR);
		}

		if(false === is_string($field)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($field)), E_USER_ERROR);
		}

		if(false === is_string($error)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($error)), E_USER_ERROR);
		}

		if(false === isset(static :: $errors['short'][$field])) {
			static :: $errors['short'][$field] = $error;
		}

		if(false === isset(static :: $errors['full'][$field])) {

			if(null !== $prefix) {
				static :: $errors['full'][$prefix . '[' . $field . ']'] = $error;
			}
			else {
				static :: $errors['full'][$field] = $error;
			}
		}

		return static :: Instance();
	}


	/**
	 * Returns message by field and rule
	 * @param string $field
	 * @param string $rule
	 * @return string
	 */
	public static function getMessage($field, $rule) {

		if(false === is_string($field)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($field)), E_USER_ERROR);
		}

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(true === isset(static :: $messages[$field][$rule])) {
			return static :: $messages[$field][$rule];
		}
	}


	/**
	 * Returns all message
	 * @return array
	 */
	public static function getMessages() {
		return static :: $messages;
	}


	/**
	 * Loads a custom validator class
	 * @param string $classname
	 * @param string $namespace
	 * @param string $name
	 * @return sFire\Validator\Message
	 */
	public static function loadCustom($classname, $namespace, $name) {

		if(false === is_string($classname)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($classname)), E_USER_ERROR);
		}

		if(false === is_string($namespace)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($namespace)), E_USER_ERROR);
		}

		if(false === is_string($name)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		static :: $custom[$name] = $namespace;

		return static :: Instance();
	}


	/**
	 * Returns all errors
	 * @param boolean $fullnames
	 * @return array
	 */
	public static function getErrors($fullnames = false) {

		if(false === is_bool($fullnames)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($fullnames)), E_USER_ERROR);
		}

		if(true === $fullnames) {
			return static :: $errors['full'];
		}

		return static :: $errors['short'];
	}


	/**
	 * Adds a new custom message to current fieldname
	 * @param string $rule
	 * @param array $params
	 */
	private static function addMessage($rule, $params) {

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Params passed to %s() are not well formed', __METHOD__), E_USER_ERROR);
		}

		foreach(static :: $fieldnames as $fieldname) {

			if(false === isset(static :: $messages[$fieldname][$rule])) {
				static :: $messages[$fieldname][$rule] = [];
			}

			static :: $messages[$fieldname][$rule] = $params[0];
		}
	}


    /**
	 * Check if rule exists / valid
	 * @param string $rule
	 * @return boolean
	 */
	private static function isRule($rule) {

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		$class = static :: getRuleClass($rule);

		return true === class_exists($class);
	}
}
?>
