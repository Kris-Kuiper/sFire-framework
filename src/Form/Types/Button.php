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
use sFire\Escaper\Escape;

class Button {

    use FormTrait;


    /**
     * @var string $text
     */
    private $text;


    /**
     * Set the text of the button
     * @param string $text
     * @return sFire\Form\Types\Button
     */
    public function text($text) {

        if(false === is_string($text)) {
            return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($text)), E_USER_ERROR);
        }

    	$this -> text = $text;

    	return $this;
    }


    /**
     * Builds the HTML
     * @return string
     */
    private function build() {

    	if(null === $this -> text) {

    		$html = '<input type="'. $this -> type .'"';
    		
    		foreach($this -> attributes as $attribute => $value) {
    		    $html .= ' ' . $attribute . '="'. Escape :: attr($value) .'"';
    		}

    		$html .= '>';

    		return $html;
    	}

        $this -> fill();
        
        $html = '<button';
        
        foreach($this -> attributes as $attribute => $value) {
            $html .= ' ' . $attribute . '="'. Escape :: attr($value) .'"';
        }

        $html .= '>' . Escape :: html($this -> text) . '</button>';

        return $html;
        
    }
}
?>