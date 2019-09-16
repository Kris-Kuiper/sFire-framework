<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Translation;

use sFire\Config\Path;
use sFire\System\File;
use sFire\Routing\Router;
use sFire\MVC\Viewmodel;
use sFire\Application\Application;

class Translation {


	/**
	 * @var array $translations
	 */
	private static $translations = [];


	/**
	 * @var string
	 */
	private static $language;


	/**
	 * Translate text based on a key
	 * @param string $key
	 * @param array $params
	 * @param string $language
	 * @param sFire\MVC\Viewmodel $viewmodel
	 * @return string
	 */
	public static function translate($key, $params = [], $language = null, Viewmodel $viewmodel = null) {
		
		$identifier = null;

		if(null !== $viewmodel) {
			$identifier = static :: load($viewmodel);
		}

		if(null === $key) {
			$key = '';
		}

		//Check if key is a string
		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		//Check if params is an array
		if(null !== $params && false === is_array($params)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($params)), E_USER_ERROR);
		}

		//Check if language is a string
		if(null !== $language && false === is_string($language)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($language)), E_USER_ERROR);
		}

		//Set default language if non given
		if(null === $language) {
			$language = static :: $language;
		}

		//If identifier is known (usually with a viewmodel as last param)
		if(null !== $identifier) {

			if(true === isset(static :: $translations[$identifier], static :: $translations[$identifier][$language])) {

				if(true === isset(static :: $translations[$identifier][$language][$key])) {
					return vsprintf(static :: $translations[$identifier][$language][$key], $params);
				}
			}
		}

		//Check the full resource for the translation
		foreach(static :: $translations as $translation) {

			if(true === isset($translation[$language][$key])) {
				return vsprintf($translation[$language][$key], $params);
			}
		}

		//No translation found
		return vsprintf($key, $params);
	}


	/**
	 * Loads a new translation file
	 * @param string|sFire\MVC\Viewmodel $source
	 * @return string
	 */
	public static function load($source) {

		if(false === is_string($source) && false === $source instanceof Viewmodel) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or instance of sFire\MVC\Viewmodel, "%s" given', __METHOD__, gettype($source)), E_USER_ERROR);
		}

		$file = $source;

		if($source instanceof Viewmodel) {

			$path 			= Path :: get('modules') . Router :: getRoute() -> getModule() . DIRECTORY_SEPARATOR;
			$views 			= $path . Application :: get(['directory', 'view']);
			$translations 	= $path . Application :: get(['directory', 'translation']);
			$extension 		= Application :: get(['extensions', 'view']);
			$file  			= preg_replace('#^'. preg_quote($views) .'#', '', $source -> getFile());
			$file  			= preg_replace('#'. preg_quote($extension) .'$#', '', $file);
		}

		$module 	= Router :: getRoute() -> getModule();
		$extension 	= Application :: get(['extensions', 'translation']);
		$file 		= str_replace('.', DIRECTORY_SEPARATOR, $file);
		$file 		= Path :: get('modules') . $module . DIRECTORY_SEPARATOR . Application :: get(['directory', 'translation']) . $file . $extension;
		$file 		= new File($file);
		$identifier = md5($file -> entity() -> getBasepath());
		$cache 		= new File(Path :: get('cache-translation') . $identifier);

		if(false === $file -> exists() && false === $source instanceof Viewmodel) {
			return trigger_error(sprintf('Translation file "%s" used in %s() does not exists', $file -> entity() -> getBasepath(), __METHOD__), E_USER_ERROR);
		}

		static :: cache($cache, $file);

		return $identifier;
	}


	/**
	 * Sets the default language
	 * @param string $language
	 */
	public static function setLanguage($language) {

		if(false === is_string($language)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($language)), E_USER_ERROR);
		}

		self :: $language = $language;
	}


	/**
	 * Returns the default language
	 * @return string
	 */
	public static function getLanguage() {
		return static :: $language;
	}


	/**
	 * Returns cache if exists, otherwise the translation cache file is created, filled with content and returned
	 * @param sFire\System\File $cache
	 * @param sFire\System\File $file
	 */
	private static function cache(File $cache, File $file) {

		if(false === isset(static :: $translations[$cache -> entity() -> getName()])) {

			$parse = true;

			if(true === $cache -> exists()) {
				
				if($cache -> entity() -> getModificationTime() >= $file -> entity() -> getModificationTime()) {
					$parse = false;
				}
			}

			//Check if file needs to be parsed
			if(true === $parse) {

				$content = serialize(static :: parse($file));
				$cache -> create() -> flush() -> append($content);
			}

			static :: $translations[$cache -> entity() -> getName()] = unserialize($cache -> getContent());
		}
	}


	/**
	 * Parses a translation file and converts it into an array
	 * @param sFire\System\File $file
	 * @return array
	 */
	private static function parse(File $file) {

		$lines 		= explode("\n", $file -> getContent());
		$languages 	= [];

		foreach($lines as $index => $line) {
			
			$line = trim($line);

			if('' === $line) {
				continue;
			}

			//Check for language format
			preg_match('#^\[([a-z_\-0-9]+)\]#i', $line, $language);

			if(count($language) === 2) {
				
				$lg = $language[1];
				$languages[$lg] = [];

				continue;
			}

			//If there is no language found
			if(false === isset($lg)) {
				return trigger_error(sprintf('Missing language title in translation file "%s" on line %s', $file -> entity() -> getBasepath(), ++$index), E_USER_ERROR);
			}
			
			//Match the format (key value) on a single translation line
			preg_match('#^(\'|")(.*)(\1)\s+(\'|")(.*)(\3)$#', $line, $match);

			if(count($match) !== 7) {
				return trigger_error(sprintf('Incorrect format in translation file "%s" on line %s', $file -> entity() -> getBasepath(), ++$index), E_USER_ERROR);
			}

			$languages[$lg][$match[2]] = $match[5];
		}

		return $languages;
	}
}