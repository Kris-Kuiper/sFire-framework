<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Utils;

class StringToArray {

	/**
	 * Get value from array string
	 * @param mixed $key
	 * @param mixed $default
	 * @param mixed $data
	 * @return mixed
	 */
	public function execute($key = null, $default = null, $data = null) {

		//Matching type array
		if(is_array($key)) {

			foreach($key as $index) {

				if(!isset($data[$index])) {
					return $default;
				}

				$data = $data[$index];
			}

			return $data;
		}

		//Matching type string
		if($key && isset($data[$key])) {
			return $data[$key];
		}

		//Matching type string array
		$names = explode('[', $key);

		foreach($names as $index => $name) {

			$name = rtrim($name, ']');

			if(!isset($data[$name])) {
				return null;
			}

			$data = $data[$name];

			if($index == count($names) - 1) {
				$default = $data;
			}
		}

		return $default;
	}
}
?>