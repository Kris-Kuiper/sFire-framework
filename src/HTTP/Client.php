<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\HTTP;

use sFire\Entity\Response;
use sFire\Utils\URLParser;
use sFire\System\File;

#Http client
final class Client {

	const MAXREDIRS 				= 'CURLOPT_MAXREDIRS';
	const AUTH_BASIC 				= 'CURLAUTH_BASIC';
	const AUTH_DIGEST 				= 'CURLAUTH_DIGEST';
	const AUTH_GSSNEGOTIATE 		= 'CURLAUTH_GSSNEGOTIATE';
	const AUTH_NTLM 				= 'CURLAUTH_NTLM';
	const AUTH_ANY 					= 'CURLAUTH_ANY';
	const AUTH_ANYSAFE 				= 'CURLAUTH_ANYSAFE';
	const HTTP_NONE					= 'CURL_HTTP_VERSION_NONE';
	const HTTP_1_0					= 'CURL_HTTP_VERSION_1_0';
	const HTTP_1_1					= 'CURL_HTTP_VERSION_1_1';
	const HTTP_2_0					= 'CURL_HTTP_VERSION_2_0';
	const HTTP_2TLS					= 'CURL_HTTP_VERSION_2TLS';
	const HTTP_2_PRIOR_KNOWLEDGE 	= 'CURL_HTTP_VERSION_2_PRIOR_KNOWLEDGE';


	/**
	 * string $url
	 */
	private $url;


	/**
	 * string $method
	 */
	private $method;


	/**
	 * array $options
	 */
	private $options = [];


	/**
	 * array $headers
	 */
	private $headers = [];


	/**
	 * array $params
	 */
	private $params = [];


	/**
	 * array $files
	 */
	private $files = [];


	/**
	 * array $cookies
	 */
	private $cookies = [];


	/**
	 * sFire\Entity\Response $response
	 */
	private $response;


	/**
	 * Constructor
	 * @return sFire\HTTP\Client;
	 */
	public function __construct() {

		if(false === function_exists('curl_init')) {
			return trigger_error(sprintf('Function "curl_init" should be loaded to use %s', __CLASS__), E_USER_ERROR);
		}

		$this -> response = new Response();
	}


	/**
	 * Rest PUT method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function put($url, $closure = null) {
		return $this -> method($url, $closure, 'put');
	}


	/**
	 * Rest DELETE method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function delete($url, $closure = null) {
		return $this -> method($url, $closure, 'delete');
	}

	
	/**
	 * Rest POST method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function post($url, $closure = null) {
		return $this -> method($url, $closure, 'post');
	}

	
	/**
	 * Rest GET method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function get($url, $closure = null) {
		return $this -> method($url, $closure, 'get');
	}


	/**
	 * Rest PATCH method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function patch($url, $closure = null) {
		return $this -> method($url, $closure, 'patch');
	}


	/**
	 * Rest HEAD method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function head($url, $closure = null) {
		return $this -> method($url, $closure, 'head');
	}


	/**
	 * Rest OPTIONS method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function options($url, $closure = null) {
		return $this -> method($url, $closure, 'options');
	}


	/**
	 * Rest TRACE method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function trace($url, $closure = null) {
		return $this -> method($url, $closure, 'trace');
	}


	/**
	 * Rest CONNECT method 
	 * @param string $url
	 * @param object $closure
	 * @return sFire\HTTP\Client
	 */
	public function connect($url, $closure = null) {
		return $this -> method($url, $closure, 'connect');
	}


	/**
	 * Returns the body of the response
	 * @return string
	 */
	public function getBody() {
		return $this -> response -> getBody();
	}


	/**
	 * Returns the body in JSON format (if response is valid JSON)
	 * @return string
	 */
	public function getJson() {
		return $this -> response -> getJson();
	}


	/**
	 * Returns the headers of the response
	 * @return array
	 */
	public function getHeaders() {
		return $this -> response -> getHeaders();
	}


	/**
	 * Returns the status code of the response
	 * @return array
	 */
	public function getStatus() {
		return $this -> response -> getStatus();
	}


	/**
	 * Returns information about the response
	 * @return array
	 */
	public function getInfo() {
		return $this -> response -> getInfo();
	}


	/**
	 * Returns the response
	 * @return array
	 */
	public function getResponse() {
		return $this -> response -> getResponse();
	}


