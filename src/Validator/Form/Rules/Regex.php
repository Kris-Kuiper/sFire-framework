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

class Regex implements RuleInterface {

	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Invalid value');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$data = $this -> getValue();
		$params = $this -> getParameters();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(false === is_string($params[0])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($params[0])), E_USER_ERROR);
		}

		if(true === is_string($data)) {
			return false === in_array(preg_match($params[0], $data), [false, 0]);
		}

		return false;
	}
}
?>