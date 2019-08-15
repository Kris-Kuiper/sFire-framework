<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Session\Driver;

use sFire\Container\Container;
use sFire\Config\Path;
use sFire\Hash\Token;

class Plain extends Container {

	/**
     * @var array $data
     */
    protected static $data = [];


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

		static :: $data = array_merge(static :: $data, $_SESSION);
	}


	/**
	 * Destructor
	 */
	public function __destruct() {
		$_SESSION = static :: $data;
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
}
?>