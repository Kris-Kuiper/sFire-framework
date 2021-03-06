<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Mail;

interface MailInterface {

	
	/**
	 * Try to send the mail with optional callback
	 * @param object $closure
	 * @return $this
	 */
	public function send($closure = null);


	/**
	 * Adds to email
	 * @param string $email
	 * @param string $name
	 * @return $this
	 */
	public function to($email, $name);

	
	/**
	 * Adds an attachment to the email
	 * @param string $mime
	 * @param string $name
	 * @param sFire\System\File|string $file
	 * @return $this
	 */
	public function attachment($file, $name = null, $mime = null);


	/**
	 * Adds reply-to to headers
	 * @param string $email
	 * @param string $name
	 * @return $this
	 */
	public function reply($email, $name);


	/**
	 * Adds email to bcc
	 * @param string $email
	 * @param string $name
	 * @return $this
	 */
	public function bcc($email, $name);


	/**
	 * Adds subject
	 * @param string $subject
	 * @return $this
	 */
	public function subject($subject);


	/**
	 * Adds email to cc
	 * @param string $email
	 * @param string $name
	 * @return $this
	 */
	public function cc($email, $name);


	/**
	 * Adds from email to headers
	 * @param string $email
	 * @param string $name
	 * @return $this
	 */
	public function from($email, $name);
}