<?php 
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Logger;

use sFire\System\File;
use sFire\System\Directory;
use sFire\Application\Application;

class Logger {
	

	const HOUR 	= 'Y-m-d H';
	const DAY 	= 'Y-m-d';
	const WEEK 	= 'Y-W';
	const MONTH = 'Y-m';
	const YEAR 	= 'Y';


	/**
	 * @var sFire\System\Directory $directory 
	 */
	private $directory;


	/**
	 * @var string $suffix 
	 */
	private	$suffix;


	/**
	 * @var string $extension 
	 */
	private	$extension = '.log';


	/**
	 * @var string $mode 
	 */
	private	$mode = self :: DAY;
			

	/**
	 * Sets the directory to write to
	 * @param string $directory
	 * @return sFire\Logger\Logger
	 */
	public function setDirectory($directory) {

		if(false === is_string($directory)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($directory)), E_USER_ERROR);
		}

		$this -> directory = new Directory($directory);

		if(false === $this -> directory -> isWritable()) {
			return trigger_error(sprintf('Directory "%s" passed to %s() is not writable', $directory, __METHOD__), E_USER_ERROR);
		}

		return $this;
	}


	/**
	 * Returns the current directory
	 * @return sFire\System\Directory
	 */
	public function getDirectory() {
		return $this -> directory;
	}


	/**
	 * Set file extension
	 * @param string $extension
	 * @return sFire\Logger\Logger
	 */
	public function setExtension($extension) {

		if(false === is_string($extension)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($extension)), E_USER_ERROR);
		}

		//Prepend dot for extension if necessary
		$extension = ($extension[0] === '.') ? $extension : '.' . $extension;
		
		$this -> extension = $extension;

		return $this;
	}


	/**
	 * Returns the file extension of log file
	 * @return string
	 */
	public function getExtension() {

		if(null === $this -> extension) {
			return Application :: get(['extensions', 'log']);
		}

		return $this -> extension;
	}


	/**
	 * Sets the log rotate mode
	 * @param string $mode
	 * @return sFire\Logger\Logger
	 */
	public function setMode($mode) {

		if(false === is_string($mode)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($mode)), E_USER_ERROR);
		}

		$this -> mode = $mode;

		return $this;
	}


	/**
	 * Sets the suffix
	 * @param string $suffix
	 * @return sFire\Logger\Logger
	 */
	public function setSuffix($suffix) {

		if(false === is_string($suffix)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($suffix)), E_USER_ERROR);
		}

		$this -> suffix = $suffix;

		return $this;
	}


	/**
	 * Returns the current suffix
	 * @return string
	 */
	public function getSuffix() {
		return $this -> suffix;
	}

	
	/**
	 * Returns the current rotation mode
	 * @return string
	 */
	public function getMode() {
		return $this -> mode;
	}


	/**
	 * Write data to file
	 * @param string $data
	 * @return sFire\Logger\Logger
	 */
	public function write($data) {

		if(null === $this -> directory) {
			return trigger_error(sprintf('Directory used in %s() can not be empty', __METHOD__), E_USER_ERROR);
		}

		if(false === is_string($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		$file = $this -> generateFileName();
		$file = new File($file);

		//Create file if not exists
		if(false === $file -> exists()) {
			$file -> create();
		}

		//Append data to file
		$file -> append($data . "\n");

		return $this;
	}


	/**
	 * Generates a filename with current rotation mode
	 * @return string
	 */
	private function generateFileName() {
		return $this -> directory -> entity() -> getPath() . date($this -> getMode()) . $this -> getSuffix() . $this -> getExtension();
	}
}
?>