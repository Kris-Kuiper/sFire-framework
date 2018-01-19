<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Session\Driver;

use sFire\Session\AbstractSession;

class Plain extends AbstractSession {

	/**
     * @var array $data
     */
    protected static $data = [];


	/**
	 * Constructor
	 */
	public function __construct() {

		parent :: __construct();
		static :: $data = array_merge(static :: $data, $_SESSION);
	}


	/**
	 * Destructor
	 */
	public function __destruct() {
		$_SESSION = static :: $data;
	}
}
?>