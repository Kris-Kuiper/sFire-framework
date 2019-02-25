<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\DB\Driver\MySQLi;

use sFire\Routing\Router;
use sFire\Utils\NameConvert;
use sFire\DateTime\DateTime;
use sFire\Application\Application;
use sFire\MVC\NamespaceTrait;
use sFire\MVC\Service;


class Entity extends Service {

	use NamespaceTrait;


	/**
	 * @var mixed $adapter
	 */
	private $adapter;


	/**
	 * @var mixed $identifier
	 */
	private $identifier;


	/**
	 * @var string $table
	 */
	private $table;


	/**
	 * @var array $columns
	 */
	private $columns = [];


	/**
	 * @var array $exclude
	 */
	private $exclude = [];


	/**
	 * @var array $date_formats
	 */
	private $date_formats = [];


	/**
	 * @var boolean $convert_json
	 */
	private $convert_json = false;


	/**
	 * Dynamic getter and setter
	 * @param string $method
	 * @param array $params
	 * @return mixed
	 */
	public function __call($method, $params) {

		//Setter
		if(substr($method, 0, 3) === 'set') {
			
			$column = substr($method, 3);

			if(true === isset($this -> columns[$column])) {

				if(count($params) > 0) {
					$this -> columns[$column][key($this -> columns[$column])] = $params[0];
				}
			}
			else {
				
				$cl = new \ReflectionClass($this);
				return trigger_error(sprintf('"%s" is not a valid method in "%s" class', $method, $cl -> getFileName()), E_USER_ERROR);
			}

			return $this;
		}

		//Getter
		if(substr($method, 0, 3) === 'get') {

			$column = substr($method, 3);

			if(true === isset($this -> columns[$column])) {
				return $this -> columns[$column][key($this -> columns[$column])];
			}
			else {
				
				$cl = new \ReflectionClass($this);
				return trigger_error(sprintf('"%s" is not a valid method in "%s" class', $method, $cl -> getFileName()), E_USER_ERROR);
			}
		}
	}


