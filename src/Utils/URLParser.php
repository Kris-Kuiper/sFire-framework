<?php
namespace sFire\Utils;

class URLParser {


	const UNTIL_SCHEME 		= 'scheme';
	const UNTIL_HOST 		= 'host';
	const UNTIL_USER 		= 'user';
	const UNTIL_PASSWORD	= 'pass';
	const UNTIL_PATH 		= 'path';
	const UNTIL_QUERY		= 'query';
	const UNTIL_FRAGMENT	= 'fragment';


	/**
	 * @var array $url
	 */
	private $url;


	/**
	 * Constructor
	 * @param string $url
	 * @return sFire\Utils\URLParser
	 */
	public function __construct($url) {

		if(false === is_string($url)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		$this -> url = (object) parse_url($url);
	}


	/**
	 * Returns the scheme of the url
	 * @return string
	 */
	public function getScheme() {

		if(true === isset($this -> url -> scheme)) {
			return $this -> url -> scheme;
		}
	}


	/**
	 * Returns the password of the url
	 * @return string
	 */
	public function getPassword() {

		if(true === isset($this -> url -> pass)) {
			return $this -> url -> pass;
		}
	}

	
	/**
	 * Returns the user of the url
	 * @return string
	 */
	public function getUser() {

		if(true === isset($this -> url -> user)) {
			return $this -> url -> user;
		}
	}


	/**
	 * Returns the query of the url
	 * @return string
	 */
	public function getQuery() {

		if(true === isset($this -> url -> query)) {
			return $this -> url -> query;
		}
	}


	/**
	 * Returns the port of the url
	 * @return string
	 */
	public function getPort() {

		if(true === isset($this -> url -> port)) {
			return $this -> url -> port;
		}
	}


	/**
	 * Returns the host of the url
	 * @return string
	 */
	public function getHost() {

		if(true === isset($this -> url -> host)) {
			return $this -> url -> host;
		}
	}


	/**
	 * Returns the path of the url
	 * @return string
	 */
	public function getPath() {

		if(true === isset($this -> url -> path)) {
			return $this -> url -> path;
		}
	}


	/**
	 * Returns the fragment (after the #) of the url
	 * @return string
	 */
	public function getFragment() {

		if(true === isset($this -> url -> fragment)) {
			return $this -> url -> fragment;
		}
	}


	/**
	 * Returns the trimmed path of the url (without leading forward slashes)
	 * @return string
	 */
	public function trimPath() {

		if(true === isset($this -> url -> path)) {
			return ltrim($this -> url -> path, '/');
		}
	}


	/**
	 * Parses the query string and converts it into an Array
	 * @return array
	 */
	public function parseQuery() {

		parse_str($this -> getQuery(), $query);

		return $query;
	}


	/**
	 * Adds all the components of the url until the end or until the first parameter has been reached to generate a URL
	 * @param string $till
	 * @return string
	 */
	public function generate($till = self :: UNTIL_FRAGMENT) {

		$url = null !== $this -> getScheme() ? $this -> getScheme() . '://' : '';

		if($till !== self :: UNTIL_SCHEME) {
			
			$url .= null !== $this -> getUser() ? $this -> getUser() . ':' : '';

			if($till !== self :: UNTIL_USER) {

				$url .= null !== $this -> getUser() ? $this -> getPassword() . '@' : '';
				
				if($till !== self :: UNTIL_PASSWORD) {

					$url .= $this -> getHost();

					if($till !== self :: UNTIL_HOST) {
						
						$url .= null !== $this -> getPath() ? $this -> getPath() : '/';

						if($till !== self :: UNTIL_PATH) {
							
							$url .= null !== $this -> getQuery() ? '?' . $this -> getQuery() : '';

							if($till !== self :: UNTIL_QUERY) {
								$url .= '#' . $this -> getFragment();						
							}
						}
					}
				}
			}
		}

		return $url;
	}


	/**
	 * Returns the parsed URL
	 * @return object
	 */
	public function parseUrl() {
		return $this -> url;
	}
}