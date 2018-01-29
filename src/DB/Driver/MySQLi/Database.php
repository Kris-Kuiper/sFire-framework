<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\DB\Driver\MySQLi;

use sFire\DB\InterfaceDB;
use sFire\DB\ResultSet;

class Database implements InterfaceDB {


	/**
	 * @var array $data
	 */
	private $data = [

		'host' 		=> null, 
		'username' 	=> null, 
		'password' 	=> null, 
		'db' 		=> null, 
		'port' 		=> 3306,
		'charset'	=> 'utf8'
	];


	/**
	 * @var \mysqli_stmt $connection
	 */
	private $connection;


	/**
	 * @var array $stmt
	 */
	private $stmt = [];


	/**
	 * @var array $stmt
	 */
	private $shadow = [];


	/**
	 * @var float $trace
	 */
	private $trace;


	/**
	 * Constructor
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $db
	 * @param int $port
	 * @param boolean $connect
	 * @param string $charset
	 */
	public function __construct($host, $username = null, $password = null, $db = null, $port = 3306, $connect = true, $charset = null) {

		if(true === is_array($host)) {
			$this -> data = (object) array_merge($this -> data, $host);
		}
		elseif($host instanceof \mysqli) {
			$this -> connection = $host;
		}
		else {

			if(false === is_string($host)) {
				return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($host)), E_USER_ERROR);
			}

