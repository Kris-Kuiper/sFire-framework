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

class Isint implements RuleInterface {

	use MatchRule;


	/**
	 * @var boolean $strict 
	 */
	private $strict = false;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Must be an integer number');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$data 	= $this -> getValue();

		if(true === isset($params[0]) && true === is_bool($params[0])) {
			$this -> strict = $params[0];
		}

		if(true === is_string($data) || is_numeric($data)) {

			if(true === $this -> strict) {
				return true === is_int($data);
			}

			return !in_array(preg_match('/^-?[0-9]+$/', $data), [false, 0]);
		}

		return false;
	}
}
?>