<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Utils;

class NameConvert {

	/**
	 * Converts string underscores to camelCase
	 * @param string $string
	 * @param boolean $capitalizeFirstCharacter
	 * @return string
	 */
	public static function toCamelcase($string, $capitalizeFirstCharacter = false) {

		if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

		if(false === is_bool($capitalizeFirstCharacter)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($capitalizeFirstCharacter)), E_USER_ERROR);
		}

	    $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));

	    if(false === $capitalizeFirstCharacter) {
	        $str[0] = strtolower($str[0]);
	    }

	    return $str;
	}


	/**
	 * Converts string camelcase to snakecase
	 * @param string $string
	 * @return string
	 */
	public static function toSnakecase($string) {

		if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

		return ltrim(strtolower(preg_replace('/[A-Z]([A-Z](?![a-z]))*/', '_$0', $string)), '_');
	}
}
?>