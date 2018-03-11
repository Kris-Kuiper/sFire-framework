<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Session\Driver;

use sFire\Container\Container;
use sFire\Config\Path;
use sFire\Hash\Token;
use sFire\Hash\HashTrait;

class Encrypted extends Container {

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

		if(false === isset($_SESSION)) {

            session_save_path(Path :: get('session'));
            
            $session_id = '';

            if('1' === ini_get('session.use_cookies') && true === isset($_COOKIE[session_name()])) {
                $session_id = $_COOKIE[session_name()];
            }
            elseif('1' !== ini_get('session.use_only_cookies') && true === isset($_GET[session_name()])) {
                $session_id = $_GET[session_name()];
            }
            
            if(0 === preg_match('/^[a-zA-Z0-9\-]{32}$/', $session_id)) {
                session_id(Token :: create(32, true, true, true));
            }

            session_start();
        }

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
     * Regenerates session id
     */
    public static function regenerate() {
        session_regenerate_id();
    }


    /**
     * Returns the session id
     * @return string
    */
    public static function getSessionId() {
        return session_id();
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