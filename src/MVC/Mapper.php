<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\MVC;

use sFire\MVC\Main;
use sFire\Router\Router;
use sFire\Helpers\NameConvert;
use sFire\Application\Application;

class Mapper extends Main {


	/**
	 * @var string $table
	 */
	private $table = null;


	/**
	 * @var array $dbtables
	 */
	private $dbtables = [];


	/**
	 * Loads and stores a new DBTable and returns it
	 * @param string $table
	 * @return mixed
	 */
	public function dbtable($table = null) {

		if(true === isset($this -> table) && null === $table) {
			$table = $this -> table;
		}
		
		if($table !== null && false === is_string($table)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($table)), E_USER_ERROR);
		}

		if($table === null) {
			return trigger_error('Table name can not be empty. Set the table name with the "setTable" method in the mapper', E_USER_ERROR);
		}

		if(false === isset($this -> dbtables[$table])) {

			$class 		= Application :: get(['prefix', 'dbtable']) . NameConvert :: toCamelCase($table, true);
			$path 		= Router :: getCurrentRoute() -> getModule() . DIRECTORY_SEPARATOR . Application :: get(['directory', 'dbtable']);
			$namespace 	= str_replace(DIRECTORY_SEPARATOR, '\\', $path . $class);

			if(false === class_exists($namespace)) {
				return trigger_error(sprintf('"%s" class does not exists in "%s"', $class, $path), E_USER_ERROR);
			}
			
			$this -> dbtables[$table] = new $namespace;

			//Set the tablename if class isn't already populated with the table property
			$cl = new \ReflectionClass($this -> dbtables[$table]);
			
			foreach($cl -> getProperties() as $prop) {

				if('table' === $prop -> name) {
					goto output;
				}
			}

			call_user_func_array([$this -> dbtables[$table], 'setTable'], [$table]);
		}

		output:
		return $this -> dbtables[$table];
	}


	/**
	 * Set the table
	 * @param string $table
	 */
	protected function setTable($table) {

		if(false === is_string($table)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($table)), E_USER_ERROR);
		}

		$this -> table = $table;
	}


	/**
	 * Returns the table
	 * @return null | string
	 */
	protected function getTable() {
		return $this -> table;
	}
}
?>