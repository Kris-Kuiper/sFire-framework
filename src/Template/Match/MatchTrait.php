<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Template\Match;

trait MatchTrait {

	/**
	 * @var string $line
	 */
    private $line;


    /**
     * @var boolean $inline
     */
    private $inline;


    /**
     * @var array $match
     */
    private $match;

    
    /**
     * @var string $viewIdentifier
     */
    private $viewIdentifier;


    /**
     * Returns match
     * @return array
     */
    public function getMatch() {
        return $this -> match;
    }


    /**
     * Returns line
     * @return string
     */
    public function getLine() {
        return $this -> line;
    }


    /**
     * Returns view identifier
     * @return string
     */
    public function getViewIdentifier() {
        return $this -> viewIdentifier;
    }


    /**
     * Returns inline
     * @return boolean
     */
    public function getInline() {
        return $this -> inline;
    }


    /**
     * Sets match
     * @param array $match
     */
    private function setMatch($match) {
        $this -> match = $match;
    }


    /**
     * Sets line
     * @param string $line
     */
    private function setLine($line) {
        $this -> line = $line;
    }


    /**
     * Sets view identifier
     * @param string $viewIdentifier
     */
    private function setViewIdentifier($viewIdentifier) {
        $this -> viewIdentifier = $viewIdentifier;
    }

    /**
     * Sets inline
     * @param boolean $inline
     */
    private function setInline($inline) {
        $this -> inline = $inline;
    }
}
?>