<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Form\Types;

use sFire\Form\Traits\FormTrait;
use sFire\HTTP\Request;
use sFire\Number\Number;

class Selectrange {

    use FormTrait;


    /**
     * @var float $min
     */
    private $min;


    /**
     * @var float $max
     */
    private $max;


    /**
     * @var float $steps
     */
    private $steps = 1;


    /**
     * @var int $round
     */
    private $round = 0;


    /**
     * Set the min. and max. number to iterate. The min will increase by the steps and can be formatted with the round parameter
     * @param float $min
     * @param float $max
     * @param float $steps
     * @param int $round
     * @return sFire\Form\Types\Selectrange
     */
    public function range($min, $max, $steps = 1, $round = 0) {

        if(false === is_numeric($min)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type float or integer, "%s" given', __METHOD__, gettype($min)), E_USER_ERROR);
        }

        if(false === is_numeric($max)) {
            return trigger_error(sprintf('Argument 2 passed to %s() must be of the type float or integer, "%s" given', __METHOD__, gettype($max)), E_USER_ERROR);
        }

        if(false === is_numeric($steps)) {
            return trigger_error(sprintf('Argument 3 passed to %s() must be of the type float or integer, "%s" given', __METHOD__, gettype($steps)), E_USER_ERROR);
        }

        if(false === ('-' . intval($round) == '-' . $round)) {
            return trigger_error(sprintf('Argument 4 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($round)), E_USER_ERROR);
        }

        $this -> min    = $min;
        $this -> max    = $max;
        $this -> steps  = $steps;
        $this -> round  = $round;

        return $this;
    }


    /**
     * Builds the HTML
     * @return string
     */
    private function build() {

        //Get value
        $selected = null;

        if(true === $this -> filled) {

            if(true === isset($this -> attributes['name'])) {

                if(true === Request :: isPost()) {
                    $selected = Request :: fromPost($this -> attributes['name']);
                }
            }
        }

        //Build select
        $html = '<select';
        
        foreach($this -> attributes as $attribute => $value) {

            if('value' !== $attribute) {
                $html .= ' ' . $attribute . '="'. @htmlentities($value) .'"';
            }
        }

        $html .= '>';

        $number = new Number();

        //Build options
        $maxsteps = (max($this -> min, $this -> max) - min($this -> min, $this -> max)) / $this -> steps;

        for($i = 0; $i <= $maxsteps; $i++) {

            //-1 to -2 && 1 to -1
            if($this -> max < 0 && $this -> min > $this -> max) {
                $key = $this -> min - $this -> steps * $i;
            }
            //-1 to 1 && -2 to -1
            elseif($this -> min < 0) {
                $key = $this -> min + $this -> steps * $i;
            }
            //0 to 1
            elseif($this -> min <= $this -> max) {
                $key = $this -> steps * $i;
            }
            //1 to 0
            else {
                $key = $this -> min - $this -> steps * $i;
            }

            $number -> set($key) -> toFixed($this -> round);

            $html .= '<option value="'. @htmlentities($key) .'"';

            if((string) $key == $selected) {
                $html .= ' selected="selected"';
            }

            $html .= '>'. @htmlentities($number -> get()) .'</option>';              
        }
        
        $html .= '</select>';

        return $html;
    }
}
?>