<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\File\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Size implements RuleInterface {


	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('File size must be between %s bytes and %s bytes');
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

		if(false === isset($params[1])) {
			return trigger_error(sprintf('Missing argument 2 for %s', __METHOD__), E_USER_ERROR);
		}

		if(false === ('-' . intval($params[1]) == '-' . $params[1])) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($params[1])), E_USER_ERROR);
		}

		$size = @filesize($file['tmp_name']);

		if(true === is_int($size)) {

			if($size >= $params[0] && $size <= $params[1]) {
				return true;
			}
		}

		return false;
	}
}
?>