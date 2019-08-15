<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Template;

/*
$var
$var.bar
$var.$foo
$var.0
$var -> foo
$var -> foo.1
$var -> foo()
$var -> foo('bar')
$var -> foo($bar)
$var -> foo().1
*/

use sFire\Template\TemplateData;

class ParseVariable {

	public static function parse($variable) {

		$match 	= preg_split('#(\$|\.|[ ]*->[ ]*)#', $variable, -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
		$var 	= '';

		for($i = 0; $i < count($match); $i++) {

			switch(trim($match[$i])) {

				case '$' : 

					$i++;

					if(true === in_array('$' . $match[$i], TemplateData :: $localVariables)) {
						$var .= '@$' . $match[$i];
					}
					else {
						$var .= '@$this -> ' . $match[$i];
					}

				break;

				case '.' : 

					$i++;

					if($match[$i] === '$') {

						$i++;

						if(true === in_array('$' . $match[$i], TemplateData :: $localVariables)) {
							$var .= '[@$' . $match[$i] . ']';
						}
						else {
							$var .= '[@$this ->' . $match[$i] . ']';
						}
					}
					else {
						$var .= '["' . $match[$i] . '"]';
					}

				break;

				default :
					$var .= $match[$i];

				break;
			}
		}

		return $var;
	}
}
?>