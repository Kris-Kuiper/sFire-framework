<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
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

class MatchUserDefined {

	use MatchTrait;

	/**
	 * @var string $action
	 */
	private $action;


	/**
	 * Constructor
	 * @param string $line
	 */
	public function __construct($line, $action, $inline = false) {
		
		$this -> setAction($action);
		$this -> setLine($line);
		$this -> setInline($inline);

		if(preg_match_all('#@'. $action .'(\(([^)]*)\))#is', $this -> line, $matches)) {
			$this -> setMatch($matches);
		}
	}


	/**
	 * Set the action
	 * @param string $action
	 */
	public function setAction($action) {
		
		if(false === is_string($action)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($action)), E_USER_ERROR);
		}

		$this -> action = $action;
	}


	/**
	 * Replaces current line with PHP
	 * @return /sFire\Template\Match\MatchIf
	 */
	public function replace() {

		if(null !== $this -> match) {

			foreach($this -> match[0] as $index => $match) {

				$match 	= new MatchTranslation($this -> match[2][$index], true);
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

				$code = '$this -> template(\'' . $this -> action . '\', [' . $output . '])';
				$code = false === $this -> getInline() ? '<?php echo ' . $code . '; ?>' : $code;

				$this -> line = preg_replace('/' . preg_quote($this -> match[0][$index], '/') . '/', $code, $this -> line , 1);
			}
		}

		return $this;
	}
}
?>