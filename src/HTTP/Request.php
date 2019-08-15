<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\HTTP;

use sFire\Utils\StringToArray;

final class Request {


	const EMPTY_STRING_TO_NULL = 1;
	const EMPTY_ARRAY_TO_NULL = 2;
	const TRIM_STRING = 3;


	/**
	 * @var array $method
	 */
	private static $method = [];


	/**
	 * @var array $options
	 */
	private static $options = [];


	/**
	 * Enable options
	 * @param integer $option
	 */
	public static function enable($option) {

		if(false === is_int($option)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($option)), E_USER_ERROR);
		}
		
		static :: $options[$option] = true;
	}


	/**
	 * Disable options
	 * @param integer $option
	 */
	public static function disable($option) {

		if(false === is_int($option)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($option)), E_USER_ERROR);
		}
		
		if(true === isset(static :: $options[$option])) {
			unset(static :: $options[$option]);
		}
	}


    /**
     * Get variable from GET
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromGet($key = null, $default = null) {
		return static :: from('get', $key, $default, $_GET);
	}


	/**
     * Get variable from POST
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromPost($key = null, $default = null) {
		return static :: from('post', $key, $default, $_POST);
	}


	/**
     * Get variable from PUT
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromPut($key = null, $default = null) {
		return static :: from('put', $key, $default);
	}


	/**
     * Get variable from DELETE
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromDelete($key = null, $default = null) {
		return static :: from('delete', $key, $default);
	}


	/**
     * Get variable from PATCH
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromPatch($key = null, $default = null) {
		return static :: from('patch', $key, $default);
	}


	/**
     * Get variable from CONNECT
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromConnect($key = null, $default = null) {
		return static :: from('connect', $key, $default);
	}


	/**
     * Get variable from HEAD
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromHead($key = null, $default = null) {
		return static :: from('head', $key, $default);
	}


	/**
     * Get variable from OPTIONS
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromOptions($key = null, $default = null) {
		return static :: from('options', $key, $default);
	}


	/**
     * Get variable from TRACE
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromTrace($key = null, $default = null) {
		return static :: from('trace', $key, $default);
	}


	/**
     * Get variable from the current HTTP method
     * @param mixed $key
     * @param mixed $default
     * @return mixed
     */
	public static function fromCurrent($key = null, $default = null) {

		switch(static :: getMethod()) {
			
			case 'get' : $data = $_GET; break;
			case 'post' : $data = $_POST; break;
			default : $data = null;
		}

		return static :: from(static :: getMethod(), $key, $default, $data);
	}


	/**
     * Get body from current HTTP method
     * @return mixed
     */
	public static function getBody() {
		return @file_get_contents('php://input');
	}

	
	/**
	 * Get all request headers
	 * @return array
	 */
	public static function getHeaders() {
		return getallheaders();
	}


	/**
	 * Get the user agent from the request
	 * @return string
	 */
	public static function getUserAgent() {
		return isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : null;
	}


	/**
	 * Get the connection type from the request
	 * @return string
	 */
	public static function getConnection() {
		return isset($_SERVER['HTTP_CONNECTION']) ? $_SERVER['HTTP_CONNECTION'] : null;
	}


	/**
	 * Get the cache control from the request
	 * @return string
	 */
	public static function getCacheControl() {
		return isset($_SERVER['HTTP_CACHE_CONTROL']) ? $_SERVER['HTTP_CACHE_CONTROL'] : null;
	}


	/**
	 * Get the accept from the request
	 * @return string
	 */
	public static function getAccept() {
		return isset($_SERVER['HTTP_ACCEPT']) ? $_SERVER['HTTP_ACCEPT'] : null;
	}


	/**
	 * Get the accepted encoding from the request
	 * @return string
	 */
	public static function getAcceptedEncoding() {
		return isset($_SERVER['HTTP_ACCEPT_ENCODING']) ? $_SERVER['HTTP_ACCEPT_ENCODING'] : null;
	}


	/**
	 * Get the accepted language from the request
	 * @return string
	 */
	public static function getAcceptedLanguage() {
		return isset($_SERVER['HTTP_ACCEPT_LANGUAGE']) ? $_SERVER['HTTP_ACCEPT_LANGUAGE'] : null;
	}


	/**
	 * Get the authentication type from the request
	 * @return string
	 */
	public static function getAuthentication() {
		return isset($_SERVER['AUTH_TYPE']) ? $_SERVER['AUTH_TYPE'] : null;
	}


	/**
	 * Get the protocol from the request
	 * @return string
	 */
	public static function getProtocol() {
		return isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : null;
	}


	/**
	 * Get the user from the request
	 * @return string
	 */
	public static function getUser() {
		return isset($_SERVER['PHP_AUTH_USER']) ? $_SERVER['PHP_AUTH_USER'] : null;
	}


	/**
	 * Get the password from the request
	 * @return string
	 */
	public static function getPassword() {
		return isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : null;
	}


	/**
	 * Get the character set from the request
	 * @return string
	 */
	public static function getCharacterSet() {
		return isset($_SERVER['HTTP_ACCEPT_CHARSET']) ? $_SERVER['HTTP_ACCEPT_CHARSET'] : null;
	}


	/**
	 * Get the request time
	 * @return string
	 */
	public static function getRequestTime() {
		return isset($_SERVER['REQUEST_TIME_FLOAT']) ? $_SERVER['REQUEST_TIME_FLOAT'] : null;
	}


	/**
	 * Get the referer from the request
	 * @return string
	 */
	public static function getReferer() {
		return isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : null;
	}


	/**
	 * Get the content length from the request
	 * @return string
	 */
	public static function getContentLength() {
		return isset($_SERVER['CONTENT_LENGTH']) ? $_SERVER['CONTENT_LENGTH'] : null;
	}


	/**
	 * Get the scheme from the request
	 * @return string
	 */
	public static function getScheme() {
		return isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : null;
	}


	/**
	 * Get the request uri from the request
	 * @return string
	 */
	public static function getUri() {
		return isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : null;
	}


	/**
	 * Get the host from the request
	 * @return string
	 */
	public static function getHost() {
		return isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : null;
	}


	/**
	 * Get all the uploaded files in a more readable format from the request
	 * @return array
	 */
	public static function getUploadedFiles() {
		return static :: convertFilesArray($_FILES);
	}


	/**
	 * Get request header by key
	 * @param string $header
	 * @return string
	 */
	public static function getHeader($header) {

		$headers = static :: getHeaders();

		if(true === isset($headers[$header])) {
			return $headers[$header];
		}

		return null;
	}


	/**
	 * Get request header by key
	 * @param string $header
	 * @return boolean
	 */
	public static function hasHeader($header) {

		$headers = static :: getHeaders();

		return true === isset($headers[$header]);
	}


	/**
	 * Return all the data from get, post, put, delete, patch, connect, head, options and trace
	 * @return array
	 */
	public static function all() {

		if(true === isset(static :: $method['data'])) {
			return static :: $method['data'];
		}

		parse_str(static :: getBody(), $vars);

		return [

			'get' 	 	=> $_GET,
			'post'	 	=> $_POST,
			'put' 	 	=> static :: isMethod('put') 	 ? 	$vars : [],
			'delete' 	=> static :: isMethod('delete')  ? 	$vars : [],
			'patch'  	=> static :: isMethod('patch') 	 ? 	$vars : [],
			'connect'  	=> static :: isMethod('connect') ? 	$vars : [],
			'head'  	=> static :: isMethod('head')	 ? 	$vars : [],
			'options'  	=> static :: isMethod('options') ? 	$vars : [],
			'trace'  	=> static :: isMethod('trace') 	 ? 	$vars : []
		];
	}


	/**
	 * Check if request method equals $method
	 * @param string $method
	 * @return boolean
	 */
	public static function isMethod($method = null) {
		return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) == strtolower(trim($method)) : false;
	}


	/**
     * Check if request method is GET
     * @return boolean
     */
	public static function isGet() {
		return static :: isMethod('get');
	}


	/**
     * Check if request method is POST
     * @return boolean
     */
	public static function isPost() {
		return static :: isMethod('post');
	}


	/**
     * Check if request method is PUT
     * @return boolean
     */
	public static function isPut() {
		return static :: isMethod('put');
	}


	/**
     * Check if request method is DELETE
     * @return boolean
     */
	public static function isDelete() {
		return static :: isMethod('delete');
	}


	/**
     * Check if request method is PATCH
     * @return boolean
     */
	public static function isPatch() {
		return static :: isMethod('patch');
	}


	/**
     * Check if request method is CONNECT
     * @return boolean
     */
	public static function isConnect() {
		return static :: isMethod('connect');
	}


	/**
     * Check if request method is HEAD
     * @return boolean
     */
	public static function isHead() {
		return static :: isMethod('head');
	}


	/**
     * Check if request method is OPTIONS
     * @return boolean
     */
	public static function isOptions() {
		return static :: isMethod('options');
	}


	/**
     * Check if request method is TRACE
     * @return boolean
     */
	public static function isTrace() {
		return static :: isMethod('trace');
	}


	/**
	 * Return the request method
	 * @return string
	 */
	public static function getMethod() {
		return isset($_SERVER['REQUEST_METHOD']) ? strtolower($_SERVER['REQUEST_METHOD']) : null;
	}


	/**
	 * Set request method
	 * @param string $method
	 */
	public static function setMethod($method) {

		if(false === is_string($method)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($method)), E_USER_ERROR);
  		}

		$_SERVER['REQUEST_METHOD'] = strtoupper($method);
	}


	/**
	 * Set URI
	 * @param string $url
	 */
	public static function setUri($url) {

		if(false === is_string($url)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
  		}

		$_SERVER['REQUEST_URI'] = $url;
	}


	/**
	 * Set HOST
	 * @param string $host
	 */
	public static function setHost($host) {

		if(false === is_string($host)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($host)), E_USER_ERROR);
  		}

		$_SERVER['HTTP_HOST'] = $host;
	}


	/**
	 * Set SCHEME
	 * @param string $scheme
	 */
	public static function setScheme($scheme) {

		if(false === is_string($scheme)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($scheme)), E_USER_ERROR);
  		}

		$_SERVER['REQUEST_SCHEME'] = $scheme;
	}


	/**
	 * Returns the request IP address
	 * @return string
	 */
	public static function getIp() {

		if(isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		if(isset($_SERVER['HTTP-X-FORWARDED-FOR'])) {
			return $_SERVER['HTTP-X-FORWARDED-FOR'];
		}
		
		if(isset($_SERVER['HTTP_VIA'])) {
			return $_SERVER['HTTP_VIA'];
		}
		
		if(isset($_SERVER['REMOTE_ADDR'])) {
			return $_SERVER['REMOTE_ADDR'];
		}
	}


	/**
	 * Returns parsed url by giving an option key for only the value of that key to return
	 * @param string $key
	 * @return array|string|null
	 */
	public static function parseUrl($key = null) {

		if(null !== $key && false === is_string($key)) {
  			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
  		}

		$url 	= [];
		$types 	= [

			'host' 		=> 'HTTP_HOST',
			'port' 		=> 'SERVER_PORT',
			'protocol' 	=> 'REQUEST_SCHEME',
			'path'		=> 'REQUEST_URI',
			'query' 	=> 'QUERY_STRING'
		];

		foreach($types as $type => $value) {

			if(true === isset($_SERVER[$value])) {
				$url[$type] = $_SERVER[$value];
			}
		}

		if(true === isset($url['path'], $url['query']) && '' !== $url['query']) {
			$url['path'] = str_replace('?' . $url['query'], '', $url['path']);
		}

		if(true === isset($url[$key])) {
			return $url[$key];
		}

		if(null === $key) {
			return $url;
		}

		return null;
	}
	

	/**
	 * Returns variable from source while converting an array from string
	 * @param mixed $key
     * @param mixed $default 
	 * @return mixed
	 */
	private static function get($key = null, $default = null) {

		if(null !== $key && isset(static :: $method['method'], static :: $method['data'])) {

			if(static :: isMethod(static :: $method['method'])) {

				$helper  = new StringToArray();
				$data 	 = $helper -> execute($key, $default, static :: $method['data']);

				//Trim string
				if(true === isset(static :: $options[self :: TRIM_STRING])) {

					if(is_string($data) === 0) {
						$data = trim($data);
					}
				}

				//Convert empty string to NULL
				if(true === isset(static :: $options[self :: EMPTY_STRING_TO_NULL])) {

					if(is_string($data) && strlen($data) === 0) {
						$data = null;
					}
				}

				//Convert empty array to NULL
				if(true === isset(static :: $options[self :: EMPTY_ARRAY_TO_NULL])) {

					if(is_array($data) && count($data) === 0) {
						$data = null;
					}
				}

				$default = null === $data ? $default : $data;
			}

			static :: $method = [];
		}

		return $default;
	}


	/**
     * Get variable from variable source
     * @param string $type
     * @param mixed $key
     * @param mixed $default
     * @param array $source
     * @return mixed
     */
	private static function from($type, $key, $default, &$source = null) {

    	if(null === $source) {

	    	if(static :: isMethod($type)) {

	    		$source = [];
	    		static :: parseRawHttpRequest($source);
	    	}
    	}

    	static :: $method = ['method' => $type, 'data' => &$source];

    	if(null !== $key) {
    		return static :: get($key, $default);
    	}

    	return $source;
    }


    /**
     * Parses raw HTTP request string
     * @param array $data
     */
    private static function parseRawHttpRequest(array &$data) {

    	if(false === isset($_SERVER['CONTENT_TYPE'])) {
    		$_SERVER['CONTENT_TYPE'] = '';
    	}

		$input = static :: getBody();

		preg_match('/boundary=(.*)$/', $_SERVER['CONTENT_TYPE'], $matches);

		if(0 === count($matches)) {

			$json = @json_decode($input, true);

			if(true === (json_last_error() == JSON_ERROR_NONE)) {

				$data = $json;
				return $json;
			}

			parse_str(urldecode($input), $data);
			return $data;
		}

		$boundary 	= $matches[1];
		$blocks 	= preg_split("/-+$boundary/", $input);

		array_pop($blocks);

		foreach($blocks as $id => $block) {

			if('' === $block) {
				continue;
			}

			if(false !== strpos($block, 'application/octet-stream')) {
			
				preg_match("/name=\"([^\"]*)\".*stream[\n|\r]+([^\n\r].*)?$/s", $block, $matches);
				$data['files'][$matches[1]] = $matches[2];
			}
			else {

				preg_match('/name=\"([^\"]*)\"[\n|\r]+([^\n\r].*)?\r$/s', $block, $matches);
				$data[$matches[1]] = $matches[2];
			}
		}
	}


	/**
	 * Converts the $_FILES array into more logical array
	 * @param array $files
	 * @return array
	 */
	private static function convertFilesArray($files) {
		
		$result = [];

		foreach($files as $field => $data) {
			
			foreach($data as $key => $val) {
				
				$result[$field] = [];
				
				if(false === is_array($val)) {
					$result[$field] = $data;
				} 
				else {
					
					$res = [];
					static :: filesFlip($res, [], $data);
					$result[$field] += $res;
				}
			}
		}

		return $result;
	}


	/**
	 * Recursive merge arrays
	 * @param array $array1
	 * @param array $array2
	 * @return array
	 */
	private static function arrayRecursiveMerge($array1, $array2) {

		if(false === is_array($array1) || false === is_array($array2)) { 
			return $array2; 
		}

		foreach($array2 as $key => $value) {
			$array1[$key] = static :: arrayRecursiveMerge(@$array1[$key], $value);
		}

		return $array1;
	}


	/**
	 * Flip values
	 * @param array $result
	 * @param array $keys
	 * @param string $value
	 */
	private static function filesFlip(&$result, $keys, $value) {
		
		if(true === is_array($value)) {
		
			foreach($value as $k => $v) {
				
				$newkeys = $keys;
				array_push($newkeys, $k);
				static :: filesFlip($result, $newkeys, $v);
			}
		} 
		else {
			
			$res 	= $value;
			$first 	= array_shift($keys);

			array_push($keys, $first);

			foreach(array_reverse($keys) as $k) {
				$res = [$k => $res];
			}

			$result = static :: arrayRecursiveMerge($result, $res);
		}
	}
}
?>
