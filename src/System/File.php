<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\System;

use sFire\Entity\File as FileEntity;
use sFire\Utils\ArrayToEntity;
use sFire\System\Mime;

class File {


	/**
	 * @var sFire\Entity\File $file
	 */
	private $file;


	/**
	 * @var sFire\System\Mime $mime
	 */
	private $mime;


	/**
	 * Constructor
	 * @param string $file
	 */
	public function __construct($file) {

		if(false === is_string($file)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		$this -> file = new FileEntity();

		$info = (object) pathinfo($file);
		$data = [

			'readable'	=> false,
			'writable'	=> false,
			'exists'	=> false,
			'name'		=> isset($info -> filename) ? $info -> filename : null,
			'basename'	=> isset($info -> basename) ? $info -> basename : null,
			'extension'	=> isset($info -> extension) ? $info -> extension : null,
			'path'		=> $info -> dirname . DIRECTORY_SEPARATOR,
			'basepath'	=> $info -> dirname . DIRECTORY_SEPARATOR . $info -> basename
		];

		if(true === is_file($file)) {
			$data['exists'] = true;
		}

		if(true === is_readable($file)) {

			if(@exif_imagetype($file) > 0) {
				
				$size = getimagesize($file);

				$data['width']  = $size[0];
				$data['height'] = $size[1];
			}

			$data = array_merge($data, [

				'accesstime' 		=> fileatime($file),
				'group'				=> filegroup($file),
				'modificationtime'	=> filemtime($file),
				'owner'				=> fileowner($file),
				'readable'			=> true,
				'writable'			=> is_writable($file),
				'filesize'			=> filesize($file),
			]);
		}

		new ArrayToEntity($this -> file, $data);
	}


	/**
	 * Returns the file entity
	 * @return sFire\Entity\File
	 */
	public function entity() {
		return $this -> file;
	}


	/**
	 * Moves the current file to given directory
	 * @param string $directory
	 * @return sFire\System\File
	 */
	public function move($directory) {

		if(false === is_string($directory)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($directory)), E_USER_ERROR);
		}

