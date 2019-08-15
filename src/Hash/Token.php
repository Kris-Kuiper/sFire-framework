<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Hash;

final class Token {

	/**
	 * Generate an unique id 
	 * @param int $length
	 * @return string
	 */
	public static function uniqueId($length = 10) {

		if(false === ('-' . intval($length) == '-' . $length)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($length)), E_USER_ERROR);
		}

		if($length > 30 || $length < 1) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be an Integer between 1 and 30', __METHOD__), E_USER_ERROR);
		}

		return substr(strrev(str_replace('/', '', str_replace('.', '', strip_tags(stripslashes(crypt(uniqid(rand(), 1))))))), 0, intval($length));
	}


	/**
	 * Create random token
	 * @param int $length
	 * @param boolean $numbers
	 * @param boolean $letters
	 * @param boolean $capitals
	 * @param boolean $symbols
	 * @return string
	 */
	public static function create($length = 6, $numbers = true, $letters = false, $capitals = false, $symbols = false) {

		if(false === ('-' . intval($length) == '-' . $length)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($length)), E_USER_ERROR);
		}

		$types = ['numbers', 'letters', 'capitals', 'symbols'];

		for($i = 0; $i < count($types); $i++) {

			if(null !== ${$types[$i]} && false === is_bool(${$types[$i]})) {
				return trigger_error(sprintf('Argument %s passed to %s() must be of the type boolean, "%s" given', ($i + 2), __METHOD__, gettype(${$types[$i]})), E_USER_ERROR);
			}
		}

		$array 				= [];
		$caseinsensitive	= ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
		$casesensitive 		= ['A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R', 'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z'];
		$numbers_arr 		= [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
		$symbols_arr 		= ['!', '@', '%', '$', '.', '&', '*', '-', '+', '#'];

		$numbers 	&& ($array = array_merge($array, $numbers_arr));
		$letters 	&& ($array = array_merge($array, $caseinsensitive));
		$capitals 	&& ($array = array_merge($array, $casesensitive));
		$symbols 	&& ($array = array_merge($array, $symbols_arr));

		$str = '';

		if(count($array) > 0) {

			for($i = 0; $i < $length; $i++) {
				$str .= $array[array_rand($array, 1)];
			}
		}

		return $str;
	}
}
?>