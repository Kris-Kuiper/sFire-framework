<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Form\Types;

use sFire\Form\Traits\FormTrait;
use sFire\Escaper\Escape;

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
                $html .= ' ' . $attribute . '="'. Escape :: attr($value) .'"';
            }
    	}

    	$html .= '>';

        if(true === isset($this -> attributes['value'])) {
            $html .= Escape :: attr($this -> attributes['value']);
        }

        $html .= '</textarea>';

        return $html;
    }
}
?>