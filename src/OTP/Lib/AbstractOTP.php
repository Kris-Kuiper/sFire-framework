<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\OTP\Lib;

use sFire\Handler\NotImplementedHandler;

abstract class AbstractOTP {


    /**
     * Get the password for a specific timestamp value
     * @param integer $timestamp
     */
    public function timestamp($timestamp) {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
     * Get the password for the current timestamp value
     */
    public function now() {
        throw new NotImplementedHandler(__METHOD__);
    }


    /**
     * Get the password for a specific counter value
     * @param integer $count 
     */
    public function counter($count) {
        throw new NotImplementedHandler(__METHOD__);
    }
}