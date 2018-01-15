<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
  
namespace sFire\Entity;

class Entity {
	

	/**
	 * Converts all getters to an Array
	 * @param array $set
	 * @param boolean $convert 
	 * @return array
	 */	
	public function toArray($set = [], $convert = true) {

		$methods = get_class_methods($this);
		$array 	 = [];

		if(true === is_string($set)) {
			$set = [$set];
		}

		$set 	= array_flip($set);
		$amount = count($set);

		foreach($methods as $method) {
			
			$chunks = explode('get', $method);

			if(count($chunks) == 2) {

				$key = (true === $convert) ? strtolower($chunks[1]) : $chunks[1];

				if($amount === 0 || ($amount > 0 && true === isset($set[$key]))) {

					$value		 = call_user_func_array([$this, $method], []);
					$array[$key] = $value;
				}
			}
		}
		
		return json_decode(json_encode($array), true);
	}


	/**
	 * Converts all getters to a JSON string
	 * @param array $set
	 * @param boolean $convert 
	 * @return string
	 */	
	public function toJson($set = [], $convert = true) {
		return json_encode($this -> toArray($set, $convert));
	}


	/**
	 * Converts all getters to a stdClass object
	 * @param array $set
	 * @param boolean $convert 
	 * @return stdClass
	 */	
	public function toObject($set = [], $convert = true) {
		return (object) $this -> toArray($set, $convert);
	}

}
?>