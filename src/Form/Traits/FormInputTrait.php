<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */
 
namespace sFire\Form\Traits;

trait FormInputTrait {

	/**
     * Builds the HTML
     * @return string
     */
    private function build() {

        $this -> fill();

        $html = '<input type="'. $this -> type .'"';
        
        foreach($this -> attributes as $attribute => $value) {
            $html .= ' ' . $attribute . '="'. @htmlentities($value) .'"';
        }

        $html .= '>';

        return $html;
    }
}
?>