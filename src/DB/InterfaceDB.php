<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\DB;

interface InterfaceDB {


	/**
	 * Constructor
	 * @param string $host
	 * @param string $username
	 * @param string $password
	 * @param string $db
	 * @param int $port
	 * @param boolean $connect
	 * @param string $charset
	 * @return $this
	 */
	public function __construct($host, $username, $password, $db, $port, $connect, $charset);


	/**
	 * Connect to server
	 */
	public function connect();


	/**
	 * Returns results from statement as an array
	 * @return array
	 */
	public function toArray();


	/**
	 * Returns results from statement as a JSON string
	 * @return string
	 */
	public function toJson();


	/**
	 * Returns results from statement as a Array with Objects
	 * @return string
	 */
	public function toObject();


	/**
	 * Prepare a raw query
	 * @param string $query
	 * @param array $params
	 * @return $this
	 */
	public function query($query, $params = []);


	/**
	 * Escapes string for statement usage
	 * @param string $data
	 * @return string
	 */
	public function escape($data);


	/**
	 * Closes the connection
	 * @return $this
	 */
	public function close();
}
?>