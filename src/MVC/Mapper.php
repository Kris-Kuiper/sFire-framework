<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\MVC;

use sFire\MVC\Main;
use sFire\MVC\NamespaceTrait;
use sFire\Routing\Router;
use sFire\Utils\NameConvert;
use sFire\Application\Application;

class Mapper extends Main {


	use NamespaceTrait;

	/**
	 * @var string $dbtable
	 */
	private $dbtable = null;


	/**
	 * @var array $dbtables
	 */
	private $dbtables = [];


	/**
	 * Loads and stores a new DBTable and returns it
	 * @param string $dbtable
	 * @return mixed
	 */
	public function dbtable($dbtable = null) {

		if(true === isset($this -> dbtable) && null === $dbtable) {
			$dbtable = $this -> dbtable;
		}
		
		if($dbtable !== null && false === is_string($dbtable)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($dbtable)), E_USER_ERROR);
		}

		if($dbtable === null) {
			return trigger_error('Table name can not be empty. Set the table name with the "setTable" method in the mapper', E_USER_ERROR);
		}

		if(false === isset($this -> dbtables[$dbtable])) {

			$folders 	= explode('.', $dbtable); //Convert dots to directory separators
			$amount 	= count($folders) - 1;
			$namespace	= '';

			foreach($folders as $index => $folder) {

				if($amount === $index) {
					
					$class = Application :: get(['prefix', 'dbtable']) . NameConvert :: toCamelCase($folder, true);
					$namespace .= $class;

					break;
				}

				$namespace 	.= $folder . DIRECTORY_SEPARATOR;
			}

			$path 		= $this -> getNamespace($this, ['directory', 'mapper'], ['directory', 'dbtable']);
			$namespace 	= str_replace(DIRECTORY_SEPARATOR, '\\', $path . $namespace);

			if(false === class_exists($namespace)) {
				return trigger_error(sprintf('"%s" class does not exists in "%s"', $class, $path), E_USER_ERROR);
			}
			
			$this -> dbtables[$dbtable] = new $namespace;

			//Set the tablename if class isn't already populated with the table property
			$cl = new \ReflectionClass($this -> dbtables[$dbtable]);
			
			foreach($cl -> getProperties() as $prop) {

				if('table' === $prop -> name) {
					goto output;
				}
			}

			$table = $this -> dbtables[$dbtable] -> getTable();

			if(null === $table) {
				$this -> dbtables[$dbtable] -> setTable($dbtable);
			}
		}

		output:
		return $this -> dbtables[$dbtable];
	}


	/**
	 * Set the dbtable
	 * @param string $dbtable
	 */
	protected function setDBTable($dbtable) {

		if(false === is_string($dbtable)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($dbtable)), E_USER_ERROR);
		}

		$this -> dbtable = $dbtable;
	}


	/**
	 * Returns the table
	 * @return null | string
	 */
	protected function getDBTable() {
		return $this -> dbtable;
	}
}
?>