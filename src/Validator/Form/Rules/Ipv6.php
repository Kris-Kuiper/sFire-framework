<?php

/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\Form\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Ipv6 implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Invalid IPv6 address');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$value = $this -> getValue();

		if(true === $this -> getValidateAsArray() && true === is_array($value)) {
			
			foreach($value as $val) {

				if(false === $this -> check($val)) {
					return false;
				}
			}
		}
		else {
			return $this -> check($value);
		}

		return true;
	}


	/**
	 * Check if rule passes
	 * @param mixed $value
	 * @return boolean
	 */
	private function check($value) {

		if(true === is_string($value)) {
			return false === in_array(preg_match('/^(((?=.*(::))(?!.*\3.+\3))\3?|([\dA-F]{1,4}(\3|:\b|$)|\2))(?4){5}((?4){2}|(((2[0-4]|1\d|[1-9])?\d|25[0-5])\.?\b){4})\z/i', $value), [false, 0]);
		}

		return false;
	}
}
?>