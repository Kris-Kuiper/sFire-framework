<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Config;

use sFire\Container\Container;
use sFire\Config\Path;

final class Files extends Container {


	/**
	 * @var array $data 
	 */
	protected static $data = [];


	/**
	 * Constructor
	 */
	public function __construct() {

		static :: add('routes', Path :: get('config') . 'routes.php');
		static :: add('boot', 'config' . DIRECTORY_SEPARATOR . 'boot.php');
		static :: add('module-config', 'config' . DIRECTORY_SEPARATOR . 'config.php');
	}
}