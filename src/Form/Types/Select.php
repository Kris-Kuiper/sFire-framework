<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Form\Types;

use sFire\Form\Traits\FormTrait;
use sFire\HTTP\Request;

class Select {

    use FormTrait;


    /**
     * @var array $option
     */
    private $options = [];


    /**
     * 
     * @param array $options
     * @return sFire\Form\Types\Select
     */
    public function options($options = null) {

        if(null !== $options && false === is_array($options)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type array, "%s" given', __METHOD__, gettype($options)), E_USER_ERROR);
        }

        $this -> options = $options;

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

        //Build options
        foreach($this -> options as $key => $value) {

            if(true === is_array($value)) {

                $html .= '<optgroup label="'. @htmlentities($key) .'">';

                    foreach($value as $key => $value) {

                        $html .= '<option value="'. @htmlentities($key) .'"';

                            if($key === $selected) {
                                $html .= ' selected="selected"';
                            }

                        $html .= '>'. @htmlentities($value) .'</option>';
                    }

                $html .= '</optgroup>';
            }
            else {

                $html .= '<option value="'. @htmlentities($key) .'"';

                    if($key === $selected) {
                        $html .= ' selected="selected"';
                    }

                $html .= '>'. @htmlentities($value) .'</option>';
            }
        }
        
        $html .= '</select>';

        return $html;
    }
}
?>