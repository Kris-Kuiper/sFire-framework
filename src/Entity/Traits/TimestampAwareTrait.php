<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Entity\Traits;

trait TimestampAwareTrait {


    /**
     * @var \DateTime $created_at
     */
    public $created_at;


    /**
     * @var \DateTime $created_on
     */
    public $created_on;


    /**
     * @var \DateTime $updated_at
     */
    public $updated_at;


    /**
     * @var \DateTime $updated_on
     */
    public $updated_on;


    /**
     * @var \DateTime $edited_at
     */
    public $edited_at;


    /**
     * @var \DateTime $edited_on
     */
    public $edited_on;


    /**
     * @var \DateTime $deleted_at
     */
    public $deleted_at;


    /**
     * @var \DateTime $deleted_on
     */
    public $deleted_on;


    /**
     * @var \DateTime $activated_at
     */
    public $activated_at;


    /**
     * @var \DateTime $activated_on
     */
    public $activated_on;


    /**
     * @var \DateTime $completed_at
     */
    public $completed_at;


    /**
     * @var \DateTime $completed_on
     */
    public $completed_on;


    /**
     * Sets the created at
     * @param string $created_at
     * @return TimestampAwareTrait
     */
    public function setCreatedAt($created_at) {

        $this -> created_at = $created_at !== null ? new \DateTime($created_at) : null;
        return $this;
    }


    /**
     * Returns the created at
     * @return \DateTime
     */
    public function getCreatedAt() {
        return $this -> created_at;
    }


    /**
     * Sets the created on
     * @param string $created_on
     * @return TimestampAwareTrait
     */
    public function setCreatedOn($created_on) {
        
        $this -> created_on = $created_on !== null ? new \DateTime($created_on) : null;
        return $this;
    }

    /**
     * Returns the created on
     * @return \DateTime
     */
    public function getCreatedOn() {
        return $this -> created_on;
    }


    /**
     * Sets the updated at
     * @param string $updated_at
     * @return TimestampAwareTrait
     */
    public function setUpdatedAt($updated_at) {
        
        $this -> updated_at = $updated_at !== null ? new \DateTime($updated_at) : null;
        return $this;
    }

    /**
     * Returns the updated at
     * @return \DateTime
     */
    public function getUpdatedAt() {
        return $this -> updated_at;
    }


    /**
     * Sets the updated on
     * @param string $updated_on
     * @return TimestampAwareTrait
     */
    public function setUpdatedOn($updated_on) {
        
        $this -> updated_on = $updated_on !== null ? new \DateTime($updated_on) : null;
        return $this;
    }

    /**
     * Returns the updated on
     * @return \DateTime
     */
    public function getUpdatedOn() {
        return $this -> updated_on;
    }


    /**
     * Sets the edited at
     * @param string $edited_at
     * @return TimestampAwareTrait
     */
    public function setEditedAt($edited_at) {
        
        $this -> edited_at = $edited_at !== null ? new \DateTime($edited_at) : null;
        return $this;
    }

    /**
     * Returns the edited at
     * @return \DateTime
     */
    public function getEditedAt() {
        return $this -> edited_at;
    }


    /**
     * Sets the edited on
     * @param string $edited_on
     * @return TimestampAwareTrait
     */
    public function setEditedOn($edited_on) {
        
        $this -> edited_on = $edited_on !== null ? new \DateTime($edited_on) : null;
        return $this;
    }

    /**
     * Returns the edited on
     * @return \DateTime
     */
    public function getEditedOn() {
        return $this -> edited_on;
    }


    /**
     * Sets the deleted at
     * @param string $deleted_at
     * @return TimestampAwareTrait
     */
    public function setDeletedAt($deleted_at) {
        
        $this -> deleted_at = $deleted_at !== null ? new \DateTime($deleted_at) : null;
        return $this;
    }

    /**
     * Returns the deleted at
     * @return \DateTime
     */
    public function getDeletedAt() {
        return $this -> deleted_at;
    }


    /**
     * Sets the deleted on
     * @param string $deleted_on
     * @return TimestampAwareTrait
     */
    public function setDeletedOn($deleted_on) {
        
        $this -> deleted_on = $deleted_on !== null ? new \DateTime($deleted_on) : null;
        return $this;
    }

    /**
     * Returns the deleted on
     * @return \DateTime
     */
    public function getDeletedOn() {
        return $this -> deleted_on;
    }


    /**
     * Sets the activated_at
     * @param string $activated at
     * @return TimestampAwareTrait
     */
    public function setActivatedAt($activated_at) {
        
        $this -> activated_at = $activated_at !== null ? new \DateTime($activated_at) : null;
        return $this;
    }

    /**
     * Returns the activated_at
     * @return \DateTime
     */
    public function getActivatedAt() {
        return $this -> activated_at;
    }


    /**
     * Sets the activated on
     * @param string $activated_on
     * @return TimestampAwareTrait
     */
    public function setActivatedOn($activated_on) {
        
        $this -> activated_on = $activated_on !== null ? new \DateTime($activated_on) : null;
        return $this;
    }

    /**
     * Returns the activated on
     * @return \DateTime
     */
    public function getActivatedOn() {
        return $this -> activated_on;
    }


    /**
     * Sets the completed at
     * @param string $completed_at
     * @return TimestampAwareTrait
     */
    public function setCompletedAt($completed_at) {
        
        $this -> completed_at = $completed_at !== null ? new \DateTime($completed_at) : null;
        return $this;
    }

    /**
     * Returns the completed at
     * @return \DateTime
     */
    public function getCompletedAt() {
        return $this -> completed_at;
    }


    /**
     * Sets the completed on
     * @param string $completed_on
     * @return TimestampAwareTrait
     */
    public function setCompletedOn($completed_on) {
        
        $this -> completed_on = $completed_on !== null ? new \DateTime($completed_on) : null;
        return $this;
    }

    /**
     * Returns the completed on
     * @return \DateTime
     */
    public function getCompletedOn() {
        return $this -> completed_on;
    }
}
?>