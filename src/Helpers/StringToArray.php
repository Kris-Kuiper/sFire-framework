<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Helpers;

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