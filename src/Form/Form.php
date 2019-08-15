<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Form;

class Form {

	const NAMESPACE_TYPES = '\\Types\\';

	public function __call($type, $params) {

		$params = [
						
			'name' 	=> isset($params[0]) ? $params[0] : null,
	    	'value' => isset($params[1]) ? $params[1] : null,
		];

		$class = $this -> getTypeClass($type);

		if(false === $this -> isType($type)) {
			$class = $this -> getTypeClass('Generic');
    	}

    	$type = new $class($type, $params['name'], $params['value']);

    	return $type;
	}


    /**
	 * Check if type exists / valid
	 * @param string $type
	 * @return boolean
	 */
    private function isType($type) {

    	if(false === is_string($type)) {
			return false;
		}

		$class = $this -> getTypeClass($type);

		return true === class_exists($class);
    }


    /**
	 * Returns the class of the type
	 * @param string $type
	 * @return string
	 */
	private function getTypeClass($type) {
		return __NAMESPACE__ . self :: NAMESPACE_TYPES . ucfirst(strtolower($type));
	}
}
?>