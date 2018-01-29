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

class Contains implements RuleInterface {

	use MatchRule;

	
	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Must contain "%s"');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$data 	= $this -> getValue();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(false === is_string($params[0])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($params[0])), E_USER_ERROR);
		}

		if(true === is_string($data)) {
			return false === in_array(preg_match('/'. preg_quote($params[0]) .'/', $data), [false, 0]);
		}

		return false;
	}
}
?>