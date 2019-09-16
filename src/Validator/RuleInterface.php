<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Validator;

interface RuleInterface {


	/**
	 * Constructor
	 */
	public function __construct();
	

	/**
     * Returns field
     * @return string
     */
	public function getField();


	/**
     * Returns parameters
     * @return array
     */
	public function getParameters();


	/**
     * Returns message
     * @return string
     */
	public function getMessage();


	/**
     * Sets field
     * @param array $field
     */
	public function setField($field);


	 /**
     * Sets parameters
     * @param array $parameters
     */
	public function setParameters($parameters);


	/**
     * Sets message
     * @param string $message
     */
	public function setMessage($message);


	/**
	 * Check if rule passes
	 * @return boolean
	 */
	public function isValid();
}