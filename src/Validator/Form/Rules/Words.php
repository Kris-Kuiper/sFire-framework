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

class Words implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Amount of words is not equal to %s');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {

		$params = $this -> getParameters();
		$value  = $this -> getValue();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(false === ('-' . intval($params[0]) == '-' . $params[0])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($params[0])), E_USER_ERROR);
		}

		if(true === is_string($value)) {

			if('' === trim($value) && intval($params[0]) === 0) {
				return true;
			}

			if('' !== trim($value) && intval($params[0]) > 0) {
				return count(explode(' ', trim($value))) === intval($params[0]);
			}
		}

		return false;
	}
}
?>