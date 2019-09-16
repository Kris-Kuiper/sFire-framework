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

class Failed implements RuleInterface {


	use MatchRule;


	/**
	 * Constructor
	 */
	public function __construct() {
	}


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid() {
		
		$file = $this -> getFile();

		$messages = [

			UPLOAD_ERR_FORM_SIZE 	=> 'File exceeds the maximum file size that was specified in the HTML form',
			UPLOAD_ERR_PARTIAL 		=> 'File was only partially uploaded',
			UPLOAD_ERR_NO_FILE		=> 'No file was uploaded',
			UPLOAD_ERR_NO_TMP_DIR	=> 'Missing a temporary folder',
			UPLOAD_ERR_CANT_WRITE	=> 'Failed to write file to disk',
			UPLOAD_ERR_EXTENSION	=> 'An extension stopped the file upload'
		];

		if(false === isset($messages[$file['error']])) {
			return true;
		}

		$this -> setMessage($messages[$file['error']]);

		return false;
	}
}