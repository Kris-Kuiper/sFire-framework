<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
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
		static :: add('module-config', 'config' . DIRECTORY_SEPARATOR . 'config.php');
	}
}
?>