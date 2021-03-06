<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\File\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Minwidth implements RuleInterface {


	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Width must be equal or greater than %spx');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$file 	= $this -> getFile();

		if(false === isset($params[0])) {
			return trigger_error(sprintf('Missing argument 1 for %s', __METHOD__), E_USER_ERROR);
		}

		if(false === ('-' . intval($params[0]) == '-' . $params[0])) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($params[0])), E_USER_ERROR);
		}

		$dimensions = @getimagesize($file['tmp_name']);

		if(true === is_array($dimensions)) {

			if($dimensions[0] >= $params[0]) {
				return true;
			}
		}

		return false;
	}
}