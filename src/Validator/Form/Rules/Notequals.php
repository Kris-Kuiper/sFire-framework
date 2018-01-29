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
		$data 	= $this -> getValue();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(true === isset($params[1]) && true === is_bool($params[1])) {
			$this -> strict = $params[1];
		}

		return true === ($this -> strict === true ? ($data !== $params[0]) : ($data != $params[0]));
	}
}
?>