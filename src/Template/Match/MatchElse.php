<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Template\Match;

class MatchElse {

	use MatchTrait;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line) {

		$this -> setLine($line);

		if(preg_match_all('#@else#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchElse
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				$code = '<?php else: ?>';
				$this -> line = preg_replace('/' . preg_quote($this -> match[0][$index], '/') . '/', $code, $this -> line , 1);
			}
		}

		return $this;
	}
}
?>