<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Permissions;

final class Resources {


	/**
	 * @var array $resources
	 */
	private $resources = [];


	/**
	 * Sets the resources
	 * @param array $resources
	 */
	public function setResources($resources) {

		if(false === is_array($resources)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($resources)), E_USER_ERROR);
		}

		$this -> resources = $resources;
	}


	/**
	 * Retrieve all resources or all matched resources if $match is given as a string
	 * @param string|null $match
	 * @return array
	 */
	public function getResources($match = null) {

		if(null !== $match && false === is_string($match)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($match)), E_USER_ERROR);
		}

		$resources = [];
		$match 		 = null === $match ? '.*?' : $match;

		foreach($this -> resources as $type => $value) {

			if(preg_match(sprintf('#%s#', str_replace('#', '\#', $match)), $type)) {
				$resources[] = $type;			
			}
		}

		return $resources;
	}


	/**
	 * Check if a resource exists
	 * @param string $resource
	 * @return boolean
	 */
	public function hasResource($resource) {

		if(null !== $resource && false === is_string($resource)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($resource)), E_USER_ERROR);
		}

		return true === array_key_exists($resource, $this -> resources);
	}
}