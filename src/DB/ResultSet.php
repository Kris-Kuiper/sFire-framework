<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\DB;

class ResultSet extends \ArrayIterator {


	const TYPE_OBJECT = 'object';
	const TYPE_JSON	  = 'json';
	const TYPE_ARRAY  = 'array';


	/**
	 * @var null|string $convert
	 */
	private $convert = null;


	/**
	 * @var null|string $entity
	 */
	private $entity = null;


	/**
	 * @var mixed $adapter
	 */
	private $adapter = null;


	/**
	 * Constructor
	 * @param array $dataset
	 * @param mixed $type
	 */
	public function __construct($dataset, $type = self :: TYPE_ARRAY, $adapter = null) {

		if(null !== $type && false === is_string($type) && 'object' !== gettype($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type String or Object, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		switch($type) {

			case self :: TYPE_ARRAY: 
			case self :: TYPE_OBJECT: 
			case self :: TYPE_JSON: $this -> convert = $type; break;

			case null : $this -> convert = self :: TYPE_ARRAY; break;
			
			default : 

				if('object' === gettype($type)) {
					
					$class = new \ReflectionClass($type);
					$type = $class->getName();
				}

				if(false === class_exists($type)) {
					return trigger_error(sprintf('"%s" is not a valid type or Entity class file in %s', $type, __METHOD__), E_USER_ERROR);	
				}

				$this -> convert = 'entity';
				$this -> entity  = $type;

			break;
		}

		$this -> adapter = $adapter;

		parent :: __construct($dataset);
	}


	/**
	 * Converts current array element to entity when requested
	 * @return mixed
	 */
	public function current() {

		$current = parent :: current();

		if(null !== $current) {

			if(true === $this -> is_multidimensional($current)) {

				if($this -> convert !== self :: TYPE_ARRAY) {
					return trigger_error(sprintf('Multidimensional Array can not be converted to "%s" type in "%s"', $this -> convert, __METHOD__), E_USER_ERROR);	
				}

				return $current;
			}

			switch($this -> convert) {

				case self :: TYPE_ARRAY: 
					return $current;

				case self :: TYPE_OBJECT: 
					return (object) $current;

				case self :: TYPE_JSON: 
					return json_encode($current);

				default : 

					if(false === class_exists($this -> entity)) {
						return trigger_error(sprintf('"%s" is not a valid type or Entity class file in %s', $type, __METHOD__), E_USER_ERROR);
					}

					$entity = new $this -> entity();

					$entity -> setAdapter($this -> adapter);
					$entity -> fromArray($current);

					return $entity;
			}
		}
	}


	/**
	 * Returns if an Array is multidimensional
	 * @param Array $array
	 * @return boolean
	 */
	private function is_multidimensional($array) {

		if(false === is_array($array)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type Array, "%s" given', __METHOD__, gettype($array)), E_USER_ERROR);
		}

	    return (count(array_filter($array, 'is_array')) > 0);
	}
}
?>