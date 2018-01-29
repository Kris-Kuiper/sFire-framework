<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Config;

use sFire\Container\Container;

final class Path extends Container {


	/**
	 * @var array $data 
	 */
	protected static $data = [];


	/**
	 * Constructor
	 */
	public function __construct() {

		static :: add('root', dirname(dirname(dirname(dirname(dirname(dirname(__FILE__)))))) . DIRECTORY_SEPARATOR);
		
		static :: add('public', 			static :: get('root') . 	'public' . 		DIRECTORY_SEPARATOR);
		static :: add('config', 			static :: get('root') . 	'config' . 		DIRECTORY_SEPARATOR);
		static :: add('data', 				static :: get('root') . 	'data' . 		DIRECTORY_SEPARATOR);
		static :: add('schedule', 			static :: get('root') . 	'schedules' . 	DIRECTORY_SEPARATOR);
		static :: add('modules', 			static :: get('root') . 	'modules' . 	DIRECTORY_SEPARATOR);
		static :: add('vendor', 			static :: get('root') . 	'vendor' . 		DIRECTORY_SEPARATOR);
		static :: add('cache', 				static :: get('data') . 	'cache' . 		DIRECTORY_SEPARATOR);
		static :: add('log', 				static :: get('data') . 	'log' . 		DIRECTORY_SEPARATOR);
		static :: add('session', 			static :: get('data') . 	'session' . 	DIRECTORY_SEPARATOR);
		static :: add('ssl', 				static :: get('data') . 	'ssl' . 		DIRECTORY_SEPARATOR);
		static :: add('upload', 			static :: get('data') . 	'upload' . 		DIRECTORY_SEPARATOR);
		static :: add('cache-shared', 		static :: get('cache') . 	'shared' . 		DIRECTORY_SEPARATOR);
		static :: add('cache-template', 	static :: get('cache') . 	'template' . 	DIRECTORY_SEPARATOR);
		static :: add('cache-translation', 	static :: get('cache') . 	'translation' . DIRECTORY_SEPARATOR);
		static :: add('log-access', 		static :: get('log') .		'access' . 		DIRECTORY_SEPARATOR);
		static :: add('log-error', 			static :: get('log') . 		'error' . 		DIRECTORY_SEPARATOR);
		static :: add('log-schedule', 		static :: get('log') . 		'schedule' . 	DIRECTORY_SEPARATOR);
	}
}
?>