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

class Directory extends Entity {
	

	/**
     * @var string $name
     */
	private $name;
	

	/**
     * @var boolean $exists
     */	
	private $exists;
	

	/**
     * @var int $accesstime
     */	
	private $accesstime;
	

	/**
     * @var string $path
     */		
	private $path;
	

	/**
     * @var string $basepath
     */			
	private $basepath;
	

	/**
     * @var string $group
     */			
	private $group;
	

	/**
     * @var int $modificationtime
     */		
	private $modificationtime;
	

	/**
     * @var boolean $readable
     */		
	private $readable;
	

	/**
     * @var string $owner
     */		
	private $owner;
	

	/**
     * @var boolean $writable
     */			
	private $writable;

	
	/**
     * Sets the name
     * @param string $name
     * @return Directory
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
     * Sets if directory exists
     * @param boolean $exists
     * @return Directory
     */
	public function setExists($exists) {
		
		$this -> exists = $exists;
		return $this;
	}

	
	/**
     * Returns if directory exists
     * @return boolean
     */
	public function getExists() {
		return $this -> exists;
	}
	
	
	/**
     * Sets the access time
     * @param int $accesstime
     * @return Directory
     */
	public function setAccessTime($accesstime) {
		
		$this -> accesstime = $accesstime;
		return $this;
	}

	
	/**
     * Returns the access time
     * @return $access
     */
	public function getAccessTime() {
		return $this -> accesstime;
	}

	
	/**
     * Sets the path
     * @param string $path
     * @return Directory
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
     * Sets the basepath
     * @param string $basepath
     * @return Directory
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
     * Sets the group
     * @param string $group
     * @return Directory
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
     * @return Directory
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
     * Sets readable
     * @param boolean $readable
     * @return Directory
     */
	public function setReadable($readable) {
		
		$this -> readable = $readable;
		return $this;
	}

	
	/**
     * Returns if directory is readable
     * @return boolean
     */
	public function getReadable() {
		return $this -> readable;
	}

	
	/**
     * Sets the owner
     * @param string $owner
     * @return Directory
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
     * Sets writable
     * @param boolean $writable
     * @return Directory
     */
	public function setWritable($writable) {
		
		$this -> writable = $writable;
		return $this;
	}

	
	/**
     * Returns if directory is writable
     * @return boolean
     */
	public function getWritable() {
		return $this -> writable;
	}
}