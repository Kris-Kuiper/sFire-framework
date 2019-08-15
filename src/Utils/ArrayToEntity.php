<?php 
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Utils;

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