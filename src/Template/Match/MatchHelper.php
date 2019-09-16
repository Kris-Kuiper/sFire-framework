<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Template\Match;

use sFire\Template\Match\MatchVariable;

class MatchHelper {

	use MatchTrait;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line, $inline = false) {

		$this -> setLine($line);
		$this -> setInline($inline);

		if(preg_match_all('#@helper(\((((?>[^()]+)|(?1))*)\))#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchForm
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				$match 		= new MatchVariable($this -> match[2][$index]);
				$variables  = $match -> replace() -> getLine();

				preg_match('#([^,]+)[ ]*,[ ]*([^,]+)([ ]*,[ ]*(.*))?#', $variables, $parameters);

				$helper = [];

				//Get the name of the helper
				if(true === isset($parameters[1])) {
					$helper['name'] = $parameters[1];
				}

				//Get the method of the helper
				if(true === isset($parameters[2])) {
					$helper['method'] = trim(trim($parameters[2], "'"), '"') . '(';
				}

				//Get the method of the helper
				if(true === isset($parameters[4])) {
					$helper['method'] .= $parameters[4];
				}

				$helper['method'] .= ')';

				$code = '$this -> helper('. $helper['name'] .') -> '. $helper['method'];
				$code = false === $this -> getInline() ? '<?php echo ' . $code . '; ?>' : $code;

				$this -> line = preg_replace('/' . preg_quote($this -> match[0][$index], '/') . '/', $code, $this -> line , 1);
			}
		}

		return $this;
	}
}