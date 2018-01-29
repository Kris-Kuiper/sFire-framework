<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Permissions;

use sFire\Permissions\Resources;

final class ACL {


	/**
	 * @var array $permissions
	 */
	private $permissions = [];


	/**
	 * Returns if a role is allowed access to the resource
	 * @param string $role
	 * @param string $resource
	 * @return boolean 
	 */
	public function isAllowed($role, $resource) {

		if(false === is_string($role)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($role)), E_USER_ERROR);
		}

		if(false === is_string($resource)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($resource)), E_USER_ERROR);
		}

		if(true === isset($this -> permissions[$role][$resource])) {
			return $this -> permissions[$role][$resource];
		}

		return false;
	}


	/**
	 * Returns if a role is denied access to the resource
	 * @param string $role
	 * @param string $resource
	 * @return boolean 
	 */
	public function isDenied($role, $resource) {
		return !$this -> isAllowed($role, $resource);
	}


	/**
	 * Allow a resource by role
	 * @param string|array $roles
	 * @param string|array $resources
	 * @return sFire\Permissions\ACL
	 */
	public function allow($roles, $resources) {
		return $this -> fill($roles, $resources, true);
	}


	/**
	 * Deny a resource by role
	 * @param string|array $roles
	 * @param string|array $resources
	 * @return sFire\Permissions\ACL
	 */
	public function deny($roles, $resources) {
		return $this -> fill($roles, $resources, false);
	}


	/**
	 * Let a single or multiple roles inherit the resources of other single or multiple roles
	 * @param string|array $roles
	 * @param string|array $inherits
	 * @return sFire\Permissions\ACL
	 */
	public function inherit($roles, $inherits) {

		if(true === is_string($roles)) {
			$roles = [$roles];
		}

		if(true === is_string($inherits)) {
			$inherits = [$inherits];
		}

		if(false === is_array($roles)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($roles)), E_USER_ERROR);
		}

		if(false === is_array($inherits)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($inherits)), E_USER_ERROR);
		}

		foreach($inherits as $inherit) {

			if(true === isset($this -> permissions[$inherit])) {

				foreach($this -> permissions[$inherit] as $resource => $allowed) {

					foreach($roles as $role) {
						$this -> fill($role, $resource, $allowed);
					}
				}

			}
		}

		return $this; 
	}


	/**
	 * Returns all the roles as an array
	 * @return array
	 */
	public function getRoles() {

		$roles = [];

		foreach($this -> permissions as $role => $permissions) {
			$roles[$role] = $this -> getRole($role);
		}

		return $roles;
	}


	/**
	 * Returns the roles in array
	 * @return array
	 */
	public function getRole($role) {
		
		if(false === is_string($role)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($role)), E_USER_ERROR);
		}

		foreach($this -> permissions as $type => $resources) {

			if($role === $type) {
				
				$p = new Resources();
				$p -> setResources($resources);

				return $p;
			}
		}

		return null;
	}


	/**
	 * Returns all the resourses in array
	 * @param string|null $match
	 * @return array
	 */
	public function getResources($match = null) {

		if(null !== $match && false === is_string($match)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($match)), E_USER_ERROR);
		}

		$permissions = [];
		$match 		 = null === $match ? '.*?' : $match;

		foreach($this -> permissions as $permission) {

			foreach($permission as $type => $value) {

				if(preg_match(sprintf('#%s#', str_replace('#', '\#', $match)), $type)) {
					$permissions[] = $type;			
				}
			}
		}

		return array_unique($permissions);
	}


	/**
	 * Add a new permission entry
	 * @param string|array $roles
	 * @param string|array $resources
	 * @param boolean $allow
	 * @return sFire\Permissions\ACL
	 */
	private function fill($roles, $resources, $allow) {

		if(true === is_string($roles)) {
			$roles = [$roles];
		}

		if(true === is_string($resources)) {
			$resources = [$resources];
		}

		if(false === is_array($roles)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($roles)), E_USER_ERROR);
		}

		if(false === is_array($resources)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($resources)), E_USER_ERROR);
		}

		if(false === is_bool($allow)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($allow)), E_USER_ERROR);
		}

		foreach($roles as $role) {

			if(false === is_string($role)) {
				return trigger_error(sprintf('Argument 1 passed to %s() must contain only strings, "%s" given', __METHOD__, gettype($role)), E_USER_ERROR);
			}

			if(false === isset($this -> permissions[$role])) {
				$this -> permissions[$role] = [];
			}

			foreach($resources as $resource) {

				if(false === is_string($resource)) {
					return trigger_error(sprintf('Argument 2 passed to %s() must contain only strings, "%s" given', __METHOD__, gettype($resource)), E_USER_ERROR);
				}

				$this -> permissions[$role][$resource] = $allow;
			}
		}

		return $this;
	}
}
?>