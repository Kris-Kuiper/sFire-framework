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

class Json implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Invalid JSON string');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$value = $this -> getValue();

		if(is_string($value)) {
			
			@json_decode($value);

	 		return true === (json_last_error() == JSON_ERROR_NONE);
	 	}

	 	return false;
	}
}
?>