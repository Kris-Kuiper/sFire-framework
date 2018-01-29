<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Form\Types;

use sFire\Validator\Form\Validator;
use sFire\Form\Form;
use sFire\Session\Driver\Plain as Session;
use sFire\Hash\Token as TokenHash;
use sFire\Application\Application;

class Token {

    const TOKEN_AMOUNT = 25;


    /**
     * Generates 
     * @return string
     */
    public function __toString() {

        $token = $this -> generate();
        $form  = new Form();

        return $form -> hidden('_token-name', $token -> name) -> filled(false) . $form -> hidden('_token-value', $token -> value) -> filled(false);
    }


    /**
     * Generates a new token and saves it into the session
     * @return object
     */
    public function generate() {
        
        $session = new Session();

        if(false === $session -> has('_token')) {
            $session -> add('_token', []);
        }

        $name  = TokenHash :: create(20, true, true, true);
        $value = TokenHash :: create(40, true, true, true);

        $session -> add('_token', [$name => $value]);
        $amount = count($session -> get('_token'));
        $limit  = Application :: get(['token', 'amount'], self :: TOKEN_AMOUNT);

        if($amount >= $limit) {
            $session -> add('_token', array_slice($session -> pull('_token'), $amount - $limit, null, true));
        }

        return (object) ['name' => $name, 'value' => $value];
    }
}
?>