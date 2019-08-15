<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\Handler;

class NotImplementedHandler {

	public function __construct($method) {
		return trigger_error(sprintf('Method "%s" is not implemented', $method), E_USER_ERROR);
	}
}
?>