	/**
	 * Set the current table
	 * @param string $table
	 * @return $this
	 */
	public function setTable($table) {

		if(false === is_string($table)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($table)), E_USER_ERROR);
		}

		$this -> table = $table;

		return $this;
	}


	/**
	 * Returns the table from current namespace or from table variable if set
	 * @return string
	 */
	public function getTable() {

		if(null === $this -> table) {

			$path  = $this -> getNamespace($this, ['directory', 'entity'], ['directory', 'entity']) . Application :: get(['prefix', 'entity']);
			$path  = str_replace(DIRECTORY_SEPARATOR, '\\', $path);
			$class = new \ReflectionClass(get_class($this));
			
			$this -> table = NameConvert :: toSnakecase(str_replace($path, '', $class -> name));
		}

		return $this -> table;
	}


	/**
	 * Adds a string or array of date formats so selected dates which matches the formats will be converted automatically to Date Objects
	 * @param array|string $formats
	 * @return $this
	 */
	public function setDateFormat($formats) {

		if(false === is_array($formats)) {
			$formats = [$formats];
		}

		$this -> date_formats = $formats;

		return $this;
	}


	/**
	 * Columns containing valid JSON will be converted to Array's automatically
	 * @param boolean $convert
	 * @return $this
	 */
	public function convertToJson($convert = true) {

		if(false === is_bool($convert)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type Boolean, "%s" given', __METHOD__, gettype($convert)), E_USER_ERROR);
		}

		$this -> convert_json = $convert;

		return $this;
	}


	/**
	 * Converts given array to entity data
	 * @param array $data
	 * @return $this
	 */
	public function fromArray($data) {

		if(false === is_array($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type Array, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		$this -> beforeLoad();

		foreach($data as $column => $value) {
			
			$value 	= $this -> convertToDate($value);
			$value 	= $this -> convertJsonToArray($value);
			$col 	= NameConvert :: toCamelcase($column, true);
			$method = 'set' . $col;

			if(true === method_exists($this, $method)) {
				call_user_func_array([$this, $method], [$value]);
			}
			else {
				$this -> addValue($column, $value, $col);
			}
		}

		$this -> afterLoad();

		return $this;
	}


	/**
	 * Returns all variables to a single Array
	 * @return array
	 */
	public function toArray() {

		$class = new \ReflectionClass($this);
		$props = $class -> getProperties();
		$data  = [];

		foreach($props as $prop) {

			if($class -> name === $prop -> class) {

				if(true === isset($this -> {$prop -> name})) {
					$data[$prop -> name] = $this -> {$prop -> name};
				}
			}
		}

		foreach($this -> columns as $column) {
			$data[key($column)] = $column[key($column)];
		}

		return $data;
	}


	/**
	 * Return all variables to STD Class
	 * @return object
	 */
	public function toObject() {
		return (object) $this -> toArray();
	}


	/**
	 * Return all variables to JSON string
	 * @return string
	 */
	public function toJson() {
		return json_encode($this -> toArray());
	}


	/**
	 * Returns the adapter
	 * @return mixed
	 */
	public function getAdapter() {
		return $this -> adapter;
	}
	
	
	/**
	 * Sets the identifier
	 * @param string|array $identifier
	 */
	public function setIdentifier($identifier) {

		if(false === is_array($identifier)) {
			$identifier = [$identifier];
		}

		$this -> identifier = $identifier;
	}
	

	/**
	 * Sets exclude column names to be excluded when entity is saved. This will prevent data saved to MySQL generated columns which will trigger an error
	 * @param string|array $identifier
	 */
	public function setExclude($exclude) {

		if(false === is_array($exclude)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type Array, "%s" given', __METHOD__, gettype($exclude)), E_USER_ERROR);
		}

		$this -> exclude = $exclude;

		return $this;
	}


	/**
	 * Returns the identifier
	 * @return string 
	 */
	public function getIdentifier() {

		if(false === isset($this -> identifier)) {

			$cl = new \ReflectionClass($this);
			return trigger_error(sprintf('No table identifier specified in "%s" class', $cl -> getFileName()), E_USER_ERROR);
		}

		return $this -> identifier;
	}


	/**
	 * Sets the adapter
	 * @param mixed $adapter
	 */
	public function setAdapter($adapter) {
		$this -> adapter = $adapter;
	}


	/**
	 * Inserts or updates entity based on identifier
	 * @return boolean
	 */
	public function save() {

		if(null === $this -> getAdapter()) {
			return trigger_error('Adapter is not set. Set the adapter with the setAdapter() method', E_USER_ERROR);
		}

		//Execute user defined before save function
		$this -> beforeSave();

		$class   = new \ReflectionClass(get_class($this));
		$values  = $this -> toArray();
		$columns = [];

		array_walk($values, function($a, $b) use (&$columns, &$values) { 

			if(true === in_array($b, $this -> exclude)) {
				
				unset($values[$b]);
				return;
			}

			if($a instanceof DateTime) {
				$values[$b] = $a -> loadFormat();
			}

			if(true === is_array($a)) {
				$values[$b] = json_encode($a);
			}

			$columns[] = sprintf('`%s` = VALUES(`%s`)', $this -> getAdapter() -> escape($b), $this -> getAdapter() -> escape($b));
		});

		//Convert column names to column names with backtics
		foreach($values as $column => $value) {
			
			$values['`' . $column . '`'] = $value;
			unset($values[$column]);
		}

		$query = sprintf('INSERT INTO %s (%s) VALUES(%s) ON DUPLICATE KEY UPDATE %s', $this -> getAdapter() -> escape($this -> getTable()), implode(array_keys($values), ', '), implode(array_fill(0, count($values), '?'), ','), implode($columns, ', '));

		$this -> getAdapter() -> query($query, array_values($values));

		$success = $this -> getAdapter() -> execute();
		
		//Set new id if last id is not null
		$id  		= $this -> getAdapter() -> getLastId();
		$ids 		= $this -> getIdentifier();
		$affected 	= $this -> getAdapter() -> getAffected();

		if(count($ids) > 0 && $affected > 0) {

			$method = sprintf('set%s', NameConvert :: toCamelcase($ids[0], true));

			if(true === method_exists($this, $method)) {
				call_user_func_array([$this, $method], [$id]);
			}
		}

		//Execute user defined after save function
		$this -> afterSave();

		return $success;
	}


	/**
	 * Delete the current entitiy based on identifier
	 * @return boolean
	 */
	public function delete() {
		
		if(null === $this -> getAdapter()) {
			return trigger_error('Adapter is not set. Set the adapter with the setAdapter() method', E_USER_ERROR);
		}

		//Execute user defined before deleting function
		$this -> beforeDelete();

		$class  	 = new \ReflectionClass(get_class($this));
		$identifiers = $this -> getIdentifier();	
		$values  	 = $this -> toArray();
		$ids 		 = [];
		$escape 	 = [];
		
		if(0 === count($identifiers)) {
			return trigger_error(sprintf('Identifier is NULL. Can not delete entity without a valid value as identifier in %s', $class -> getFileName()), E_USER_ERROR);
		}

		foreach($identifiers as $identifier) {
			
			$ids[] = sprintf('%s = ?', $this -> getAdapter() -> escape($identifier));

			if(true === isset($values[$identifier])) {
				$escape[] = $values[$identifier];
			}
			else {
				$escape[] = null;
			}
		}

		$query = sprintf('DELETE FROM %s WHERE %s LIMIT 1', $this -> getAdapter() -> escape($this -> getTable()), implode($ids, ' AND '));

		$this -> getAdapter() -> query($query, $escape);

		$success = $this -> getAdapter() -> execute();

		//Execute user defined after deleting function
		$this -> afterDelete();

		return $success;
	}


	/**
	 * Refreshes the the current entitiy based on optional identifier(s) and value(s)
	 * @param array $identifiers
	 * @return $this
	 */
	public function refresh($identifiers = null) {
		
		if(true === is_string($identifiers)) {
			$identifiers = [$identifiers];
		}

		if(null !== $identifiers && false === is_array($identifiers)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($identifiers)), E_USER_ERROR);
		}

		if(null === $this -> getAdapter()) {
			return trigger_error('Adapter is not set. Set the adapter with the setAdapter() method', E_USER_ERROR);
		}

		$class  	 = new \ReflectionClass(get_class($this));
		$values  	 = $identifiers ? $identifiers : $this -> toArray();
		$identifiers = $identifiers ? array_keys($identifiers) : $this -> getIdentifier();	
		$where 		 = [];
		$escape 	 = [];

		if(0 === count($identifiers)) {
			return trigger_error(sprintf('Identifier is NULL. Can not refresh entity without a valid value as identifier in %s', $class -> getFileName()), E_USER_ERROR);
		}

		if(true === is_array($identifiers)) {

			foreach($identifiers as $identifier) {

				if(true === isset($values[$identifier])) {
					
					$where[]  = $this -> getAdapter() -> escape($identifier) . ' = ?';
					$escape[] = $values[$identifier];
				}
			}
		}

		if(0 === count($where)) {
			return trigger_error('No valid identifiers found. Either no identifier is set or one or more identifier values are empty.', E_USER_ERROR);
		}

		$query = sprintf('SELECT * FROM %s WHERE %s LIMIT 1', $this -> getAdapter() -> escape($this -> getTable()), implode($where, ' AND '));

		return $this -> fromArray($this -> getAdapter() -> query($query, $escape) -> toArray() -> current());
	}


	/**
	 * Clears all data stored in Entity except identifiers 
	 * @return $this
	 */
	public function clear() {

		$this -> columns = [];
		return $this;
	}


	/**
	 * Will be triggerd before saving entity to database
	 * @return $this
	 */
	protected function beforeSave() {
		return $this;
	}


	/**
	 * Will be triggerd after saving entity to database
	 * @return $this
	 */
	protected function afterSave() {
		return $this;
	}


	/**
	 * Will be triggerd before deleting entity from database
	 * @return $this
	 */
	protected function beforeDelete() {
		return $this;
	}


	/**
	 * Will be triggerd after deleting entity from database
	 * @return $this
	 */
	protected function afterDelete() {
		return $this;
	}


	/**
	 * Will be triggerd before the entity is loaded
	 * @return $this
	 */
	protected function beforeLoad() {
		return $this;
	}


	/**
	 * Will be triggerd after the entity is loaded
	 * @return $this
	 */
	protected function afterLoad() {
		return $this;
	}


	/**
	 * Tries to check if given value is a valid date. If so, return the used format and returns a sFire DateTime object. Otherwise, returns the given value
	 * @param string $value
	 * @return mixed
	 */
	public function convertToDate($value) {

		foreach($this -> date_formats as $format) {

			if(false !== \DateTime :: createFromFormat($format, $value)) {

				$d = new DateTime($value);
				$d -> createFromFormat($format, $value);

				if($d && $d -> format($format) == $value) {

					$d -> saveFormat($format);
					return $d;
				}
			}
		}

		return $value;
	}


	/**
	 * Tries covnert values to JSON strings. Otherwise, returns the given value
	 * @param string $value
	 * @return mixed
	 */
	public function convertJsonToArray($value) {

		if(true === $this -> convert_json) {

			if(true === is_string($value)) {

				$tmp = @json_decode($value, true);

				if(true === (json_last_error() == JSON_ERROR_NONE)) {
					$value = $tmp;
				}
			}
		}

		return $value;
	}


	/**
	 * Add new column with value and original method
	 * @param string $column
	 * @param string $value
	 * @param string $method
	 * @param mixed $value
	 */
	private function addValue($column, $value, $method) {
		$this -> columns[$method] = [$column => $value];
	}
}
?>