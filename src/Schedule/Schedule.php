<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Schedule;

use sFire\Config\Path;
use sFire\System\File;
use sFire\Logger\Logger;
use sFire\Utils\NameConvert;
use sFire\Application\Application;

class Schedule {
	

	/**
	 * @var string $path
	 */
	private $path = 'php';


	/**
	 * @var sFire\Logger\Logger $logger
	 */
	private $logger;


	/**
	 * @param Constructor
	 */
	public function __construct() {
		
		if(false === isset($_SERVER['SCRIPT_FILENAME'])) {
			return trigger_error('Script filename could not be determined', E_USER_ERROR);
		}

		if(true === isset($_SERVER['_'])) {
			$this -> path = $_SERVER['_'];
		}

		$this -> bootstrap();
	}

	
	/**
	 * Iterate over all schedule files and execute tasks
	 */
	public function run() {
		
		//Check if the function exec is enabled
		if(false === function_exists('exec')) {
		    return trigger_error('Function "exec" is not enabled', E_USER_ERROR);
		}

		//Check if schedules folder is readable
		if(false === is_readable(Path :: get('schedule'))) {
			return trigger_error(printf('Could not read schedule folder "%s"', Path :: get('schedule')));
		}

		//Check if schedule cache folder is writable
		if(false === is_writable(Path :: get('log-schedule'))) {
			return trigger_error(printf('Could not write schedule cache folder "%s"', Path :: get('log-schedules')));
		}

		//Get all schedules from schedules folder
		$schedules = glob(Path :: get('schedule') . Application :: get(['prefix', 'schedule']) . '*.php');

		//For all the schedules classes; run the script
		foreach($schedules as $schedule) {

			//Remove path
			$schedule = preg_replace('#^'. Path :: get('schedule')  .'#', '', $schedule);
			
			//Remove prefix
			$schedule = preg_replace('#^'. Application :: get(['prefix', 'schedule'])  .'#', '', $schedule);

			//Remove extension
			$schedule = preg_replace('#.php$#', '', $schedule);

			//Execute schedule
			$this -> exec(sprintf('%s %s %s', $this -> path, getcwd() . DIRECTORY_SEPARATOR . $_SERVER['SCRIPT_FILENAME'], $schedule), true);
		}
	}


	/**
	 * Execute individual task of schedule file
	 * @param 
	 * @return 
	 */
	public function task($script) {

		if(false === is_string($script)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($script)), E_USER_ERROR);
		}

		$script = sprintf('%s%s%s.php', Path :: get('schedule'), Application :: get(['prefix', 'schedule']), NameConvert :: toCamelcase($script, true));

		//Check if script is readable
		if(false === is_readable($script)) {
			return trigger_error(sprintf('Script "%s" is not a readable Schedule file', (string) $script));
		}

		//Include script
		require_once($script);

		//Create new instance
		$file 		= new File($script);
		$classname 	= $file -> entity() -> getName();
		$class 		= new $classname();
			
		//Check if needed constants are defined
		foreach(['TIME', 'ENABLED'] as $constant) {
			
			if(false === defined($classname . '::' . $constant)) {
				return trigger_error(sprintf('Missing constant "%s" in Schedule "%s" class', $constant, $classname));
			}
		}

		//Check if schedule has start method
		if(false === is_callable([$class, 'start'])) {
			return trigger_error(sprintf('Missing methode start in Schedule "%s" class', $classname));
		}

		//If the current schedule is enabled
		if(true === $class :: ENABLED) {

			$run = $this -> checkRun($class :: TIME);

			if(true === $run) {

				ob_start();

				//Capture return ouput 
				$data 						= [];
				$data[$classname]['start']  = date('Y-m-d H:i:s');
				$data[$classname]['return'] = (string) $class -> start();
				$data[$classname]['echo'] 	= ob_get_clean();
				$data[$classname]['end']  	= date('Y-m-d H:i:s');

				//Check if the current schedule has the complete method
				if(true === is_callable([$class, 'complete'])) {
					$class -> complete($data[$classname]);
				}

				//Write to log
				$logger = $this -> getLogger();
				$logger -> setDirectory(Path :: get('log-schedule'));
				$logger -> write(json_encode($data));
			}
		}
	}


	/**
	 * Runs system command 
	 * @param string $command
	 * @param boolean $background
	 * @return string
	 */
	private function exec($command, $background = false) {

		exec(escapeshellcmd($command) . ($background === true ?  ' > /dev/null &' : ''), $output);

		return $output;
	}


	/**
	 * Checks if script should run
	 * @param string $crontab
	 * @return boolean
	 */
	private function checkRun($crontab) {

	    $time 	 = explode(' ', date('i G j n w'));
	    $crontab = explode(' ', $crontab);

	    foreach($crontab as $k => &$v) {

	        $time[$k] = preg_replace('/^0+(?=\d)/', '', $time[$k]);
	        
	        $v = explode(',', $v);

	        foreach ($v as &$v1) {
	            $v1 = preg_replace(['/^\*$/', '/^\d+$/', '/^(\d+)\-(\d+)$/', '/^\*\/(\d+)$/'], ['true', $time[$k] . '===\0', '(\1<=' . $time[$k] . ' and ' . $time[$k] . '<=\2)', $time[$k] . '%\1===0'], $v1);
	        }

	        $v = '(' . implode(' or ', $v) . ')';
	    }

	    $crontab = implode(' and ', $crontab);

	    return eval('return ' . $crontab . ';');
	}


	/**
	 * Load the bootstrap
	 */
	private function bootstrap() {

		include(dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . 'bootstrap.php');
		new \Bootstrap(false);
	}


	/**
	 * Initialise new sFire\Logger and returns it 
	 * @return sFire\Logger\Logger
	 */
	private function getLogger() {

		if(null === $this -> logger) {
			$this -> logger = new Logger();
		}

		return $this -> logger;
	}
}

//Create new Schedule
$schedule = new Schedule();

if(true === isset($argv)) {
	
	if(1 === count($argv)) {
		return $schedule -> run();
	}
	elseif(count($argv) > 1) {
		$schedule -> task($argv[1]);
	}
}
?>