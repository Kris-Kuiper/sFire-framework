<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
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