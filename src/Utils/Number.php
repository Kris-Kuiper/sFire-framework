<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Utils;

final class Number {
	

	/**
	 * @var mixed $numbers
	 */
	private $number;


	/**
	 * Constructor
	 * @param mixed $number
	 * @return sFire\Number\Number
	 */
	public function __construct($number = null) {

		if(null !== $number) {
			$this -> set($number);
		}
	}


	/**
	 * Returns current number
	 * @return mixed
	 */
	public function __toString() {
		return $this -> get();
	}


	/**
	 * Returns current number
	 * @return mixed
	 */
	public function get() {
		return $this -> number;
	}


	/**
	 * Sets new number
	 * @param mixed $number
	 * @return sFire\Number\Number
	 */
	public function set($number) {

        $this -> number = $number;

		return $this;
	}


	/**
	 * Ceils all the numbers
	 * @return sFire\Number\Number
	 */
	public function ceil() {

		return $this -> convert(function($number) {
			return ceil($number);
		});
	}


	/**
	 * Floors all the numbers
	 * @return sFire\Number\Number
	 */
	public function floor() {

		return $this -> convert(function($number) {
			return floor($number);
		});
	}


	/**
	 * Round all the numbers with a precision
	 * @param int $decimals
	 * @return sFire\Number\Number
	 */
	public function round($decimals = 2) {

		if(false === ('-' . intval($decimals) == '-' . $decimals)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($decimals)), E_USER_ERROR);
		}

		return $this -> convert(function($number, $decimals) {
			return round($number, $decimals);
		}, [$decimals]);
	}


	/**
	 * Converts number into a string, keeping a specified number of decimal
	 * @param int $decimals
	 * @return sFire\Number\Number
	 */
	public function toFixed($decimals = 2) {

		if(false === ('-' . intval($decimals) == '-' . $decimals)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($decimals)), E_USER_ERROR);
		}

		return $this -> convert(function($number, $decimals) {
			
			$split = explode('.', (string) $number);

			if(count($split) < 2) {
				$split[1] = str_repeat('0', $decimals);
			}

			if(strlen($split[1]) < $decimals) {
				$split[1] .= str_repeat('0', $decimals - strlen($split[1]));
			}

			return rtrim(rtrim($split[0] . '.' . substr($split[1], 0, $decimals), ','), '.');

		}, [$decimals]);
	}


	/**
	 * Strips all numbers from string and returns these in an array
	 * @return array
	 */
	public function strip() {

		if(preg_match_all('#([0-9\.,\-]+)#', (string) $this -> number, $numbers)) {
			return $numbers[1];
		}

		return [];
	}


	/**
	 * Strips all the numbers from a string and returns the value with an optional index to retrieve
	 * @return mixed
	 */
	public function val($index = 0) {

		if(false === ('-' . intval($index) == '-' . $index)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($index)), E_USER_ERROR);
		}

		$val = $this -> strip();

		if(true === isset($val[$index])) {
			return $val[$index];
		}

		return null;
	}


	/**
	 * Converts all the numbers in a string to a specific format
	 * @param int $decimals
	 * @param string $point
	 * @param string $thousands_sep
	 * @param string $currency
	 * @return sFire\Number\Number
	 */
	public function format($decimals = 2, $point = '.', $thousands_sep = ',', $currency = null) {

		if(false === ('-' . intval($decimals) == '-' . $decimals)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($decimals)), E_USER_ERROR);
		}

		if(false === is_string($point)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($point)), E_USER_ERROR);
		}

		if(false === is_string($thousands_sep)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($thousands_sep)), E_USER_ERROR);
		}

		if(null !== $currency && false === is_string($currency)) {
			return trigger_error(sprintf('Argument 4 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($currency)), E_USER_ERROR);
		}

		return $this -> convert(function($number, $decimals, $point, $thousands_sep, $currency) {
			return ($currency ?: $currency) . number_format($number, $decimals, $point, $thousands_sep);
		}, [$decimals, $point, $thousands_sep, $currency]);
	}


	/**
	 * Executes a callable function and returns this instance
	 * @param object $callback
	 * @param array $variables
	 * @return sFire\Number\Number
	 */
	private function convert($callback, $variables = []) {

		$this -> number = preg_replace_callback('~[0-9\.,]+~', function($number) use ($callback, $variables) {
			return call_user_func_array($callback, array_merge([(float) str_replace(',', '', $number[0])], $variables));
		}, $this -> number);

		return $this;
	}
}
?>