<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Debugging;

final class Debug {
	

	/**
	 * @var array $time
	 */
	private static $time = [];


	/**
	 * Advanced debugging of variables
	 * @param mixed $data 
	 * @param boolean $export
	 * @return mixed
	 */
	public static function dump($data = null, $export = false) {

		if(false === is_bool($export)) {
 			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($export)), E_USER_ERROR);
		}

		if($data === null || (true === is_string($data) && trim($data) === '') || true === is_bool($data)) {

			if($export) {
				return var_export($data);
			}

			echo '<pre>' . var_dump($data) . '</pre>';

			return;
		}
		
		if($export) {
			return print_r($data, true);
		}

		echo '<pre>' . print_r($data, true) . '</pre>';
	}


	/**
	 * Calculate process times by adding new times an exporting them
	 * @param string $key
	 * @return array
	 */
	public static function time($key = null) {

		if(null !== $key && false === is_string($key)) {
 			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(count(static :: $time) === 0) {
			static :: $time = [];
		}

		if(null !== $key) {
			
			static :: $time[] = ['time' => microtime(), 'key' => $key];
			return;
		}

		static :: $time[] = ['time' => microtime(), 'key' => 'end'];
		
		$laps = [];

		for($i = 1; $i < count(static :: $time); $i++) {

			$start 	= explode(' ', static :: $time[$i - 1]['time']);
			$end  	= explode(' ', static :: $time[$i]['time']);

			$laps[static :: $time[$i - 1]['key'] . ' - ' . static :: $time[$i]['key']] = number_format((($end[1] + $end[0]) - ($start[1] + $start[0])), 4, '.', '');
		}

		return $laps;
	}
}
?>