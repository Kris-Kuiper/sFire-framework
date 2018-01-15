<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Template;

use sFire\MVC\ViewModel;
use sFire\System\File;
use sFire\System\Directory;
use sFire\Template\TemplateData;

class Template {

	const NAMESPACE_MATCH = 'sFire\\Template\\Match\\';

	/**
	 * @var sFire\MVC\ViewMode $viewmodel 
	 */
	private $viewmodel;


	/**
	 * @var string $filename 
	 */
	private $filename;


	/**
	 * @var sFire\System\File $file 
	 */
	private $file;


	/**
	 * @var string $directory
	 */
	private $directory;


	/**
	 * @var string $code
	 */
	private $code;


	/**
	 * Constructor
	 * @param sFire\MVC\ViewModel
	 */
	public function __construct(ViewModel $viewmodel) {
		$this -> viewmodel = $viewmodel;
	}


	/**
	 * Set the cache directory path
	 * @param string $directory
	 */
	public function setDirectory($directory) {
		$this -> directory = $directory;
	}


	/**
	 * Get the cache directory path
	 * @return string
	 */
	public function getDirectory() {
		return $this -> directory;
	}


	/**
	 * Returns the generated code
	 * @return string
	 */
	public function getCode() {
		return $this -> code;
	}


	/**
	 * Renders the view model
	 * @return sFire\Template\Template
	 */
	public function render() {

		$parse = true;

		if($this -> getFile() -> exists()) {

			if($this -> getFile() -> entity() -> getModificationTime() >= @filemtime($this -> viewmodel -> getFile())) {
				$parse = false;
			}
		}

		if(true === $parse) {

			$this -> parse();
			$this -> write();
		}

		return $this;
	}


	/**
	 * Generates filename and returns it
	 * @return string
	 */
	public function getFileName() {

		if(null === $this -> filename && null !== $this -> viewmodel) {
			$this -> filename = md5($this -> viewmodel -> getFile());
		}

		return $this -> filename;
	}

	
	/**
	 * Create file object from current directory and file and returns it
	 * @return sFire\System\File
	 */
	public function getFile() {

		if(null === $this -> file) {

			$file = $this -> getFileName();
			$this -> file = new File($this -> directory . $file);
		}

		return $this -> file;
	}


	/**
	 * Convert template file to string
	 * @return string
	 */
	private function parse() {

		$lines = file($this -> viewmodel -> getFile());

		$this -> code = '';

		$classes = [

			'MatchEcho',
			'MatchForeach',
			'MatchEndForeach',
			'MatchFor',
			'MatchEndFor',
			'MatchIf',
			'MatchElseIf',
			'MatchElse',
			'MatchEndIf',
			'MatchForm',
			'MatchTranslation',
			'MatchRouter',
			'MatchHelper',
			'MatchPartial',
			'MatchFails',
			'MatchPasses',
			'MatchPHPTags'
		];

		foreach($lines as $linenumber => $line) {
			
			foreach($classes as $class) {
				
				$class = self :: NAMESPACE_MATCH . $class;
				$match = new $class($line);
				$line  = $match -> replace() -> getLine();
			}

			//User defined functions
			foreach(TemplateData :: getTemplateFunctions() as $action => $closure) {

				$class = self :: NAMESPACE_MATCH . 'MatchUserDefined';
				$match = new $class($line, $action);
				$line  = $match -> replace() -> getLine();
			}
			
			$this -> code .= trim($line) . "\n";
		}

		$this -> code = trim($this -> code);

		return $this -> code;
	}


	/**
	 * Write data to file
	 * @return sFire\Template\Template
	 */
	private function write() {

		if(null === $this -> directory) {
			return trigger_error(sprintf('Directory used in %s() can not be NULL', __METHOD__), E_USER_ERROR);
		}

		if(false === is_string($this -> code)) {
			return trigger_error(sprintf('Code used in %s() must be of the type string, "%s" given', __METHOD__, gettype($this -> code)), E_USER_ERROR);
		}

		$file = $this -> getFileName();
		$file = new File($this -> directory . $file);

		//Create file if not exists
		if(false === $file -> exists()) {
			$file -> create();
		}

		//Append data to file after flushing it
		$file -> flush() -> append($this -> code);

		return $this;
	}
}
?>