			if(false === is_string($username)) {
				return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($username)), E_USER_ERROR);
			}

			if(false === is_string($password)) {
				return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($password)), E_USER_ERROR);
			}

			if(false === is_string($db)) {
				return trigger_error(sprintf('Argument 4 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($db)), E_USER_ERROR);
			}

			if(false === ('-' . intval($port) == '-' . $port)) {
				return trigger_error(sprintf('Argument 5 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($port)), E_USER_ERROR);
			}

			if(false === is_bool($connect)) {
				return trigger_error(sprintf('Argument 6 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($connect)), E_USER_ERROR);
			}

			$this -> data = (object) $this -> data;

			$this -> data -> host 		= $host;
			$this -> data -> username 	= $username;
			$this -> data -> password 	= $password;
			$this -> data -> db 		= $db;
			$this -> data -> port 		= $port;
			$this -> data -> charset 	= $charset;
		}

		if(true === $this -> data -> connect) {
			$this -> connect();			
		}
	}


	/**
	 * Connect to Mysql server and set charset
	 */
	public function connect() {

		if(false === $this -> connection instanceof \mysqli) {
			
			$this -> connection = new \mysqli($this -> data -> host, $this -> data -> username, $this -> data -> password, $this -> data -> db, $this -> data -> port);
			
			if($this -> data -> charset) {
	            $this -> connection -> set_charset($this -> data -> charset);
	        }
		}
	}


	/**
	 * By default returns the number of rows of the last statement. If $one is false, it will returns the num rows of all statements
	 * @param boolean $one
	 * @return arrat|int
	 */
	public function getAmount($one = true) {

		$results = [];

		foreach($this -> shadow as $shadow) {
			$results[] = intval($shadow -> num_rows);
		}

		if(true === $one) {
			return end($results);
		}

		return $results;
	}


	/**
	 * By default returns the number of rows that are been affected of the last statement. If $one is false, it will returns the affected rows of all statements
	 * @param boolean $one
	 * @return arrat|int
	 */
	public function getAffected($one = true) {

		$results = [];

		foreach($this -> shadow as $shadow) {
			$results[] = intval($shadow -> affected_rows);
		}

		if(true === $one) {
			return end($results);
		}

		return $results;
	}


	/**
	 * By default returns the last inserted id of the last statement. If $one is false, it will returns the the last inserted id of all statements
	 * @param boolean $one
	 * @return array|int
	 */
	public function getLastid($one = true) {

		$results = [];

		foreach($this -> shadow as $shadow) {
			$results[] = intval($shadow -> insert_id);
		}

		if(true === $one) {
			return end($results);
		}

		return $results;
	}


	/**
	 * Returns results from statement as an array
	 * @return sFire\Adapter\ResultSet
	 */
	public function toArray() {
		return $this -> fetch('array');
	}


	/**
	 * Returns results from statement as a Array with JSON format
	 * @return sFire\Adapter\ResultSet
	 */
	public function toJson() {
		return $this -> fetch('json');
	}


	/**
	 * Returns results from statement as a Array with SDT Objects
	 * @return sFire\Adapter\ResultSet
	 */
	public function toObject() {
		return $this -> fetch('object');
	}


	/**
	 * Returns results from statement as an result set
	 * @param string $type
	 * @return sFire\Adapter\ResultSet
	 */
	public function fetch($type = null) {

		$results = new ResultSet([], $type);

		//Returns a copy of a value
		$copy 	= function($a) { return $a; };
		$amount = count($this -> stmt);

		foreach($this -> stmt as $stmt) {

			//Execute statement and store results
			$stmt 	= $this -> executeStatement($stmt);
			$result = [];

			if(false !== $stmt && 0 !== $stmt -> field_count && 0 === $stmt -> errno) {
				
				$vars = $this -> bind($stmt);

				while($stmt -> fetch()) {

					if($amount === 1) {
				    	$results -> append(array_map($copy, $vars));
				    }
				    else {
				    	$result[] = array_map($copy, $vars);
				    }
				}
			}
			
			if($amount > 1) {
				$results -> append($result);
			}
		}

		$this -> shadow = $this -> stmt;
		$this -> reset();

		return $results;
	}


	/**
	 * Prepare a raw query
	 * @param string $query
	 * @param array $params
	 * @return sFire\Adapter\MySQLI
	 */
	public function query($query, $params = []) {
		
		if(false === is_string($query)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($query)), E_USER_ERROR);
		}

		if(null !== $params && false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		if(null === $this -> connection) {
			$this -> connect();
		}

		$bind = $this -> parse($params, $query);

		//Write time for debugging
		if(null === $this -> trace) {
			$this -> trace = microtime(true);	
		}
		
		//Prepare statement
		$stmt = $this -> connection -> prepare($query);

		if(false === $stmt) {
			return trigger_error($this -> connection  -> error, E_USER_ERROR);
		}

		if(count($bind) > 0) {

			if(false === ($stmt -> param_count == count($bind) - 1)) {
				return trigger_error('Number of variable elements query string does not match number of bind variables');
			}

			call_user_func_array([$stmt, 'bind_param'], $bind);
		}

		$this -> stmt[] = $stmt;
		$this -> shadow = $this -> stmt;

		return $this;
	}


	/**
	 * Execute all statements and returns boolean on succes/failure
	 * @return boolean
	 */
	public function execute() {

		foreach($this -> stmt as $stmt) {
			$this -> executeStatement($stmt);
		}

		$this -> shadow = $this -> stmt;
		$this -> reset();

		//Write time for debugging
		$this -> trace = microtime(true) - $this -> trace;

		return true;
	}


	/**
	 * Escapes string for statement usage
	 * @param string $data
	 * @return string
	 */
	public function escape($data) {

		if(false === is_string($data) && false === is_numeric($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, float or int, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		if(null === $this -> connection) {
			$this -> connect();
		}

		return $this -> connection -> real_escape_string($data);
	}


	/**
	 * Begin a transaction
	 * @return sFire\Adapter\MySQLI
	 */
	public function transaction() {

		if(null === $this -> connection) {
			$this -> connect();
		}

		$this -> connection -> autocommit(false);
		$this -> connection -> begin_transaction();

		return $this;
	}


	/**
	 * Commit a transaction
	 * @return sFire\Adapter\MySQLI
	 */
	public function commit() {
		
		if(null === $this -> connection) {
			$this -> connect();
		}

		$this -> connection -> commit();
		$this -> connection -> autocommit(true);

		return $this;
	}


	/**
	 * Rollback a transaction
	 * @return sFire\Adapter\MySQLI
	 */
	public function rollback() {

		if(null === $this -> connection) {
			$this -> connect();
		}

		$this -> connection -> rollback();
		$this -> connection -> autocommit(true);

		return $this;
	}


	/**
	 * Closes the database connection
	 * @return sFire\Adapter\MySQLI
	 */
	public function close() {

		if(false === $this -> connection instanceof \mysqli) {
			return trigger_error('Connection already closed');
		}

		$this -> connection -> close();

		return $this;
	}


	/**
	 * Returns the last error number
	 * @return int
	 */
	public function getLastErrno() {

		if(true === $this -> connection instanceof \mysqli) {
			$this -> connect();
		}

		return $this -> connection -> errno;
	}


	/**
	 * Returns the last error message
	 * @return string
	 */
	public function getLastError() {

		if(true === $this -> connection instanceof \mysqli) {
			$this -> connect();
		}

		return $this -> connection -> error;
	}


	/**
	 * Returns the trace (time of execution in milliseconds)
	 * @return float
	 */
	public function getTrace() {
		return $this -> trace;
	}

	
	/**
	 * Reset all stmt
	 */
	public function reset() {
		$this -> stmt = [];
	}


	/**
	 * Executes one statement
	 * @param mysqli_stmt
	 * @return mysqli_stmt
	 */
	private function executeStatement($stmt) {

		if(true !== $stmt -> execute()) {

			if($this -> getLastErrno() > 0) {
				return trigger_error(sprintf('A database error occured with error number "%s" and message: "%s"', $this -> getLastErrno(), $this -> getLastError()), E_USER_ERROR);
			}

			return false;
		}

		$stmt -> store_result();

		return $stmt;
	}

	
	/**
	 * Parse parameters and return it with types
	 * @param array $params
	 * @param string $query
	 * @return array
	 */
	private function parse(&$params, &$query) {

		//Check the type of param binding ("?" = all keys are numeric or ":" = all keys are strings)
	 	if(0 !== count(array_filter(array_keys($params), 'is_string'))) {
			
			preg_match_all('#:((?:[a-zA-Z_])(?:[a-zA-Z0-9-_]+)?)#', $query, $variables);

			if(true === isset($variables[0])) {

				$parameters = [];

				foreach($variables[0] as $index => $variable) {
					
					if(false === isset($params[$variables[1][$index]])) {
						return trigger_error(sprintf('Parameter "%s" missing from bind parameters', $variables[1][$index]), E_USER_ERROR);
					}
					
					$parameters[] =& $params[$variables[1][$index]];
					$query = str_replace($variables[0][$index], '?', $query);
				}

				$params = $parameters;
			}
	 	}

		$bind = [];

		if(count($params) > 0) {

			$types = '';

			foreach($params as $param => $value) {

				switch(gettype($value)) {
		            
		            case 'NULL'		:
		            case 'string'	: $types .= 's'; break;

		            case 'boolean'	:
		            case 'integer'	: $types .= 'i'; break;
		            
		            case 'blob'		: $types .= 'b'; break;
		            
		            case 'double'	: $types .= 'd'; break;
		        }
			}

			$bind[] =& $types;

			foreach($params as $param => $value) {
				$bind[] =& $params[$param];
			}
		}

		return $bind;
	}


	/**
	 * Bind the result to the variables and returns it
	 * @return array
	 */
	private function bind($stmt) {

		$meta 	= $stmt -> result_metadata();
		$double = [];
		$vars 	= [];

		if($stmt -> field_count > 0) {

			while($field = $meta -> fetch_field()) {

				$columnname  = $field -> name;
				${$columnname} = null;
				
				if(true === array_key_exists($columnname, $vars)) {
					$double[] = $columnname;
				}
				else {
					$vars[$columnname] = &${$columnname};
				}
			}
		}

		if(count($double) > 0) {
			return trigger_error(sprintf('Column %s in field list is ambiguous', implode(', ', array_unique($double))));
		}

		call_user_func_array([$stmt, 'bind_result'], $vars);

		return $vars;
	}
}
?>