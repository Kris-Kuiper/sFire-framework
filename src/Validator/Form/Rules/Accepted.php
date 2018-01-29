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

class Accepted implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Must be accepted');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
 		return true === in_array($this -> getValue(), ['yes', 'on', '1', 'true']);
	}
}
?>