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

class Isboolean implements RuleInterface {

	use MatchRule;


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
		return true === is_bool($value);
	}
}
?>