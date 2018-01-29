<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Session\Driver;

use sFire\Session\AbstractSession;
use sFire\Hash\HashTrait;

class Encrypted extends AbstractSession {

    use HashTrait; 


    const METHOD = 'AES-256-CBC';


    const ENCODING  = '8bit';

    
    const ALGORITHM = 'SHA256';


    /**
     * @var array $data
     */
    protected static $data = [];


	/**
     * @var string $key
     */
    private static $key;


    /**
     * Constructor
     */
	public function __construct() {

		parent :: __construct();

		static :: $key = static :: getKey(session_name());

		if(true === isset($_SESSION[static :: $key])) {
			static :: $data = array_merge(static :: $data, unserialize(static :: decrypt($_SESSION[static :: $key], static :: $key)));
		}
    }


    /**
     * Destructor
     */
    public function __destruct() {
    	$_SESSION[static :: $key] = static :: encrypt(serialize(static :: $data), static :: $key);
    }


    /**
     * Get the encryption and authentication keys from cookie
     * @param string $name
     * @return string
     */
    protected function getKey($name) {

        if(false === isset($_COOKIE[$name]) || '' === $_COOKIE[$name]) {

            $key 		 = static :: random_bytes(64); //32 for encryption and 32 for authentication
            $cookieParam = session_get_cookie_params();

            setcookie($name, base64_encode($key), ($cookieParam['lifetime'] > 0) ? time() + $cookieParam['lifetime'] : 0, $cookieParam['path'], $cookieParam['domain'], $cookieParam['secure'], $cookieParam['httponly']);
        }
        else {
            $key = base64_decode($_COOKIE[$name]);
        }

        return $key;
    }
}
?>