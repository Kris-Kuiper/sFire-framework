<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Validator\Form;


class Combine {


	/**
	 * @var array $values
	 */
	private $values = [];


	/**
	 * @var string $glue
	 */
	private $glue;


	/**
	 * @var string $format
	 */
	private $format;


	/**
	 * @var string $name
	 */
	private $name;


	/**
	 * @var array $fieldnames
	 */
	private $fieldnames;

	
    /**
	 * Add one or multiple fields to combine them as one
	 * @param string|array $fieldnames
	 * @return sFire\Validator\Combine
	 */
	public function setValues($values) {

		if(false === is_array($values)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($values)), E_USER_ERROR);
		}

		$this -> values = $values;

		return $this;
	}

	/**
	 * Sets the fieldnames
	 * @param array $fieldnames
	 * @return sFire\Validator\Combine
	 */
	public function setFieldnames($fieldnames) {

		if(false === is_array($fieldnames)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($fieldnames)), E_USER_ERROR);
		}

		$this -> fieldnames = $fieldnames;
	}


	/**
	 * Joins the fieldnames with the glue string between each fieldname
	 * @param string $glue
	 * @return sFire\Validator\Combine
	 */
	public function glue($glue) {

		if(false === is_string($glue)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($glue)), E_USER_ERROR);
		}

		$this -> glue = $glue;

		return $this;
	}


	/**
	 * Converts the fieldname values to the specific given format
	 * @param string $format
	 * @return sFire\Validator\Combine
	 */
	public function format($format) {

		if(false === is_string($format)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($format)), E_USER_ERROR);
		}

		$this -> format = $format;

		return $this;
	}


	/**
	 * Gives the combined fieldnames a new single name
	 * @param string $name
	 * @return sFire\Validator\Combine
	 */
	public function name($name) {

		if(false === is_string($name)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		$this -> name = $name;

		return $this;
	}


	/**
	 * Returns the name
	 * @return string
	 */
	public function getName() {
		return $this -> name;
	}


	/**
	 * Returns the glue
	 * @return string
	 */
	public function getGlue() {
		return $this -> glue;
	}


	/**
	 * Returns the values
	 * @return array
	 */
	public function getValues() {
		return $this -> values;
	}


	/**
	 * Returns the fieldnames
	 * @return array
	 */
	public function getFieldnames() {
		return $this -> fieldnames;
	}


	/**
	 * Combine the values with the glue or format
	 * @return string
	 */
	public function combine() {

		if(count($this -> values) > 0) {

			if(null !== $this -> glue) {
				return implode($this -> glue, $this -> values);
			}

			return vsprintf($this -> format, $this -> values);
		}
	}
}