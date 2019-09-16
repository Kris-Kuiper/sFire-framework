<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Form\Traits;

use sFire\HTTP\Request;
use sFire\Validator\Form\Message as MessageForm;
use sFire\Validator\File\Message as MessageFile;
use sFire\Utils\StringToArray;

trait FormTrait {


	/**
     * @var array $attributes
     */
    private $attributes = [];


    /**
     * @var string $value
     */
    private $value;


    /**
     * @var string $type
     */
    private $type;


    /**
     * @var string $name
     */
    private $name;


    /**
     * @var array $onerror
     */
    private $onerror;


    /**
     * @var boolean $filled
     */
    private $filled = true;


    /**
     * Contructor
     */
    public function __construct($type, $name = null, $value = null) {

        $this -> type = $type;
        
        if(null !== $name) {
            $this -> name($name);
        }

        if(null !== $value) {
            $this -> value($value);
        }
    }


    /**
     * Magic method to convert this object to a string (HTML)
     * @return string
     */
    public function __toString() {
        return $this -> build();
    }


    /**
     * Set a new value for input
     * @param string $value
     * @return sFire\Form\Traits\FormTrait
     */
    public function value($value = null) {

        if(null !== $value && false === is_string($value) && false === is_numeric($value)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or number, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
        }

        $this -> value = (string) $value;
        $this -> attributes['value'] = (string) $value;

        return $this;
    }


    /**
     * Set a new name for input
     * @param string $name
     * @return sFire\Form\Traits\FormTrait
     */
    public function name($name = null) {

        if(null !== $name && false === is_string($name) && false === is_numeric($name)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or number, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
        }

        $this -> name = $name;
        $this -> attributes['name'] = $name;

        return $this;
    }


    /**
     * Set new attributes for input
     * @param array $attributes
     * @return sFire\Form\Traits\FormTrait
     */
    public function attributes($attributes) {
        
        if(false === is_array($attributes)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($attributes)), E_USER_ERROR);
        }

        $this -> attributes = array_merge($this -> attributes, $attributes);

        return $this;
    }


    /**
     * Set if input should be automaticly filled or not
     * @param boolean $filled
     * @return sFire\Form\Traits\FormTrait
     */
    public function filled($filled) {
        
        if(false === is_bool($filled)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type boolean, "%s" given', __METHOD__, gettype($filled)), E_USER_ERROR);
        }

        $this -> filled = $filled;

        return $this;
    }


    /**
     * Merges the given attributes with the already existing attributes on error
     * @param array $attributes
     * @return sFire\Form\Traits\FormTrait
     */
    public function onerror($attributes) {

        if(false === is_array($attributes)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($attributes)), E_USER_ERROR);
        }

        if(true === isset($this -> attributes['name'])) {

            $helper = new StringToArray();
            $data   = $helper -> execute($this -> attributes['name'], null, MessageFile :: getErrors(true));
            $data   = $helper -> execute($this -> attributes['name'], null, MessageForm :: getErrors(true));

            if(null !== $data) {
                $this -> attributes = array_merge($this -> attributes, $attributes);
            }
        }

        return $this;
    }


    /**
     * Sets the value if allowed
     */
    private function fill() {

        //Prefill value
        if(true === $this -> filled) {

            if(true === isset($this -> attributes['name'])) {

                if(true === Request :: isPost()) {
                    $this -> attributes['value'] = Request :: fromPost($this -> attributes['name']);
                }
            }
        }
    }
}