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
use sFire\Template\Match\MatchFails;
use sFire\Template\Match\MatchPasses;
use sFire\Template\Match\MatchRouter;
use sFire\Template\Match\MatchEscape;
use sFire\Template\TemplateData;
use sFire\Template\Template;

class MatchFor {

	use MatchTrait;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line) {

		$this -> setLine($line);
		
		if(preg_match_all('#@for\((.*?[0-9-+])[ ]*\)#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchFor
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				//Define the local variable
				if(preg_match('/(\$[a-z0-9_]+)[ ]*=/is', $this -> match[1][$index], $variable)) {
					TemplateData :: $localVariables[] = $variable[1];
				}

				$match 	= new MatchTranslation($this -> match[1][$index], true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchEscape($output, true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchRouter($output, true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchHelper($output, true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchFails($output, true);
				$output = $match -> replace() -> getLine();

				$match 	= new MatchPasses($output, true);
				$output = $match -> replace() -> getLine();

				//User defined functions
				foreach(TemplateData :: getTemplateFunctions() as $action => $closure) {

					$class  = Template :: NAMESPACE_MATCH . 'MatchUserDefined';
					$match  = new $class($output, $action, true);
					$output = $match -> replace() -> getLine();
				}

				$match 	= new MatchVariable($output);
				$output = $match -> replace() -> getLine();

				$code = '<?php for('. $output .'): ?>';
				$this -> line = preg_replace('/' . preg_quote($this -> match[0][$index], '/') . '/', $code, $this -> line , 1);
			}
		}

		return $this;
	}
}