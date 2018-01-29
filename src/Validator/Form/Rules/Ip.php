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
use sFire\Validator\Rules\Ipv4;
use sFire\Validator\Rules\Ipv6;

class Ip {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Invalid IP address');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$ipv4 = new Ipv4();
		$ipv4 -> setData($this -> getValue());

		$ipv6 = new Ipv6();
		$ipv6 -> setData($this -> getValue());

		return true === $ipv4 -> isValid() || $ipv6 -> isValid();
	}
}
?>