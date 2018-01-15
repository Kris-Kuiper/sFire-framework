<?php 
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Helpers;

class ArrayToEntity {
	
	/**
	 * Constructor
	 * @param object $object
	 * @param array $array
	 */	
	public function __construct($object, $array) {

		foreach($array as $key => $value) {

			$method = 'set' . ucfirst($key);

			if(true === is_callable([$object, $method])) {
				call_user_func_array([$object, $method], [$value]);
			}
		}
	}
}
?>