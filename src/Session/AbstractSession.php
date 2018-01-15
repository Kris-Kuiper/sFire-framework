<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Session;

use sFire\Container\Container;
use sFire\Config\Path;
use sFire\Hash\Token;

class AbstractSession extends Container {


	/**
     * @var array $data
     */
	protected static $data = [];


	/**
	 * Constructor
	 */
	public function __construct() {

		session_save_path(Path :: get('session'));

		if(false === isset($_SESSION)) {
			
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