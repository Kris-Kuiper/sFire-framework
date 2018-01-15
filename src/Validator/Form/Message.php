<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Validator\Form;

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
?>