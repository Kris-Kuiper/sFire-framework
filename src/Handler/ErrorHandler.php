<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Handler;

use sFire\Entity\Error;
use sFire\Logger\Logger;
use sFire\Config\Path;
use sFire\HTTP\Request;

use DateTime;

class ErrorHandler {


	const PRODUCTION  = 'production';
	const DEVELOPMENT = 'development';


	/**
	 * @var sFire\Entity\Error $error
	 */
	private $error;


	/**
	 * @var sFire\Logger\Logger $logger
	 */
	private $logger;


	/**
	 * @var array $options
	 */
	private $options = [

				'write' 	=> true, 
				'display' 	=> true, 
				'mode' 		=> self :: DEVELOPMENT, 
				'directory' => null,
				'ip'		=> [],
				'types' 	=> ['date', 'ip', 'message', 'line', 'number', 'context', 'type', 'backtrace']
			];


	/**
     * Constructor
     */
	public function __construct() {
		set_error_handler([$this, 'handler']);
	}


	/**
     * Error handler
     * @param $number
     * @param string $message
     * @param string $file
     * @param string $line
     * @param array $context
     */
	public function handler($number, $message, $file, $line, $context) {

		if(0 === error_reporting()) { 
			return;
		}

		$this -> error = new Error();
		$this -> error -> setFile($file); 
		$this -> error -> setMessage($message); 
		$this -> error -> setNumber($number); 
		$this -> error -> setLine($line); 
		$this -> error -> setDate(new DateTime()); 
		$this -> error -> setContext($context); 
		$this -> error -> setBacktrace(debug_backtrace(null, 5));
		$this -> error -> setIp(Request :: getIp());

		switch($this -> error -> getNumber()) {

			case E_ERROR :
			case E_CORE_ERROR :
			case E_COMPILE_ERROR :
			case E_PARSE :
				$this -> error -> setType('FATAL');
			break;
			
			case E_USER_ERROR :
			case E_RECOVERABLE_ERROR :
				$this -> error -> setType('ERROR');
			break;
		
			case E_WARNING :
			case E_CORE_WARNING :
			case E_COMPILE_WARNING :
			case E_USER_WARNING :
				$this -> error -> setType('WARNING');
			break;
		
			case E_NOTICE :
			case E_USER_NOTICE :
				$this -> error -> setType('INFO');
			break;
			
			case E_STRICT :
				$this -> error -> setType('STRICT');
			break;
		}

		$this -> action();
	}


	/**
     * Set debug options
     * @param array $options
     */
	public function setOptions($options = []) {

		if(false === is_array($options)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($options)), E_USER_ERROR);
        }

		$this -> options = array_merge($this -> options, $options);
	}


	/**
     * Determines what to do with the error
     */
	private function action() {

		$options = (object) $this -> options;

		if(true === $options -> write) {
			$this -> writeToFile();	
		}

		if(true == $options -> display) {
			return $this -> displayError();
		}

		exit();
	}


	/**
	 * Writes current error to log file
	 */
	private function writeToFile() {

		$logger = $this -> getLogger();
		$logger -> setDirectory(Path :: get('log-error'));


		$error = $this -> error -> toJson($this -> options['types']);

		if(false === $error) {
			$this -> error -> setContext(null);
			$this -> error -> setBacktrace(null);
		}

		$error = $this -> error -> toJson($this -> options['types']);
		$logger -> write($this -> error -> toJson($this -> options['types']));
	}


	/**
	 * Prints the error to client
	 */
	private function displayError() {

		if(count($this -> options['ip']) === 0 || true === in_array(Request :: getIp(), $this -> options['ip'])) {

			$error = [

				'type'		=> $this -> error -> getType(),
				'text' 		=> $this -> error -> getMessage(),
				'file'		=> $this -> error -> getFile(),
				'line'		=> $this -> error -> getLine(),
				'backtrace' => $this -> formatBacktrace()
			];

			exit('<pre>' . print_r($error, true) . '</pre>');
		}
	}


	/**
	 * Formats the backtrace
	 * @return array
	 */
	private function formatBacktrace() {

		$backtrace = $this -> error -> getBacktrace();
		
		if(null !== $backtrace) {

			array_shift($backtrace);
			array_shift($backtrace);

			foreach($backtrace as $index => $stack) {

				foreach(['type', 'args'] as $type) {

					if(true === isset($backtrace[$index][$type])) {
						unset($backtrace[$index][$type]);
					}
				}
			}
		}

		return $backtrace;
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