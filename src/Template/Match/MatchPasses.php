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
use sFire\Template\Match\MatchTranslation;
use sFire\Template\Match\MatchHelper;
use sFire\Template\Match\MatchRouter;
use sFire\Template\TemplateData;
use sFire\Template\Template;

class MatchPasses {

	use MatchTrait;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line, $inline = false) {

		$this -> setLine($line);
		$this -> setInline($inline);

		if(preg_match_all('#@passes(\((((?>[^()]+)|(?1))*)\))#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchRoute
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				$match 	= new MatchTranslation($this -> match[2][$index], true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchRouter($output, true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchHelper($output, true);
				$output = $match -> replace() -> getLine();

				//User defined functions
				foreach(TemplateData :: getTemplateFunctions() as $action => $closure) {

					$class  = Template :: NAMESPACE_MATCH . 'MatchUserDefined';
					$match  = new $class($output, $action, true);
					$output = $match -> replace() -> getLine();
				}

				$match 	= new MatchVariable($output);
				$output = $match -> replace() -> getLine();

				$code 	= '$this -> passes('. $output .')';
				$code 	= false === $this -> getInline() ? '<?php echo ' . $code . '; ?>' : $code;

				$this -> line = preg_replace('/' . preg_quote($this -> match[0][$index], '/') . '/', $code, $this -> line , 1);
			}
		}

		return $this;
	}
}
?>