		if(false === is_dir($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s() is not an existing directory', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false === is_writable($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s() is not writable', $directory, __METHOD__), E_USER_ERROR);
		}

		//Add directory seperator to directory
		$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

		if(false !== $this -> file -> getExists()) {
			
			if(rename($this -> file -> getBasepath(), $directory . $this -> file -> getBasename())) {

				$this -> file -> setPath($directory);
				$this -> file -> setBasepath($directory . $this -> file -> getBasename());

				$this -> refresh();
			}
		}

		return $this;
	}


	/**
	 * Deletes current file
	 * @return sFire\System\File
	 */
	public function delete() {

		if(false !== $this -> file -> getExists()) {

			if(unlink($this -> file -> getBasepath())) {
				$this -> refresh();
			}
		}

		return $this;
	}


	/**
	 * Sets access and modification time of file. If the file does not exist, it will be created
	 * @param int $time
	 * @param accesstime $time
	 * @return sFire\System\File
	 */
	public function touch($time = null, $accesstime = null) {

		if(null !== $time && false === ('-' . intval($time) == '-' . $time)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($time)), E_USER_ERROR);
		}

		if(null !== $accesstime && false === ('-' . intval($accesstime) == '-' . $accesstime)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($accesstime)), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {
			touch($this -> file -> getBasepath(), $time, $accesstime);
		}

		return $this;
	}


	/**
	 * Creates file
	 * @return sFire\System\File
	 */
	public function create() {

		if(false === $this -> file -> getExists()) {
			
			fopen($this -> file -> getBasepath(), 'w+');

			$this -> refresh();
		}

		return $this;
	}


	/**
	 * Makes a copy of the file to the destination directory
	 * @param string $directory
	 *@return sFire\System\File 
	 */
	public function copy($directory) {

		if(false === is_string($directory)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($directory)), E_USER_ERROR);
		}

		if(false === is_dir($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s() is not an existing directory', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false === is_writable($directory)) {
			return trigger_error(sprintf('Directory "%s" passed to %s() is not writable', $directory, __METHOD__), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {

			//Add directory seperator to directory
			$directory = rtrim($directory, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;

			copy($this -> file -> getBasepath(), $directory . $this -> file -> getBasename());
		}

		return $this;
	}


	/**
	 * Attempts to rename oldname to newname. If newname exists, it will be overwritten
	 * @param string $name
	 * @return sFire\System\File 
	 */
	public function rename($name) {

		if(false === is_string($name)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {
			
			if(rename($this -> file -> getBasepath(), $this -> file -> getPath() . $name)) {

				$info = (object) pathinfo($this -> file -> getPath() . $name);

				$this -> file -> setName($info -> filename);
				$this -> file -> setBasename($info -> basename);
				$this -> file -> setBasepath($this -> file -> getPath() . $this -> file -> getBasename());
				$this -> file -> setExtension($info -> extension);

				$this -> refresh();
			}
		}

		return $this;
	}


	/**
	 * Returns the content of current file
	 * @return string
	 */
	public function getContent() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();

			if(false === $this -> file -> getReadable()) {
				trigger_error(sprintf('File "%s" is not readable', $this -> file -> getBasepath()), E_USER_ERROR);
			}
			
			return file_get_contents($this -> file -> getBasepath());
		}
	}


	/**
	 * Appends data to current file
	 * @param string $data
	 * @return sFire\System\File 
	 */
	public function append($data) {

		if(false === is_string($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {

	    	if(false === is_writable($this -> file -> getBasepath())) {
	    		trigger_error(sprintf('File "%s" is not writable', $this -> file -> getBasepath()), E_USER_ERROR);
	    	}

		    $handle = fopen($this -> file -> getBasepath(), 'a');

	    	fwrite($handle, $data);
	    	fclose($handle);
		}

		return $this;
	}


	/**
	 * Prepends data to current file
	 * @param string $data
	 * @return sFire\System\File 
	 */
	public function prepend($data) {

		if(false === is_string($data)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($data)), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();

    		if(false === $this -> file -> getWritable()) {
    			trigger_error(sprintf('File "%s" is not writable', $this -> file -> getBasepath()), E_USER_ERROR);
	    	}

    		$handle 	= fopen($this -> file -> getBasepath(), 'r+');
			$len 		= strlen($data);
			$final_len 	= filesize($this -> file -> getBasepath()) + $len;
			$cache_old 	= fread($handle, $len);
			
			rewind($handle);
			
			$i = 1;
			
			while(ftell($handle) < $final_len) {

				fwrite($handle, $data);
				
				$data 		= $cache_old;
				$cache_old 	= fread($handle, $len);

				fseek($handle, $i * $len);
				
				$i++;
			}
		}

		return $this;
	}


	/**
	 * Checks whether a the current file exists
	 * @return boolean
	 */
	public function exists() {

		$this -> refresh();
		return $this -> file -> getExists();
	}


	/**
	 * Empty the current file content
	 * @return sFire\System\File 
	 */
	public function flush() {

		if(false !== $this -> file -> getExists()) {

			if(false === is_writable($this -> file -> getBasepath())) {
				return trigger_error(sprintf('File "%s" passed to %s() is not writable', $this -> file -> getBasepath(), __METHOD__), E_USER_ERROR);
	    	}

			$fh = fopen($this -> file -> getBasepath(), 'w');
			
			if(flock($fh, LOCK_EX)) {

    			ftruncate($fh, 0);
			    fflush($fh);
    			flock($fh, LOCK_UN);
    		}

			fclose($fh);

			$this -> refresh();
		}

		return $this;
	}


	/**
	 * Changes current file mode
	 * @param int $type
	 * @return boolean
	 */
	public function chmod($type) {
		
		if(null !== $type && false === ('-' . intval($type) == '-' . $type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false !== $this -> file -> getExists()) {
			return chmod($this -> file -> getBasepath(), $type);
		}

		return false;
	}


	/**
	 * Changes file owner
	 * @param mixed $user
	 * @return boolean
	 */
	public function chown($user) {

		if(false !== $this -> file -> getExists()) {
			return @chown($this -> file -> getBasepath(), $user);
		}

		return false;
	}


	/**
	 * Returns the access time of current file
	 * @return int
	 */
	public function getAccessTime() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getAccessTime();
		}
	}


	/**
	 * Returns group name of current file
	 * @return string
	 */
	public function getGroup() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getGroup();
		}
	}


	/**
	 * Returns the modification time of current file
	 * @return int
	 */
	public function getModificationTime() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getModificationTime();
		}
	}


	/**
	 * Returns the owner of current file
	 * @return string
	 */
	public function getOwner() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getOwner();
		}
	}


	/**
	 * Returns the filesize in bytes of current file
	 * @return int
	 */
	public function getFilesize() {

		if(false !== $this -> file -> getExists()) {
			return $this -> file -> getFilesize();
		}
	}


	/**
	 * Returns the information about the current file
	 * @return array
	 */
	public function info() {

		$this -> refresh();

		return $this -> file -> toArray();
	}


	/**
	 * Returns if the current file is readable
	 * @return boolean
	 */
	public function isReadable() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getReadable();
		}

		return false;
	}


	/**
	 * Returns if the current file is writable
	 * @return boolean
	 */
	public function isWritable() {

		if(false !== $this -> file -> getExists()) {

			$this -> refresh();
			
			return $this -> file -> getWritable();
		}

		return false;
	}


	/**
	 * Returns mime type of current file
	 * @return string
	 */
	public function getMime() {

		if(null === $this -> mime) {
			$this -> mime = new Mime();
		}

		return Mime :: get($this -> file -> getExtension());
	}


	/**
	 * Clears file status cache
	 * @return sFire\System\File 
	 */
	public function refresh() {

		clearstatcache();

		$file = new File($this -> file -> getBasepath());
		$this -> file = $file -> entity();

		return $this;
	}
}
?>