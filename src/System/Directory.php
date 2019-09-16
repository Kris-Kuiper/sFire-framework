<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\System;

use sFire\Entity\Directory as DirectoryEntity;
use sFire\System\File;
use sFire\Utils\ArrayToEntity;

class Directory {


	const TYPE_ARRAY 	= 'array';
	const TYPE_JSON 	= 'json';
	const TYPE_OBJECT 	= 'object';
	const TYPE_DEFAULT 	= 'default';


	/**
	 * @var sFire\Entity\Directory $directory
	 */
	private $directory;


	/**
	 * Constructor
	 * @param string $directory
	 */
	public function __construct($directory) {

		$this -> directory = new DirectoryEntity();

		$name = preg_split('#[\\/]#', $directory);
		$name = end($name);
		$data = [
			
			'path' 	 	=> $directory,
			'basepath' 	=> dirname($directory),
			'name' 	 	=> $name,
			'exists' 	=> false, 
			'readable'	=> false,
			'writable'	=> false,
		];

		if(is_dir($directory)) {
			
			$data = array_merge($data, [

				'accesstime' 		=> fileatime($directory),
				'group'				=> filegroup($directory),
				'modificationtime'	=> filemtime($directory),
				'owner'				=> fileowner($directory),
				'readable'			=> is_readable($directory),
				'writable'			=> is_writable($directory),
				'exists'			=> true
			]);
		}

		new ArrayToEntity($this -> directory, $data);
	}


	/**
	 * Returns a Directory Entity
	 * @return sFire\Entity\Directory
	 */
	public function entity() {
		return $this -> directory;
	}


