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