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

class Notextension implements RuleInterface {


	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('Invalid file extension');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$params = $this -> getParameters();
		$file 	= $this -> getFile();
		$info 	= (object) pathinfo($file['name']);
		$params = array_map(function($value) {

			if(false === is_string($value)) {
				return trigger_error(sprintf('Extension passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
			}

			return ltrim(strtolower($value), '.');
		}, $params);

		if(true === isset($info -> extension) && false === in_array($info -> extension, $params)) {
			return true;
		}

		return false;
	}
}
?>