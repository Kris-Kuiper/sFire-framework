<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Validator\File\Rules;

use sFire\Validator\RuleInterface;
use sFire\Validator\MatchRule;

class Notempty implements RuleInterface {


	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this -> setMessage('No file uploaded');
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$file = $this -> getFile();

		if(null !== $file && true === isset($file['tmp_name']) && trim($file['tmp_name']) !== '') {
			return true;
		}

		return false;
	}
}
?>