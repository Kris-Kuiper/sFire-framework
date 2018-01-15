<?php 
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Entity;

use sFire\Entity\Entity;

class File extends Entity {
	
	/**
	 * @param string $name
	 */
	private $name;


	/**
	 * @param string $basename
	 */
	private $basename;


	/**
	 * @param string $basepath
	 */
	private $basepath;


	/**
	 * @param string $extension
	 */
	private $extension;


	/**
	 * @param boolean $exists
	 */
	private $exists;


	/**
	 * @param int $filesize
	 */
	private $filesize;


	/**
	 * @param int $accesstime
	 */
	private $accesstime;


	/**
	 * @param string $path
	 */
	private $path;


	/**
	 * @param string $group
	 */
	private $group;


	/**
	 * @param int $modificationtime
	 */
	private $modificationtime;


	/**
	 * @param boolean $readable
	 */
	private $readable;


	/**
	 * @param string $mime
	 */
	private $mime;


	/**
	 * @param string $owner
	 */
	private $owner;


	/**
	 * @param boolean $writable
	 */
	private $writable;


	/**
	 * @param int $width
	 */
	private $width;


	/**
	 * @param int $height
	 */
	private $height;


	/**
     * Sets the name
     * @param string $name
     * @return File
     */
	public function setName($name) {
		
		$this -> name = $name;
		return $this;
	}

	
	/**
     * Returns the name
     * @return string
     */
	public function getName() {
		return $this -> name;
	}


	/**
     * Sets the basename
     * @param string $basename
     * @return File
     */
	public function setBasename($basename) {
		
		$this -> basename = $basename;
		return $this;
	}

	
	/**
     * Returns the basename
     * @return string
     */
	public function getBasename() {
		return $this -> basename;
	}


	/**
     * Sets the basepath
     * @param string $basepath
     * @return File
     */
	public function setBasepath($basepath) {
		
		$this -> basepath = $basepath;
		return $this;
	}

	
	/**
     * Returns the basepath
     * @return string
     */
	public function getBasepath() {
		return $this -> basepath;
	}


	/**
     * Sets the extension
     * @param string $extension
     * @return File
     */
	public function setExtension($extension) {
		
		$this -> extension = $extension;
		return $this;
	}

	
	/**
     * Returns the extension
     * @return string
     */
	public function getExtension() {
		return $this -> extension;
	}


	/**
     * Sets if file exists
     * @param boolean $exists
     * @return File
     */
	public function setExists($exists) {
		
		$this -> exists = $exists;
		return $this;
	}

	
	/**
     * Returns if file exists
     * @return boolean
     */
	public function getExists() {
		return $this -> exists;
	}

	
	/**
     * Sets the filesize
     * @param int $filesize
     * @return File
     */
	public function setFilesize($filesize) {
		
		$this -> filesize = $filesize;
		return $this;
	}

	
	/**
     * Returns the filesize
     * @return int
     */
	public function getFilesize() {
		return $this -> filesize;
	}


	/**
     * Sets the access time
     * @param int $accesstime
     * @return File
     */
	public function setAccessTime($accesstime) {
		
		$this -> accesstime = $accesstime;
		return $this;
	}

	
	/**
     * Returns the access time
     * @return int
     */
	public function getAccessTime() {
		return $this -> accesstime;
	}


	/**
     * Sets the path
     * @param string $path
     * @return File
     */
	public function setPath($path) {

		$this -> path = $path;
		return $this;
	}

	
	/**
     * Returns the path
     * @return string
     */
	public function getPath() {
		return $this -> path;
	}


	/**
     * Sets the group
     * @param string $group
     * @return File
     */
	public function setGroup($group) {
		
		$this -> group = $group;
		return $this;
	}

	
	/**
     * Returns the group
     * @return string
     */
	public function getGroup() {
		return $this -> group;
	}


	/**
     * Sets the modification time
     * @param int $modificationtime
     * @return File
     */
	public function setModificationTime($modificationtime) {
		
		$this -> modificationtime = $modificationtime;
		return $this;
	}

	
	/**
     * Returns the modification time
     * @return int
     */
	public function getModificationTime() {
		return $this -> modificationtime;
	}


	/**
     * Sets if file is readable
     * @param boolean $readable
     * @return File
     */
	public function setReadable($readable) {
		
		$this -> readable = $readable;
		return $this;
	}

	
	/**
     * Returns if file is readable
     * @return boolean
     */
	public function getReadable() {
		return $this -> readable;
	}


	/**
     * Sets the mime
     * @param string $mime
     * @return File
     */
	public function setMime($mime) {
		
		$this -> mime = $mime;
		return $this;
	}

	
	/**
     * Returns the mime
     * @return string
     */
	public function getMime() {
		return $this -> mime;
	}


	/**
     * Sets the owner
     * @param string $owner
     * @return File
     */
	public function setOwner($owner) {
		
		$this -> owner = $owner;
		return $this;
	}

	
	/**
     * Returns the owner
     * @return string
     */
	public function getOwner() {
		return $this -> owner;
	}


	/**
     * Sets if file is writable
     * @param boolean $writable
     * @return File
     */
	public function setWritable($writable) {
		
		$this -> writable = $writable;
		return $this;
	}

	
	/**
     * Returns the writable
     * @return boolean
     */
	public function getWritable() {
		return $this -> writable;
	}


	/**
     * Sets the width
     * @param int $width
     * @return File
     */
	public function setWidth($width) {
		
		$this -> width = $width;
		return $this;
	}

	
	/**
     * Returns the width
     * @return int
     */
	public function getWidth() {
		return $this -> width;
	}


	/**
     * Sets the height
     * @param int $height
     * @return File
     */
	public function setHeight($height) {
		
		$this -> height = $height;
		return $this;
	}

	
	/**
     * Returns the height
     * @return int
     */
	public function getHeight() {
		return $this -> height;
	}
}
?>