<?php

/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\Form\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Notequals implements RuleInterface {

	use MatchRule;

	
	/**
	 * @var boolean $strict 
	 */
	private $strict = false;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Must not be equal to "%s"');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$value 	= $this -> getValue();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(true === isset($params[1]) && true === is_bool($params[1])) {
			$this -> strict = $params[1];
		}

		if(true === $this -> getValidateAsArray() && true === is_array($value)) {
			
			foreach($value as $val) {

				if(false === $this -> check($val, $params)) {
					return false;
				}
			}
		}
		else {
			return $this -> check($value, $params);
		}

		return true;
	}


	/**
	 * Check if rule passes
	 * @param mixed $value
	 * @param array $params
	 * @return boolean
	 */
	private function check($value, $params) {
		return true === ($this -> strict === true ? ($value !== $params[0]) : ($value != $params[0]));
	}
}
?>