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

class Email implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Should be a valid email address');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {

		$value = $this -> getValue();

		if(true === is_string($value)) {
			return false !== filter_var(trim($value), \FILTER_VALIDATE_EMAIL);
		}

		return false;
	}
}
?>