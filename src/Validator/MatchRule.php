<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Validator;

use sFire\Validator\Store;

trait MatchRule {


	/**
     * @var string $field
     */
    private $field;


    /**
     * @var string $prefix
     */
    private $prefix;


    /**
     * @var array $parameters
     */
    private $parameters;


    /**
     * @var string $message
     */
    private $message;


    /**
     * @var array $file
     */
    private $file;
    
    
    /**
     * @var string|array $value
     */
    private $value;


    /**
     * @var array $values
     */
    private $values;


    /**
     * @var boolean $validateAsArray
     */
    private $validateAsArray = false;


    /**
     * Returns value
     * @return array
     */
    public function getValue() {
        return $this -> value;
    }


    /**
     * Returns values
     * @return array
     */
    public function getValues() {
        return $this -> values;
    }


    /**
     * Sets value
     * @param string|array $value
     */
    public function setValue($value) {
        $this -> value = $value;
    }


    /**
     * Sets values
     * @param string $fieldname
     * @param string|array $value
     */
    public function setValues($fieldname, $value) {
        $this -> values[$fieldname] = $value;
    }

    
    /**
     * Returns the file
     * @return array
     */
    public function getFile() {
        return $this -> file;
    }
    
    
    /**
     * Sets the file
     * @param array $file
     */
    public function setFile($file) {
        $this -> file = $file;
    }


    /**
     * Returns field
     * @return string
     */
    public function getField() {
        return $this -> field;
    }


    /**
     * Returns prefix
     * @return string
     */
    public function getPrefix() {
        return $this -> prefix;
    }


    /**
     * Returns parameters
     * @return array
     */
    public function getParameters() {
        return $this -> parameters;
    }


    /**
     * Returns message
     * @return string
     */
    public function getMessage() {
        return $this -> message;
    }


    /**
     * Sets validateAsArray
     * @return boolean
     */
    public function getValidateAsArray() {
        return $this -> validateAsArray;
    }


    /**
     * Sets field
     * @param array $field
     */
    public function setField($field) {
        $this -> field = $field;
    }


    /**
     * Sets prefix
     * @param string $prefix
     */
    public function setPrefix($prefix) {
        $this -> prefix = $prefix;
    }



    /**
     * Sets parameters
     * @param array $parameters
     */
    public function setParameters($parameters) {
        $this -> parameters = $parameters;
    }


    /**
     * Sets message
     * @param string $message
     */
    public function setMessage($message) {
        $this -> message = $message;
    }


    /**
     * Sets validateAsArray
     * @param boolean $bool
     */
    public function setValidateAsArray($bool) {
        $this -> validateAsArray = $bool;
    }


    /**
     * Retrieves data based on a string key previously stored via the store function
     * @param string $key
     * @return mixed
     */
    protected function retrieve($key) {
        return Store :: get($key);
    }
}