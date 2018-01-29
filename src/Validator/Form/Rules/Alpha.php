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

class Alpha implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Only letters allowed (a-z)');
	}
	

	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$data = $this -> getValue();

		if(true === is_string($data)) {
			return !in_array(preg_match('/^[a-z]+$/i', $data), [false, 0]);
		}

		return false;
	}
}
?>