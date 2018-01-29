<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Form\Traits;

use sFire\HTTP\Request;

trait FormCheckedTrait {

	/**
     * Builds the HTML
     * @return string
     */
    private function build() {

        //Check checkbox if needed
        if(true === $this -> filled) {

            if(true === isset($this -> attributes['name'])) {

                if(true === Request :: isPost()) {

                    if(null !== Request :: fromPost($this -> attributes['name'])) {
                        $this -> attributes['checked'] = 'checked';
                    }
                }
            }
        }

        $html = '<input type="'. $this -> type .'"';
        
        foreach($this -> attributes as $attribute => $value) {
            $html .= ' ' . $attribute . '="'. @htmlentities($value) .'"';
        }

        $html .= '>';

        return $html;
    }
}
?>