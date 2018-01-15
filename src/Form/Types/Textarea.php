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

class Textarea {


    use FormTrait;


    /**
     * Builds the HTML
     * @return string
     */
    private function build() {

        $this -> fill();

        $html = '<textarea';
    	
    	foreach($this -> attributes as $attribute => $value) {

            if('value' !== $attribute) {
                $html .= ' ' . $attribute . '="'. @htmlentities($value) .'"';
            }
    	}

    	$html .= '>';

        if(true === isset($this -> attributes['value'])) {
            $html .= @htmlentities($this -> attributes['value']);
        }

        $html .= '</textarea>';

        return $html;
    }
}
?>