<?php

/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
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

		$value = $this -> getValue();

		if(is_string($value)) {
 			return filter_var($value, \FILTER_VALIDATE_URL) !== false;
 		}

 		return false;
	}
}
?>