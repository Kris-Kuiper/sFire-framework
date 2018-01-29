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

class After implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Date should be after %s');
	}

	
	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$value 	= $this -> getValue();

		if(false === is_string($value) && false === is_numeric($value)) {
			return false;
		}

		if(false === isset($params[0]) && false === is_string($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		$value  = strtotime($value);
		$param  = strtotime($params[0]);

		if(is_int($value) && is_int($param)) {
			return true === ($value >= $param);
		}

		return false;
	}
}
?>