	/**
	 * Returns all the cookies from the response
	 * @return array
	 */
	public function getCookies() {
		return $this -> response -> getCookies();
	}


	/**
	 * Adds a HTTP authentication method
	 * @param string $username
	 * @param string $password
	 * @param string $type
	 * @return sFire\HTTP\Client
	 */
	public function authenticate($username, $password, $type = self :: AUTH_ANY) {

		if(false === is_string($username)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($username)), E_USER_ERROR);
        }

        if(false === is_string($password)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($password)), E_USER_ERROR);
        }

        if(false === defined($type)) {
			return trigger_error(sprintf('Argument 3 passed to %s() is not a valid authentication type', __METHOD__), E_USER_ERROR);
        }

		$this -> options[CURLOPT_USERPWD]  = sprintf('%s:%s', $username, $password);
		$this -> options[CURLOPT_HTTPAUTH] = constant($type);

		return $this;
	}


	/**
	 * Add a new key value param to the url or query
	 * @param $key string
	 * @param $value string|array
	 * @param $encode boolean
	 * @return sFire\HTTP\Client
	 */
	public function addParam($key, $value, $encode = false) {

		if(false === is_string($key)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
        }

        if(false === is_string($value) && false === is_array($value)) {
            return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string or array, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
        }

        if(false === is_bool($encode)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($encode)), E_USER_ERROR);
		}

		$this -> params[] = (object) ['key' => $key, 'value' => $value, 'encode' => $encode];

		return $this;
	}


	/**
	 * Attach file to request
	 * @param $file string|sFire\System\File
	 * @param $name string
	 * @param $string mime
	 * @return sFire\HTTP\Client
	 */
	public function addFile($file, $name = null, $mime = null) {

		if('post' !== $this -> method) {
			return trigger_error('File uploads are only supported with POST request method', E_USER_ERROR);
		}

		if(false === $file instanceof File && false === is_string($file)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or instance of sFire\\System\\File, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
        }

        if(null !== $name && false === is_string($name)) {
            return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
        }

		if(null !== $mime && false === is_string($mime)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($mime)), E_USER_ERROR);
		}

		if(true === is_string($file)) {
			$file = new File($file);
		}

		if(false === $file -> isReadable()) {
			return trigger_error(sprintf('File %s passed to %s() is not readable', $file -> entity() -> getBasepath(), __METHOD__), E_USER_ERROR);
		}

		if(null === $name) {
			$name = $file -> entity() -> getBasename();
		}

		if(null === $mime) {
			$mime = $file -> getMime();
		}

		$this -> files[] = (object) ['file' => $file, 'name' => $name, 'mime' => $mime];

		return $this;
	}


	/**
	 * Set a user agent to the request
	 * @param string $key
	 * @return sFire\HTTP\Client
	 */
	public function userAgent($useragent) {

		if(false === is_string($useragent)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($useragent)), E_USER_ERROR);
        }

		$this -> options[CURLOPT_USERAGENT] = $useragent;

		return $this;
	}


	/**
	 * Set the connection and response timeout in seconds for the request
	 * @param int $connection
	 * @param int $response
	 * @return sFire\HTTP\Client
	 */
	public function timeout($connection = 30, $response = 30) {

		if(false === ('-' . intval($connection) == '-' . $connection)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($connection)), E_USER_ERROR);
        }

        if(false === ('-' . intval($response) == '-' . $response)) {
            return trigger_error(sprintf('Argument 2 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($response)), E_USER_ERROR);
        }

		$this -> options[CURLOPT_CONNECTTIMEOUT] = intval($connection);
		$this -> options[CURLOPT_TIMEOUT] 		 = intval($response);

		return $this;
	}


	/**
	 * Set the port
	 * @param int $port
	 * @return sFire\HTTP\Client
	 */
	public function port($port) {

		if(false === ('-' . intval($port) == '-' . $port)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($port)), E_USER_ERROR);
		}

		if($port < 1 || $port > 65535) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be between 1 and 65535, "%s" given', __METHOD__, $port), E_USER_ERROR);
		}

		$this -> options[CURLOPT_PORT] = intval($port);

		return $this;
	}


	/**
	 * Set the referer
	 * @param int $referer
	 * @return sFire\HTTP\Client
	 */
	public function referer($referer) {

		if(false === is_string($referer)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($referer)), E_USER_ERROR);
		}

		$this -> options[CURLOPT_REFERER] = $referer;

		return $this;
	}


	/**
	 * Will follow as many "Location: " headers until the amount given
	 * @param int $amount
	 * @return sFire\HTTP\Client
	 */
	public function follow($amount) {

		if(false === ('-' . intval($amount) == '-' . $amount)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($amount)), E_USER_ERROR);
		}

		$this -> options[CURLOPT_FOLLOWLOCATION] = true;
		$this -> options[CURLOPT_MAXREDIRS] 	 = intval($amount);

		return $this;
	}


	/**
	 * Set the HTTP version protocol
	 * @param int $version
	 * @return sFire\HTTP\Client
	 */
	public function httpVersion($version) {

		if(false === defined($version)) {
			return trigger_error(sprintf('Argument 1 passed to %s() is not a valid HTTP protocol version', __METHOD__), E_USER_ERROR);
		}

		$this -> options[CURLOPT_HTTP_VERSION] = constant($version);

		return $this;
	}


	/**
	 * Add a custom header to the request
	 * @param string $key
	 * @param string $value
	 * @return sFire\HTTP\Client
	 */
	public function addHeader($key, $value) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		$this -> headers[] = sprintf('%s:%s', $key, $value);

		return $this;
	}


	/**
	 * Add a cookie to the request
	 * @param string $key
	 * @param string $value
	 * @return sFire\HTTP\Client
	 */
	public function addCookie($key, $value) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		$this -> cookies[] = sprintf('%s=%s', $key, $value);

		return $this;
	}


	/**
	 * Adds new params to existing params to format the url
	 * @return string
	 */
	private function formatUrl() {

		$jar 	= [];
		$query 	= $this -> url -> getQuery();

		if(null !== $query) {
			$jar[] = $query;
		}
		
		$query 	= '';

		if(count($this -> params) > 0 || count($jar) > 0) {

			if($this -> method === 'get') {
				
				foreach($this -> params as  $param) {

					$key  	= $param -> encode ? urlencode($param -> key) : $param -> key;
					$value 	= $param -> encode ? urlencode($param -> value) : $param -> value;

					$jar[] = sprintf('%s=%s', $key, $value);
				}
			}

			$query = '?' . implode('&', $jar);
		}

		return $this -> url -> generate(URLParser :: UNTIL_PATH) . $query;
	}


	/**
	 * Formats all the parameters
	 * @return array
	 */
	private function formatParams() {

		$params = [];

		//Params
		foreach($this -> params as $param) {

			$key  	= $param -> encode ? urlencode($param -> key) : $param -> key;
			$value 	= $param -> encode ? urlencode($param -> value) : $param -> value;

			$params[$key] = $value;
		}

		//Files
		foreach($this -> files as $file) {
			
			if(function_exists('curl_file_create')) {
				$value = curl_file_create($file -> file -> entity() -> getBasepath());
			} 
			else { 
			  	$value = '@' . realpath($file -> file -> entity() -> getBasepath());
			}

			$params[$file -> name] = $value;
		}

		return $params;
	}


	/**
	 * Formats all the cookie parameters
	 * @return array
	 */
	private function formatCookies() {
		return implode(';', $this -> cookies);
	}


	/**
	 * Sends the request and sets all the response data
	 * @return array
	 */
	public function send() {

		//Set default status
		$this -> response -> setStatus([

			'code' 		=> 0,
		    'protocol' 	=> '',
		    'status' 	=> '',
		    'text' 		=> ''
		]);

		$curl = curl_init();

		curl_setopt_array($curl, $this -> createOptions());

		$response = curl_exec($curl);

		if(false !== $response) {

			$header_size = curl_getinfo($curl, CURLINFO_HEADER_SIZE);
			$headers 	 = substr($response, 0, $header_size);
			$headers 	 = $this -> parseHeaders($headers);
			$body 		 = substr($response, $header_size);
			$json 		 = @json_decode($body, true);

			$this -> response -> setHeaders($headers['headers']);
			$this -> response -> setBody($body);
			$this -> response -> setStatus($headers['status']);
			$this -> response -> setCookies($this -> parseCookies($response));
			$this -> response -> setResponse($response);

			if(true === (json_last_error() == JSON_ERROR_NONE)) {
				$this -> response -> setJson($json);
			}
		}

		$this -> response -> setInfo(curl_getinfo($curl));

		curl_close($curl);

		return $this;
	}


	/**
	 * Parses headers and forms it into an Array
	 * @param string $str
	 * @return array
	 */
	private function parseHeaders($str) {

		if(false === is_string($str)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($str)), E_USER_ERROR);
		}
		
		$headers = [];
		$status  = [];

		foreach(explode("\n", $str) as $header) {
			
			if(preg_match('/(:)/is', $header)) {
				list($key, $value) = preg_split('/(:)/is', $header, 2);
			}
			else {
				$key = $header;
			}

			if(trim($key) !== '') {

				if(preg_match('/(https?\/[0-9]\.[0-9])/i', $key, $http)) {

					$status['code'] = null;

					if(preg_match('/([0-9]+)/', ltrim($key, $http[0]), $code)) {
						$status['code'] = $code[0];
					}

					$status['protocol'] = $http[0];
					$status['status'] 	= trim($key);
					$status['text'] 	= trim(ltrim(trim(ltrim($key, $status['protocol'])), $status['code']));
					
				}
				else {
					$headers[strtolower($key)] = trim($value);
				}
			}
		}

		return ['headers' => $headers, 'status' => $status];
	}


	/**
	 * Parses the cookies from the headers and forms it into an Array
	 * @param string $str
	 * @return array
	 */
	private function parseCookies($str) {  
	    
	    if(false === is_string($str)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($str)), E_USER_ERROR);
		}

	    $jar 	 = [];
		$headers = explode("\n", trim($str));

		foreach($headers as $header) {

			if(preg_match('/^set-cookie: /i', $header)) {
				
				$cookie = [];

				//Match name and value
				preg_match('/^set-cookie: ([^=]+)=([^;]+)/i', $header, $match);

				if(count($match) === 3) {
					
					$cookie['name']  = $match[1];
					$cookie['value'] = $match[2];
				
					//Match expires
					$cookie['expires'] = null;

					preg_match('/; expires=([^;]+)/i', $header, $match);

					if(count($match) === 2) {
						$cookie['expires'] = strtotime(urldecode(trim($match[1])));
					}

					//Match domain
					$cookie['domain'] = null;

					preg_match('/; domain=([^;]+)/i', $header, $match);

					if(count($match) === 2) {
						$cookie['domain'] = urldecode(trim($match[1]));
					}

					//Match secure
					$cookie['secure'] = false;

					preg_match('/; secure/i', $header, $match);

					if(count($match) === 1) {
						$cookie['secure'] = true;
					}

					//Match httponly
					$cookie['httponly'] = false;

					preg_match('/; httponly/i', $header, $match);

					if(count($match) === 1) {
						$cookie['httponly'] = true;
					}

					//Add cookie to the jar
					if(count($cookie) > 0) {
						$jar[] = $cookie;
					}
				}
			}
		}

		return $jar;
	}


	/**
	 * Generates and returns all the options
	 * @return array
	 */
	private function createOptions() {

		$default = [

			CURLOPT_RETURNTRANSFER 	=> true,
			CURLOPT_HEADER 			=> true,
			CURLOPT_TIMEOUT			=> 30,
			CURLOPT_URL 			=> $this -> formatUrl(),
			CURLOPT_COOKIE			=> $this -> formatCookies(),
			CURLOPT_HTTPHEADER		=> $this -> headers
		];

		if('get' !== $this -> method) {
			$this -> options[CURLOPT_POSTFIELDS] = $this -> formatParams();
		}

		return $default + $this -> options;
	}


	/**
	 * Sets the method and url and executes a user closure function if given
	 * @param string $url
	 * @param object $closure
	 * @param string $method
	 * @return sFire\HTTP\Client 
	 */
	private function method($url, $closure, $method) {
			
		if(false === is_string($url)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($url)), E_USER_ERROR);
		}

		if(false === is_string($method)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($method)), E_USER_ERROR);
		}

		$this -> method = $method;
		$this -> url 	= new URLParser($url);
		$this -> options[CURLOPT_CUSTOMREQUEST] = strtoupper($method);

		if(is_object($closure)) {
			call_user_func($closure, $this);
		}

		$this -> send();

		return $this;
	}
}
?>
