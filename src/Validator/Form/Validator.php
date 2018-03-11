<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\Form;

use sFire\Utils\StringToArray;
use sFire\Validator\Form\Message;
use sFire\Validator\Form\Combine;
use sFire\Hash\Token;
use sFire\Translation\Translation;
use sFire\Session\Driver\Plain as Session;
use sFire\Validator\Validator as MainValidator;
use sFire\Application\Application;

class Validator {


	use MainValidator;


	const NAMESPACE_RULES = '\\Rules\\';


	/**
	 * @var array $combine
	 */
	private $combine = [];


	/**
	 * @var array $validateAsArray
	 */
	private $validateAsArray = [];


	/**
	 * Constructor
	 * @param array $data
	 * @param string $prefix
	 */
	public function __construct($prefix = null, $data = null) {

		$this -> setPrefix($prefix);
		$this -> setData((null !== $data ? $data : $_POST));
	}


	/**
	 * Add a custom message for field
	 * @param string $field
	 * @return \sFire\Validator\Message
	 */
	public function setMessage() {

		$fieldnames = func_get_args();
		$instance	= $this -> getMessageInstance();

		if(0 === count($fieldnames)) {

			foreach($this -> rules as $rule) {
				
				$fieldnames[$rule -> field] = $rule -> field;

				foreach($this -> combine as $combine) {
						
					if($combine -> getName() === $rule -> field) {
						$instance :: setFieldnames(array_merge($combine -> getFieldnames(), [$rule -> field]));
					}
				}
			}
		}	

		foreach($fieldnames as $fieldname) {

			if(false === is_string($fieldname)) {
				return trigger_error(sprintf('All fieldnames used in "%s"() must be of the type string, "%s" given', __METHOD__, gettype($fieldname)), E_USER_ERROR);
			}
		}

		$instance :: setFieldnames($fieldnames);

		foreach($this -> combine as $combine) {
				
			if($combine -> getName() === $fieldname) {
				$instance :: setFieldnames(array_merge($combine -> getFieldnames(), $fieldnames));
			}
		}

		return $instance;
	}


