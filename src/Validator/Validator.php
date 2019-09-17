<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator;

use sFire\Routing\Router;
use sFire\Validator\Message;
use sFire\Validator\Store;
use sFire\Application\Application;

trait Validator {


	/**
	 * @var array $data
	 */
	private $data = [];


	/**
	 * @var string $prefix
	 */
	private $prefix;


	/**
	 * @var array $rules
	 */
	private $rules = [];


	/**
	 * @var array $fieldnames
	 */
	private $fieldnames = [];


	/**
	 * @var array $custom
	 */
	private $custom = [];


	/**
	 * @var boolean $executed
	 */
	private $executed = false;


	/**
	 * @var boolean $passes
	 */
	private $passes = true;


	/**
	 * @var array $required
	 */
	private $required = [];


	/**
	 * @var \sFire\Validator\Message $messageInstance
	 */
	private $messageInstance;


	/**
	 * Catch all for adding new rules. Adds a new rule with optional params
	 * @param string $rule
	 * @param array $params
	 * @return sFire\Validator\Validator
	 */
	public function __call($rule, $params) {

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(false === $this -> isRule($rule)) {
    		return trigger_error(sprintf('Rule "%s" used in %s() is not a valid rule', $rule, __METHOD__), E_USER_ERROR);
    	}
		
		$fieldnames = $this -> getFieldnames();

		if(false === is_array($fieldnames)) {
			return trigger_error(sprintf('Fieldnames used in "%s"() must be of the type array, "%s" given', __METHOD__, gettype($fieldnames)), E_USER_ERROR);
		}

		foreach($fieldnames as $fieldname) {
			$this -> addRule($rule, $fieldname, $params);
		}

		return $this;
	}


	/**
	 * Add one or multiple validation rules to one or multiple fields
	 * @param string|array $fieldnames
	 * @return sFire\Validator\Validator
	 */
	public function field($fieldnames) {

		$fieldnames = func_get_args();

		foreach($fieldnames as $fieldname) {

			if(false === is_string($fieldname)) {
				return trigger_error(sprintf('All fieldnames used in "%s"() must be of the type string, "%s" given', __METHOD__, gettype($fieldname)), E_USER_ERROR);
			}
		}

		$this -> setFieldnames($fieldnames);

		return $this;
	}


	/**
	 * Executes the validation with the current ruleset and returns if the validation passes 
	 * @return boolean
	 */
	public function passes() {
		
		$this -> execute();

		return $this -> getPasses();
	}


	/**
	 * Executes the validation with the current ruleset and returns if the validation fails 
	 * @return boolean
	 */
	public function fails() {
		
		$this -> execute();

		return !$this -> getPasses();
	}


