<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Entity;

use sFire\Entity\Entity;

use DateTime;

class Error extends Entity {
	

	/**
	 * @var string $message
	 */
	private $message;


	/**
	 * @var string $line
	 */
	private $line;


	/**
	 * @var string $ip
	 */
	private $ip;


	/**
	 * @var \Datetime $date
	 */
	private $date;


	/**
	 * @var array $context
	 */
	private $context;


	/**
	 * @var string $file
	 */
	private $file;


	/**
	 * @var string $type
	 */
	private $type;


	/**
	 * @var string $string
	 */
	private $number;


	/**
	 * @var array $backtrace
	 */
	private $backtrace;

	
	/**
     * Sets the message
     * @param string $message
     * @return sFire\Entity\Error
     */
	public function setMessage($message) {
		
		$this -> message = $message;
		return $this;
	}

	
	/**
     * Returns the message
     * @return string
     */
	public function getMessage() {
		return $this -> message;
	}

	
	/**
     * Sets the line
     * @param string $line
     * @return sFire\Entity\Error
     */
	public function setLine($line) {
		
		$this -> line = $line;
		return $this;
	}

	
	/**
     * Returns the line
     * @return string
     */
	public function getLine() {
		return $this -> line;
	}

	
	/**
     * Sets the ip
     * @param string $ip
     * @return sFire\Entity\Error
     */
	public function setIp($ip) {
		
		$this -> ip = $ip;
		return $this;
	}

	
	/**
     * Returns the ip
     * @return string
     */
	public function getIp() {
		return $this -> ip;
	}

	
	/**
     * Sets the date
     * @param \Datetime $date
     * @return sFire\Entity\Error
     */
	public function setDate(DateTime $date) {
		
		$this -> date = $date;
		return $this;
	}

	
	/**
     * Returns the date
     * @return \Datetime
     */
	public function getDate() {
		return $this -> date;
	}

	
	/**
     * Sets the file
     * @param string $file
     * @return sFire\Entity\Error
     */
	public function setFile($file) {
		
		$this -> file = $file;
		return $this;
	}

	
	/**
     * Returns the file
     * @return string
     */
	public function getFile() {
		return $this -> file;
	}

	
	/**
     * Sets the number
     * @param string $number
     * @return sFire\Entity\Error
     */
	public function setNumber($number) {
		
		$this -> number = $number;
		return $this;
	}

	
	/**
     * Returns the number
     * @return string
     */
	public function getNumber() {
		return $this -> number;
	}

	
	/**
     * Sets the context
     * @param array $context
     * @return sFire\Entity\Error
     */
	public function setContext($context = []) {
		
		$this -> context = $context;
		return $this;
	}

	
	/**
     * Returns the context
     * @return array
     */
	public function getContext() {
		return $this -> context;
	}

	
	/**
     * Sets the type
     * @param string $type
     * @return sFire\Entity\Error
     */
	public function setType($type) {
		
		$this -> type = $type;
		return $this;
	}

	
	/**
     * Returns the type
     * @return string
     */
	public function getType() {
		return $this -> type;
	}

	
	/**
     * Sets the backtrace
     * @param array $backtrace
     * @return sFire\Entity\Error
     */
	public function setBacktrace($backtrace) {
		
		$this -> backtrace = $backtrace;
		return $this;
	}

	
	/**
     * Returns the backtrace
     * @return array
     */
	public function getBacktrace() {
		return $this -> backtrace;
	}
}