	/**
	 * Set a rule that a field value must be an array and all array items will be validated individual
	 * @param boolean|null $bool
	 * @return sFire\Validator
	 */
	public function validateAsArray($bool = null) {

		if(false === is_bool($bool) && null !== $bool) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($bool)), E_USER_ERROR);
		}

		if(null === $bool) {
			$bool = true;
		}

		foreach($this -> getFieldnames() as $field) {
			$this -> validateAsArray[$field] = $bool;
		}

		return $this;
	}


	/**
	 * Combines multiple fields to be as one for validation
	 * @return sFire\Validator\Combine
	 */
	public function combine() {

		$fieldnames = func_get_args();
		$values 	= [];

		foreach($fieldnames as $fieldname) {

			if(false === is_string($fieldname)) {
				return trigger_error(sprintf('All fieldnames used in "%s"() must be of the type string, "%s" given', __METHOD__, gettype($fieldname)), E_USER_ERROR);
			}

			$values[] = $this -> getValue($fieldname, $this -> getPrefix());
		}

		$combine = new Combine();
		$combine -> setValues($values);
		$combine -> setFieldnames($fieldnames);

		$this -> combine[] = $combine;

		return $combine;
	}


	/**
	 * Validates token from input
	 * @return array
	 */
	public function token() {

		$session = new Session();

		$name  = $this -> getValue('_token-name');
		$value = $this -> getValue('_token-value');

		if((null !== $name && null !== $value && $session -> pull(['_token', $name]) !== $value) || null === $name || null === $value) {
			
			$instance = $this -> getMessageInstance();
			$instance :: setFieldnames(['_token']);
			$instance :: setError(null, '_token', 'Token mismatch');
			$this -> setPasses(false);
		}
	}


	/**
	 * Returns the instance for custom messsages
	 * @return \sFire\Validator\Message
	 */
	private function getMessageInstance() {

		if(null === $this -> messageInstance) {
			$this -> messageInstance = Message :: Instance();
		}

		return $this -> messageInstance;
	}


	/**
	 * Executes the validation with the current rule set
	 */
	private function execute() {

		if(false === $this -> getExecuted()) {

			foreach($this -> rules as $rule) {

				$value = $this -> getValue($rule -> field, $rule -> prefix);

				//Execute custom extended validator
				if($rule -> closure) {

					if(false === call_user_func($rule -> closure, $rule -> field, $value)) {

						$this -> setPasses(false);

						$instance = $this -> getMessageInstance();
						$instance :: setFieldnames([$rule -> field]);
						$instance :: setError($rule -> prefix, $rule -> field, $rule -> message);
					}

					continue;
				}

				//Execute build in validator
				$class = $this -> getRuleClass($rule -> rule);
				$class = new $class();

				//Set rule data
				$class -> setField($rule -> field);
				$class -> setValue($value);
				$class -> setPrefix($rule -> prefix);
				$class -> setParameters($rule -> parameters);
				$class -> setValidateAsArray($this -> isValidateArray($rule -> field));

				if(true === is_callable([$class, 'requestParameters'])) {
					
					$params = call_user_func_array([$class, 'requestParameters'], []);

					foreach($params as $param) {
						
						$value = $this -> getValue($param, $rule -> prefix);
						$class -> setValues($param, $value);
					}
				}

				//Check if rule passes
				if(false === $this -> isRequired($rule -> field, $value) && false === $class -> isValid()) {

					$this -> setPasses(false);
					$this -> setErrorMessage($rule, $class);

					foreach($this -> combine as $combine) {
						
						if($combine -> getName() === $rule -> field) {

							foreach($combine -> getFieldnames() as $fieldname) {

								$rule -> field = $fieldname;
								$this -> setErrorMessage($rule, $class);
							}

							break;
						}
					}
				}
			}

			$this -> setExecuted(true);
		}
	}


	/**
     * Evaluates if field value is required or not
     * @param $field string
     * @param $value string
     * @return boolean
     */
	private function isRequired($field, $value) {
		
		if(true === isset($this -> required[$field])) {
			
			$empty = true;

			if(true === is_array($value)) {

				foreach($value as $val) {

					if(strlen(trim($val)) > 0) {
						$empty = false;
					}
				}
			}

			if(true === is_string($value) && strlen(trim($value)) > 0) {
				$empty = false;
			}

			if(false === $this -> required[$field] && $empty) {
			    return true;
			}
		}

		return false;
	}


	/**
     * Evaluates if field value should be validated as an array
     * @param $field string
     * @return boolean
     */
	private function isValidateArray($field) {
		
		if(true === isset($this -> validateAsArray[$field])) {
			return $this -> validateAsArray[$field];
		}

		return false;
	}


	/**
	 * Returns the class of the rule
	 * @param string $rule
	 * @return string
	 */
	private function getRuleClass($rule) {

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(true === isset($this -> custom[$rule])) {
			return $this -> custom[$rule];
		}

		return __NAMESPACE__ . self :: NAMESPACE_RULES . ucfirst(strtolower($rule));
	}


	/**
	 * Set the error message for particular rule
	 * @param string $rule
	 * @param object $class
	 */
	private function setErrorMessage($rule, $class) {

		$instance = $this -> getMessageInstance();

		$message  = $instance :: getMessage($rule -> field, $rule -> rule);
		$message  = null !== $message ? $message : $class -> getMessage();
		$message  = Translation :: translate($message, $rule -> parameters);
		$message  = vsprintf($message, $rule -> parameters);

		$instance :: setFieldnames([$rule -> field]);
		$instance :: setError($rule -> prefix, $rule -> field, $message);

		call_user_func_array([$instance, $rule -> rule], [$message]);
	}


	/**
	 * Sets data
	 * @param array $data
	 */
	private function setData($data) {

		if(false === is_array($data)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
   		}

		$this -> data = $data;
	}


	/**
	 * Returns the value for a given field
	 * @param string $field
	 * @param string $prefix
	 * @return mixed
	 */
	private function getValue($field, $prefix = null) {

		foreach($this -> combine as $combine) {
			
			if($combine -> getName() === $field) {
				return $combine -> combine();
			}
		}

		//Prefix
		if(null !== $prefix) {
			$field = $prefix . '[' . $field . ']';
		}

		//Get the value
		$helper = new StringToArray();
		$data 	= $helper -> execute($field, null, $this -> getData());

		return $data;
	}
}
?>