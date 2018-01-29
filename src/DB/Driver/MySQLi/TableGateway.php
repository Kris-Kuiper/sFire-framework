<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\DB\Driver\MySQLi;

use sFire\MVC\Main;
use sFire\Routing\Router;
use sFire\DB\ResultSet;
use sFire\Utils\NameConvert;
use sFire\Application\Application;

class TableGateway extends Main {

	private $data;


	/**
	 * @var mixed $adapter
	 */
	private $adapter;


	/**
	 * Returns the adapter
	 * @return mixed
	 */
	public function getAdapter() {
		return $this -> adapter;
	}
	
	
	/**
	 * Sets the adapter
	 * @param mixed $adapter
	 */
	public function setAdapter($adapter) {
		$this -> adapter = $adapter;
	}


	/**
	 * Sets the table
	 */
	public function setTable($table) {
		$this -> table = $table;
	}


	/**
	 * Returns the table
	 * @return string 
	 */
	public function getTable() {

		if(false === isset($this -> table)) {

			$cl = new \ReflectionClass($this);
			return trigger_error(sprintf('No table specified in "%s" class', $cl -> getFileName()), E_USER_ERROR);
		}

		return $this -> table;
	}


	/**
	 * Retrieve the number of rows
	 * @param string $column
	 * @param string $where
	 * @param array $params
	 * @return int|boolean
	 */
	public function rows($column = '*', $where = null, $params = []) {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		if(false === is_string($column)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($column)), E_USER_ERROR);
		}

		if(null !== $where && false === is_string($where)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($where)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		$query  = [];

		//Select
		$query[] = sprintf('SELECT COUNT(%s) AS amount FROM %s', $this -> getAdapter() -> escape($column), $this -> getAdapter() -> escape($this -> getTable()));

		//Where
		if(null !== $where) {
			$query[] = sprintf('WHERE %s', $this -> getAdapter() -> escape($where));
		}

		$statement = $this -> getAdapter() -> query(implode(' ', $query), $params) -> execute();

		if(true === $statement) {

			$amount = $this -> getAdapter() -> toArray();

			if(true === isset($amount[0]['amount'])) {
				return $amount[0]['amount'];
			}
		}

		return false;
	}


	/**
	 * Update rows
	 * @param string $where
	 * @param array $params
	 * @param string $limit
	 * @param string $order
	 * @return boolean
	 */
	public function update($columns, $where = null, $params = [], $limit = null, $order = null) {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		if(false === is_array($columns)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(null !== $where && false === is_string($where)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($where)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(null !== $limit && false === is_string($limit)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($limit)), E_USER_ERROR);
		}

		if(null !== $order && false === is_string($order)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($order)), E_USER_ERROR);
		}

		$query 	= [];
		$cols 	= [];
		$mode 	= $this -> mode($params);

		foreach($columns as $column => $value) {

			if('params' === $mode) {
				
				$cols[] = $this -> getAdapter() -> escape($column) . ' = :' . $column;
				$params[$column] = $value;
			}
			else {

				$cols[] = $this -> getAdapter() -> escape($column) . ' = ?';
				array_unshift($params, $value);
			}
		}
		
		$query[] = sprintf('UPDATE %s SET %s', $this -> getAdapter() -> escape($this -> getTable()), implode($cols, ','));

		//Where
		if(null !== $where) {
			$query[] = sprintf('WHERE %s', $this -> getAdapter() -> escape($where));			
		}

		//Order
		if(null !== $order) {
			$query[] = sprintf('ORDER BY %s', $this -> getAdapter() -> escape($order));			
		}

		//Limit
		if(null !== $limit) {
			$query[] = sprintf('LIMIT %s', $this -> getAdapter() -> escape($limit));			
		}

		if('bind' === $mode) {
			$params = array_values($params);
		}

		return $this -> getAdapter() -> query(implode(' ', $query), $params) -> execute();
	}


	/**
	 * Insert row
	 * @param array $params
	 * @return boolean
	 */
	public function insert($params, $ignore = false) {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(0 === count($params)) {
			return trigger_error(sprintf('Argument 1 passed to %s() does not contain any value', __METHOD__), E_USER_ERROR);
		}

		if(false === is_bool($ignore)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($ignore)), E_USER_ERROR);
		}

		$query = [];

		if(true === $this -> is_multidimensional($params)) {

			$fields = 0;
			$escape	= [];
			$data 	= [];

			foreach($params as $index => $insert) {

				if(0 === $index) {
					$fields = $insert;
				}

				if(count($fields) !== count($insert)) {
					return trigger_error(sprintf('Amount of fields in array with index %s does not match the amount of fields in the first index (%s) in "%s"', $index, count($fields), __METHOD__), E_USER_ERROR);
				}

				$escape[] = sprintf('(%s)', implode(array_fill(0, count($insert), '?'), ','));
				$data = array_merge($data, array_values($insert));
			}

			$query[] = sprintf('INSERT%s INTO %s (%s)', (true === $ignore ? ' IGNORE' : ''), $this -> getAdapter() -> escape($this -> getTable()), $this -> getAdapter() -> escape(implode(array_keys($fields), ', ')));
			$query[] = sprintf('VALUES%s', implode($escape, ', '));
			$params  = $data;
		}
		else {
			
			$query[] = sprintf('INSERT%s INTO %s (%s)', (true === $ignore ? ' IGNORE' : ''), $this -> getAdapter() -> escape($this -> getTable()), $this -> getAdapter() -> escape(implode(array_keys($params), ', ')));
			$query[] = sprintf('VALUES(%s)', implode(array_fill(0, count($params), '?'), ','));
		}

		return $this -> getAdapter() -> query(implode(' ', $query), array_values($params)) -> execute();
	}


	/**
	 * Delete rows
	 * @param string $where
	 * @param array $params
	 * @param string $limit
	 * @param string $order
	 * @return boolean
	 */
	public function delete($where = null, $params = [], $limit = null, $order = null) {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		if(null !== $where && false === is_string($where)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($where)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(null !== $limit && false === is_string($limit)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($limit)), E_USER_ERROR);
		}

		if(null !== $order && false === is_string($order)) {
			return trigger_error(sprintf('Argument 5 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($order)), E_USER_ERROR);
		}

		//Select
		$query[] = sprintf('DELETE FROM %s', $this -> getAdapter() -> escape($this -> getTable()));

		//Where
		if(null !== $where) {
			$query[] = sprintf('WHERE %s', $this -> getAdapter() -> escape($where));
		}

		//Order
		if(null !== $order) {
			$query[] = sprintf('ORDER BY %s', $this -> getAdapter() -> escape($order));			
		}

		//Limit
		if(null !== $limit) {
			$query[] = sprintf('LIMIT %s', $this -> getAdapter() -> escape($limit));
		}

		return $this -> getAdapter() -> query(implode(' ', $query), $params) -> execute();
	}


	/**
	 * Call stored procedure
	 * @param string $function
	 * @param array $params
	 * @return boolean
	 */
	public function call($function, $params) {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		if(null !== $function && false === is_string($function)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($function)), E_USER_ERROR);
		}

		if(false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		$query  = sprintf('CALL %s(%s)', $function, implode(array_fill(0, count($params), '?'), ','));
		$output = $this -> getAdapter() -> query($query, $params) -> execute();

		return $output;
	}


	/**
	 * Execute select statement and returns a ResultSet
	 * @param string $query
	 * @param array $params
	 * @param boolean $toEntity
	 * @return sFire\Adapter\MySQL\ResultSet | null
	 */
	public function select($query, $params = [], $type = null) {

		$type = $type === null ? $this -> getEntityNamespace($this -> getTable()) : $type;

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}
		
		return new ResultSet($this -> getAdapter() -> query($query, $params) -> toArray(), $type, $this -> getAdapter());
	}


	/**
	 * Returns the last inserted id
	 * @return int
	 */
	public function getLastId() {

		if(null === $this -> getAdapter()) {
			return trigger_error(sprintf('No database adapter was set in "%s"', __METHOD__), E_USER_ERROR);
		}

		$output = $this -> getAdapter() -> getLastId();

		$this -> getAdapter() -> reset();

		return $output;
	}


	/**
	 * Check the type of param binding ("?" = all keys are numeric or ":" = all keys are strings)
	 * @param array $params
	 * @return string
	 */
	private function mode($params) {

		$mode = 'params';

	 	if(count($params) > 0 && 0 === count(array_filter(array_keys($params), 'is_string'))) {
	 		$mode = 'bind';
	 	}

	 	return $mode;
	}


	/**
	 * Loads entity table name
	 * @param string $table
	 * @return string
	 */
	private function getEntityNamespace($table) {

		if(false === is_string($table)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($table)), E_USER_ERROR);
		}

		$path = Router :: getRoute() -> getModule() . DIRECTORY_SEPARATOR . Application :: get(['directory', 'entity']) . Application :: get(['prefix', 'entity']) . NameConvert :: toCamelCase($table, true);
		$path = str_replace(DIRECTORY_SEPARATOR, '\\', $path);

		return $path;
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