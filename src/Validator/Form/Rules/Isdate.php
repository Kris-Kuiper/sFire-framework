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

class Isdate implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Must be a valid date');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {

		$params = $this -> getParameters();
		$value 	= $this -> getValue();

		if(false === is_string($value) && false === is_numeric($value)) {
			return false;
		}

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
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
		
		$timestamp = strtotime($value);

		if(true === is_int($timestamp)) {
			return true === (date($params[0], $timestamp) == $value);
		}

		return false;
	}
}