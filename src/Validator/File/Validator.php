<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\File;

use sFire\Utils\StringToArray;
use sFire\HTTP\Request;
use sFire\Validator\File\Message;
use sFire\Translation\Translation;
use sFire\Validator\Validator as MainValidator;
use sFire\Application\Application;

class Validator {


	use MainValidator;


	const NAMESPACE_RULES = '\\Rules\\';


	/**
	 * Constructor
	 * @param array $data
	 * @param string $prefix
	 */
	public function __construct($prefix = null) {

		$this -> setPrefix($prefix);
		$this -> setData($_FILES);
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
			}
		}	

		foreach($fieldnames as $fieldname) {

			if(false === is_string($fieldname)) {
				return trigger_error(sprintf('All fieldnames used in "%s"() must be of the type string, "%s" given', __METHOD__, gettype($fieldname)), E_USER_ERROR);
			}
		}

		if(count($instance :: getFieldnames()) === 0) {
			$instance :: setFieldnames($fieldnames);
		}

		return $instance;
	}


	/**
	 * Returns the instance for custom messages
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

				$file = $this -> getFile($rule -> field, $rule -> prefix);

				//Execute custom extended validator
				if($rule -> closure) {

					if(false === call_user_func($rule -> closure, $rule -> field, $file)) {

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
				$class -> setFile($file);
				$class -> setPrefix($rule -> prefix);
				$class -> setParameters($rule -> parameters);

				//Check if rule passes
				if(false === $this -> isRequired($rule -> field, $file) && false === $class -> isValid()) {

					$this -> setPasses(false);
					$this -> setErrorMessage($rule, $class);
				}
			}

			$this -> setExecuted(true);
		}
	}


	/**
     * Evaluates if file is required or not
     * @param $field string
     * @param $file null | string
     * @return boolean
     */
	private function isRequired($field, $file) {
		
		if(true === isset($this -> required[$field])) {
			
			if(false === $this -> required[$field] && (null === $file || trim($file['tmp_name']) === '')) {
			    return true;
			}
		}

		return false;
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

		return  __NAMESPACE__ . self :: NAMESPACE_RULES . ucfirst(strtolower($rule));
	}


	/**
	 * Sets data
	 * @param array $data
	 */
	private function setData($data) {

		if(false === is_array($data)) {
   			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
   		}
   		
		$this -> data = Request :: getUploadedFiles();
	}


	/**
	 * Returns the file array for a given field
	 * @param string $field
	 * @param string $prefix
	 * @return mixed
	 */
	private function getFile($field, $prefix) {

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