	/**
	 * Move the current directory to a new directory including its content
	 * @param string $directory
	 * @return sFire\System\Directory
	 */
	public function move($directory) {

		if(false === is_string($directory)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($directory)), E_USER_ERROR);
		}

		if(false === is_dir($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s is not an existing directory', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false === is_writable($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s is not writable', $directory, __METHOD__), E_USER_ERROR);
		}

		//Add directory seperator to directory
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if(false !== $this -> directory -> getExists()) {

			if(rename($this -> directory -> getPath(), $directory . $this -> directory -> getName())) {

				$this -> directory -> setPath($directory);
				$this -> directory -> setBasepath($directory . $this -> directory -> getName());
			}
		}

		return $this;
	}


	/**
	 * Delete the current directory
	 * @return sFire\System\Directory
	 */
	public function delete() {

		if(false !== $this -> directory -> getExists()) {

			if(rmdir($this -> directory -> getBasepath())) {
				$this -> refresh();
			}
		}

		return $this;
	}


	/**
	 * Copy the current directory contents to a new directory including its content
	 * @param string $directory
	 * @return sFire\System\Directory
	 */
	public function copy($directory) {

		if(false === is_dir($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s is not an existing directory', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false === is_writable($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s is not writable', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false !== $this -> directory -> getExists()) {
			$this -> rcopy($this -> directory -> getPath(), $directory);
		}

		return $this;
	}


	/**
	 * Rename the current directory
	 * @param string $name
	 * @return sFire\System\Directory
	 */
	public function rename($name) {

		if(false === is_string($name)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(false !== $this -> directory -> getExists()) {
			
			if(rename($this -> directory -> getPath(), $this -> directory -> getBasepath() . $name)) {

				$this -> directory -> setName($name);
				$this -> directory -> setPath($this -> directory -> getBasepath() . $name);
			}
		}

		return $this;
	}


	/**
	 * Returns information about the current directory
	 * @return array
	 */
	public function info() {

		$this -> refresh();

		return $this -> directory -> toArray();
	}


	/**
	 * Returns total size in bytes of all the files (recusive) in the current directory
	 * @return int
	 */
	public function getSize() {

	    $bytestotal = 0;
	    $path 		= realpath($this -> directory -> getPath());

	    if($path !== false) {

	        foreach(new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path, \FilesystemIterator :: SKIP_DOTS)) as $object) {
	            $bytestotal += $object->getSize();
	        }
	    }

	    return $bytestotal;
	}


	/**
	 * Returns files and folders about the current directory
	 * @param string $type
	 * @return array|object|string
	 */
	public function getContent($type = 'default') {

		if(false === is_string($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false === is_dir($this -> directory -> getPath())) {
			return trigger_error(sprintf('Directory "%s" does not exists', $this -> directory -> getPath()), E_USER_ERROR);	
		}

		$files = array_values(array_diff(scandir($this -> directory -> getPath()), ['.', '..']));

		switch($type) {

			case 'array' : 
				return $files;

			case 'object' :
				return (object) $files;

			case 'json' :
				return json_encode($files);

			default : 

				$content = [];

				foreach($files as $file) {

					if(is_dir($this -> directory -> getPath() . $file)) {
						
						$content[] = new Directory($file);
						continue;
					}

					$content[] = new File($this -> directory -> getPath() . $file);
				}

				return $content;
		}
	}


	/**
	 * Creates recursively (is necessary) new directories
	 * @param int $mode
	 * @return sFire\System\Directory
	 */
	public function create($mode = 0777) {

		if(false === ('-' . intval($mode) == '-' . $mode)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($mode)), E_USER_ERROR);
		}

		$paths = preg_split('#[\\/]#', $this -> directory -> getPath());
		$build = '';

		foreach($paths as $path) {

			$build .= $path . DIRECTORY_SEPARATOR;

			if(false === @is_dir($build)) {
				@mkdir($build, $mode);
			}
		}

		$this -> refresh();

		return $this;
	}


	/**
	 * Returns boolean if current directory exists
	 * @return boolean
	 */
	public function exists() {

		$this -> refresh();
		return $this -> directory -> getExists();
	}


	/**
	 * Returns true if current directory has successfully changed file mode, otherwise false
	 * @param int|string $type
	 * @return boolean
	 */
	public function chmod($type) {
		
		if(false !== $this -> directory -> getExists()) {
			return chmod($this -> directory -> getPath(), $type);
		}

		return false;
	}


	/**
	 * Returns true if current directory has successfully changed owner, otherwise false
	 * @param string $username
	 * @return boolean
	 */
	public function chown($username) {

		if(false === is_string($username)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($username)), E_USER_ERROR);
		}

		if(false !== $this -> directory -> getExists()) {
			return chown($this -> directory -> getPath(), $username);
		}

		return false;
	}


	/**
	 * Returns boolean if the current directory is readable
	 * @return boolean
	 */
	public function isReadable() {

		if(false !== $this -> directory -> getExists()) {

			$this -> refresh();
			
			return $this -> directory -> getReadable();
		}

		return false;
	}


	/**
	 * Returns boolean if the current directory is writable
	 * @return boolean
	 */
	public function isWritable() {

		if(false !== $this -> directory -> getExists()) {

			$this -> refresh();
			
			return $this -> directory -> getWritable();
		}

		return false;
	}


	/**
	 * Drop old and create new directory entity to update information about current directory
	 * @return sFire\System\Directory
	 */
	public function refresh() {

		$directory = new Directory($this -> directory -> getPath());
		$this -> directory = $directory -> entity();

		return $this;
	}


	/**
	 * Copy all contens of directory to an existing directory
	 * @param string $source
	 * @param string $destination
	 */
	private function rcopy($source, $destination) { 
	    
	    if(false === is_string($source)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($source)), E_USER_ERROR);
		}

		if(false === is_string($destination)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($destination)), E_USER_ERROR);
		}

		if(false === is_dir($destination)) {

			$tmp = new Directory($destination);
			$tmp -> create();
		}

	    $directory = opendir($source); 
	    
	    while(false !== ($file = readdir($directory))) { 

	        if(false === in_array($file, ['.', '..'])) { 

	            if(is_dir($source . DIRECTORY_SEPARATOR . $file) ) { 
	                $this -> rcopy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file); 
	            } 
	            else { 
	                copy($source . DIRECTORY_SEPARATOR . $file, $destination . DIRECTORY_SEPARATOR . $file); 
	            } 
	        } 
	    } 

	    closedir($directory); 
	} 
}