	/**
	 * Set a rule that a field value can be empty or not
	 * @param boolean|null $required
	 * @return sFire\Validator
	 */
	public function required($required = null) {

		if(false === is_bool($required) && null !== $required) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($required)), E_USER_ERROR);
		}

		if(null === $required) {
			$required = true;
		}

		foreach($this -> getFieldnames() as $field) {
			$this -> required[$field] = $required;
		}

		return $this;
	}


	/**
	 * Loads a custom validator rule with optional validator name
	 * @param string $classname
	 * @param string $name
	 * @return sFire\Validator
	 */
	public function load($classname, $name = null) {

		if(false === is_string($classname)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($classname)), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		$directories 	= explode('.', $classname); //Convert dots to directory separators
		$amount 		= count($directories) - 1;
		$namespace 		= Router :: getRoute() -> getModule() . '\\' . str_replace(DIRECTORY_SEPARATOR, '\\', Application :: get(['directory', 'validator']));

		foreach($directories as $index => $directory) {

			if($amount === $index) {
				
				$namespace .= Application :: get(['prefix', 'validator']) . ucfirst($directory);
				break;
			}

			$namespace 	.= $directory . '\\';
		}

		if(false === class_exists($namespace)) {
			return trigger_error(sprintf('Class "%s" does not exists or is not a valid validator rule', $namespace), E_USER_ERROR);
		}

		$name = (null !== $name ? $name : $directory);
		$this -> custom[$name] = $namespace;

		$instance = $this -> getMessageInstance();
		$instance :: loadCustom($directory, $namespace, $name);

		return $this;
	}


	/**
	 * Add custom validation rule to one or multiple fields
	 * @param string|array $field
	 * @param function $closure
	 * @param string $message
	 */
	public function extend($fields, $closure, $message) {

		if(false === is_array($fields)) {
			$fields = [(string) $fields];
		}

		if(false === (gettype($closure) === 'object')) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be a valid callback function, "%s" given', __METHOD__, gettype($closure)), E_USER_ERROR);
		}

		if(false === is_string($message)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($message)), E_USER_ERROR);
		}

		foreach($fields as $field) {
			$this -> addRule(null, $field, null, $closure, $message);
		}
	}


	/**
	 * Returns all the messages
	 * @param boolean $fullname
	 * @return array
	 */
	public function getMessages($fullnames = false) {

		if(false === is_bool($fullnames)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($fullnames)), E_USER_ERROR);
		}

		$instance = $this -> getMessageInstance();
		return $instance :: getErrors($fullnames);
	}


	/**
	 * Returns messages by fieldname
	 * @param string $fiedlname
	 * @return string
	 */
	public function getMessage($fieldname) {

		if(false === is_string($fieldname)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($fieldname)), E_USER_ERROR);
		}

		$instance 	= $this -> getMessageInstance();
		$short 		= $instance :: getErrors();
		$full 		= $instance :: getErrors(true);

		if(true === isset($short[$fieldname])) {
			return $short[$fieldname];
		}

		if(true === isset($full[$fieldname])) {
			return $full[$fieldname];
		}
	}

	
	/**
	 * Sets the prefix
	 * @param string $prefix
	 * @return sFire\Validator\Validator
	 */
	public function setPrefix($prefix = null) {

		if(null !== $prefix) {

			if(false === is_string($prefix)) {
				return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($prefix)), E_USER_ERROR);
			}

			$this -> prefix = $prefix;
		}

		return $this;
	}


	/**
	 * Returns the prefix
	 * @return string
	 */
	public function getPrefix() {
		return $this -> prefix;
	}


	/**
	 * Resets the validator so execution can be done again (keeps all rules intact)
	 * @return sFire\Validator\Validator
	 */
	public function reset() {

		$this -> setPasses(true);
		$this -> setExecuted(false);

		return $this;
	}


	/**
     * Stores data based on a string key for later use in other validations
     * @param string $key
     * @param mixed $value
     */
    public function store($key, $value) {
        Store :: set($key, $value);
    }


    /**
     * Retrieves data based on a string key previously stored via the store function
     * @param string $key
     * @return mixed
     */
    public function retrieve($key) {
        return Store :: get($key);
    }


    /**
     * Set an error message for specific field (used for own custom validation in combination with sFire validation rules)
     * @param string $field
     * @param string $message
     * @return $this
     */
    public function setError($field, $message) {

    	$instance = $this -> getMessageInstance();

    	$instance :: setFieldnames([$field]);
		$instance :: setError($this -> getPrefix(), $field, $message);

		$this -> setPasses(false);

    	return $this;
    }


	/**
	 * Check if rule exists / valid
	 * @param string $rule
	 * @return boolean
	 */
	private function isRule($rule) {

		if(false === is_string($rule)) {
			return false;
		}

		$class = $this -> getRuleClass($rule);

		return true === class_exists($class);
	}


	/**
	 * Add rule to validation
	 * @param string $rule
	 * @param string $field
	 * @param array $parameters
	 */
	private function addRule($rule, $field, $parameters, $closure = null, $message = null) {

		$this -> rules[] = (object) [

			'rule' 			=> $rule, 
			'field' 		=> $field, 
			'parameters'	=> $parameters,
			'closure'		=> $closure,
			'prefix' 		=> $this -> getPrefix(),
			'message'		=> $message,
			'required' 		=> true
		];
	}


	/**
	 * Sets passes
	 * @param boolean $passes
	 */
	private function setPasses($passes) {
		$this -> passes = $passes;
	}


	/**
	 * Returns passes
	 * @return boolean;
	 */
	private function getPasses() {
		return $this -> passes;
	}


	/**
	 * Returns data
	 * @return array;
	 */
	private function getData() {
		return $this -> data;
	}

	
	/**
	 * Sets fieldnames
	 * @param array $fieldnames
	 */
	private function setFieldnames($fieldnames) {
		$this -> fieldnames = $fieldnames;
	}


	/**
	 * Returns fieldnames
	 * @return array;
	 */
	private function getFieldnames() {
		return $this -> fieldnames;
	}


	/**
	 * Sets executed
	 * @param boolean $executed
	 */
	private function setExecuted($executed) {
		$this -> executed = $executed;
	}


	/**
	 * Returns executed
	 * @return boolean;
	 */
	private function getExecuted() {
		return $this -> executed;
	}
}