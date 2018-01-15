<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Template\Match;

use sFire\Template\TemplateData;
use sFire\Template\ParseVariable;

class MatchVariable {

	use MatchTrait;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line) {

		$this -> setLine($line);

		if(preg_match_all('#(?<!["])(?!(\$this[ ]*-))\$([a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*)#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchVariable
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				if(false === in_array($match, TemplateData :: $localVariables)) {

					$code = ParseVariable :: parse($match);
					$this -> line = preg_replace('#' . preg_quote($this -> match[0][$index]) . '#', $code, $this -> line);
				}
			}
		}

		return $this;
	}
}
?>