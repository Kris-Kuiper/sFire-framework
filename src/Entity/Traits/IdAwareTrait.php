<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Entity\Traits;

trait IdAwareTrait {

	/**
	 * @var int $id
	 */
    public $id;


    /**
     * Sets id
     * @param int $id
     * @return IdAwareTrait
     */
    public function setId($id) {
        
        $this -> id = $id;
        return $this;
    }


    /**
     * Returns id
     * @return int
     */
    public function getId() {
        return $this -> id;
    }
}
?>