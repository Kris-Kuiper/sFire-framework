<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Hash;

use sFire\Hash\HashTrait;

final class Hash {

	const METHOD   			= 'AES-256-CBC';
    const ENCODING  		= '8bit';
    const ALGORITHM 		= 'SHA256';
    const HASH_ALGORITHM 	= 'sha256';

	use HashTrait;
}