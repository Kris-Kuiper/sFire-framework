<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\File;

use sFire\Validator\Message as MainMessage;

class Message {

	
	use MainMessage;

	
	const NAMESPACE_RULES = '\\Rules\\';


	/**
	 * Returns the class of the rule
	 * @param string $rule
	 * @return string
	 */
	private static function getRuleClass($rule) {

		if(false === is_string($rule)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($rule)), E_USER_ERROR);
		}

		if(true === isset(static :: $custom[$rule])) {
			return static :: $custom[$rule];
		}
		
		return __NAMESPACE__ . self :: NAMESPACE_RULES . ucfirst(strtolower($rule));
	}
}