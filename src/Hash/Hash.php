<?php
/**
 * sFire Framework
 *
 * @link      http://github.com/Kris-Kuiper/sFire-Framework
 * @copyright Copyright (c) 2014-2018 sFire Framework. (https://www.sfire.nl)
 * @license   http://sfire.nl/license GNU AFFERO GENERAL PUBLIC LICENSE
 */

namespace sFire\Hash;

use sFire\Hash\HashTrait;

final class Hash {

	const METHOD   			= 'AES-256-CBC';
    const ENCODING  		= '8bit';
    const ALGORITHM 		= 'SHA256';
    const HASH_ALGORITHM 	= PASSWORD_BCRYPT;
    const HASH_COSTS		= 12;

	use HashTrait;
}
?>