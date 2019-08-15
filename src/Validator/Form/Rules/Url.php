<?php

/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\Form\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Url implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('No valid URL');
	}

	
	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {

		$value  = $this -> getValue();
		$params = $this -> getParameters();

		if(false === isset($params[0])) {
			$params = [false];
		}

		if(true === isset($params[0]) && false === is_bool($params[0])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($params[0])), E_USER_ERROR);
		}

		if(true === $this -> getValidateAsArray() && true === is_array($value)) {
			
			foreach($value as $val) {

				if(false === $this -> check($val, $params)) {
					return false;
				}
			}
		}
		else {
			return $this -> check($value, $params);
		}

		return true;
	}


	/**
	 * Check if rule passes
	 * @param mixed $value
	 * @param array $params
	 * @return boolean
	 */
	private function check($value, $params) {

		if(true === is_string($value)) {

			if(true === $params[0]) {
				return filter_var($value, \FILTER_VALIDATE_URL) !== false;
			}
 			
 			$value = preg_replace('/^http(s):\/\//i', '', $value);
 			$value = 'http://' . $value;

 			return filter_var($value, \FILTER_VALIDATE_URL) !== false;
 		}

 		return false;
	}
}
?>
