<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Form\Traits;

use sFire\Escaper\Escape;

trait FormInputTrait {


	/**
     * Builds the HTML
     * @return string
     */
    private function build() {

        $this -> fill();

        $html = '<input type="'. $this -> type .'"';
        
        foreach($this -> attributes as $attribute => $value) {
            $html .= ' ' . $attribute . '="'. Escape :: attr($value) .'"';
        }

        $html .= '>';

        return $html;
    }
}