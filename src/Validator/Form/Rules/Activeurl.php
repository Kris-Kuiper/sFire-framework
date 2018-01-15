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

class Activeurl implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Should be an active domain');
	}

	
	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
 		
 		$value = $this -> getValue();
 		
 		if(is_string($value)) {

	 		$value = preg_replace('/^([a-z]+):\/\//i', '', $value);

			return true === (gethostbyname($value) && checkdnsrr($value, 'A'));
		}

		return false;
	}
}
?>