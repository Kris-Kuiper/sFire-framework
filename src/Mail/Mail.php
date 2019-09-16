<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2019 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */ 

namespace sFire\Mail;

use sFire\Mail\MailInterface;
use sFire\MVC\ViewModel;
use sFire\Template\Template;
use sFire\Config\Path;
use sFire\System\File;
use sFire\Application\Application;

Final class Mail implements MailInterface {


	/**
	 * @var array $headers
	 */
	private $headers = [];


	/**
	 * @var string $subject
	 */
	private $subject = '';


	/**
	 * @var array $message
	 */
	private $message = [];


	/**
	 * @var string $boundary
	 */
	private $boundary;

	
	/**
	 * @var boolean $send
	 */
	private $send = false;


	/**
	 * @var array $viewmodels
	 */
	private $viewmodels = [];


	/**
	 * @var array $variables
	 */
	private $variables = [];


	/**
	 * Try to send the mail with optional callback.
	 * @param object $closure
	 * @return sFire\Mail\Mail
	 */
	public function send($closure = null) {

		if(gettype($closure) == 'object') {
			call_user_func($closure, $this);
		}

		//Renders the optional viewmodels and assign the mail variables to them
		foreach($this -> viewmodels as $type => $view) {

			if(null !== $view) {

				$viewmodel = new ViewModel($view, false);
				$viewmodel -> assign($this -> variables);

				$this -> message[$type] = $viewmodel -> render();
			}
		}

		$to 	 = $this -> formatTo();
		$headers = $this -> formatHeaders();
		$message = $this -> formatMessage();

		@mail($to, $this -> subject, $message, $headers);

		if(null === error_get_last()) {
			$this -> send = true;			
		}

		return $this;
	}


	/**
	 * Returns if mail has been successfully send
	 * @return boolean
	 */
	public function success() {
		return $this -> send;
	}


	/**
	 * Returns if mail has been failed to send
	 * @return boolean
	 */
	public function fails() {
		return !$this -> send;
	} 


	/**
	 * Adds a custom header to the mail
	 * @param string $key
	 * @param string $value
	 * @return sFire\Mail\Mail
	 */
	public function addHeader($key, $value) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(false === is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(false === isset($this -> headers['custom'])) {
			$this -> headers['custom'] = [];
		}

		$this -> headers['custom'][$key] = $value;
	}


	/**
	 * Adds a message in HTML and/or plain text by giving a string text
	 * @param string $html
	 * @param string $text
	 * @return sFire\Mail\Mail 
	 */
	public function message($html = null, $text = null) {

		if(null !== $html && false === is_string($html)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($html)), E_USER_ERROR);
		}

		if(null !== $text && false === is_string($text)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($text)), E_USER_ERROR);
		}

		$this -> message['html'] = $html;
		$this -> message['text'] = $text;

		return $this;
	}


	/**
	 * Returns all the custom headers
	 * @return array
	 */
	public function getHeaders() {
		return false === isset($this -> headers['custom']) ? [] : $this -> headers['custom'];
	}


	/**
	 * Removes a custom header by key
	 * @param string $key
	 * @return sFire\Mail\Mail
	 */
	public function removeHeader($key) {

		if(false === is_string($key)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($key)), E_USER_ERROR);
		}

		if(true === isset($this -> headers['custom'][$key])) {
			unset($this -> headers['custom'][$key]);
		}
	}


	/**
	 * Adds an attachment to the email
	 * @param sFire\System\File|string $file
	 * @param string $name
	 * @param string $mime
	 * @return sFire\Mail\Mail
	 */
	public function attachment($file, $name = null, $mime = null) {

		if(true === is_string($file)) {
			$file = new File($file);
		}

		if(false === $file instanceof File) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string or sFire\System\File, "%s" given', __METHOD__, gettype($file)), E_USER_ERROR);
		}

		if(false === $file -> isReadable()) {
			return trigger_error(sprintf('Argument 1 passed to %s() must existing and readable file', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(null !== $mime && false === is_string($mime)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($mime)), E_USER_ERROR);
		}

		if(false === isset($this -> headers['files'])) {
			$this -> headers['files'] = [];
		}

		$name = null !== $name ? $name : $file -> entity() -> getBasename();
		$mime = null !== $mime ? $mime : $file -> getMime();

		$this -> headers['files'][] = (object) ['file' => $file, 'name' => $name, 'mime' => $mime];

		return $this;
	}


	/**
	 * Adds subject
	 * @param string $subject
	 * @return sFire\Mail\Mail
	 */
	public function subject($subject) {

		if(false === is_string($subject)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($subject)), E_USER_ERROR);
		}

		$this -> subject = $subject;

		return $this;
	}


	/**
	 * Parses the message by given an optional HTML view and an optional plain text view
	 * @param string $htmlview
	 * @param string $textview
	 * @return sFire\Mail\Mail
	 */
	public function render($htmlview = null, $textview = null) {

		if(null !== $htmlview && false === is_string($htmlview)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($htmlview)), E_USER_ERROR);
		}

		if(null !== $textview && false === is_string($textview)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($textview)), E_USER_ERROR);
		}

		$this -> viewmodels = ['html' => $htmlview, 'text' => $textview];

		return $this;
	}


	/**
	 * Assign variables to the current view
	 * @param string|array $key
	 * @param string $value
	 */
	public function assign($key, $value = null) {

		if(true === is_array($key)) {
			$this -> variables = array_merge($this -> variables, $key);
		}
		else {
			$this -> variables[$key] = $value;
		}

		return $this;
	}


	/**
	 * Sets the priority Level between 1 and 5
	 * @param int $level
	 * @return sFire\Mail\Mail
	 */
	public function priority($level = 1) {

		if(false === ('-' . intval($level) == '-' . $level)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($level)), E_USER_ERROR);
		}

		if($level < 1 || $level > 5) {
			return trigger_error(sprintf('Argument 1 passed to %s() should be between 1 and 5, "%s" given', __METHOD__, $level), E_USER_ERROR);	
		}

		$priorities = [
			
			1 => ['1 (Highest)', 'High', 'High'],
			2 => ['2 (High)', 'High', 'High'],
			3 => ['3 (Normal)', 'Normal', 'Normal'],
			4 => ['4 (Low)', 'Low', 'Low'],
			5 => ['5 (Lowest)', 'Low', 'Low'],
		];

		$this -> headers['priority'] = $priorities[$level];

		return $this;
	}


	/**
	 * Adds to email
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function to($email, $name = null) {

		if(false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(false === isset($this -> headers['to'])) {
			$this -> headers['to'] = [];
		}

		$this -> headers['to'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}


	/**
	 * Adds from email to headers
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function from($email, $name = null) {

		if(false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		$this -> headers['from']   = [];
		$this -> headers['from'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}


	/**
	 * Adds reply-to to headers
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function reply($email, $name = null) {

		if(false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		$this -> headers['reply-to']   = [];
		$this -> headers['reply-to'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}

	
	/**
	 * Adds email to bcc
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function bcc($email, $name = null) {

		if(false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(false === isset($this -> headers['bcc'])) {
			$this -> headers['bcc'] = [];
		}

		$this -> headers['bcc'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}


	/**
	 * Adds email to cc
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function cc($email, $name = null) {

		if(false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		if(false === isset($this -> headers['cc'])) {
			$this -> headers['cc'] = [];
		}

		$this -> headers['cc'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}


	/**
	 * Adds a notify email
	 * @param string $email
	 * @param string $name
	 * @return sFire\Mail\Mail
	 */
	public function notify($email = null, $name = null) {

		if(null !== $email && false === $this -> validateEmail($email)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be a valid email', __METHOD__), E_USER_ERROR);
		}

		if(null !== $name && false === is_string($name)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($name)), E_USER_ERROR);
		}

		$this -> headers['notify']   = [];
		$this -> headers['notify'][] = (object) ['email' => $email, 'name' => $name];

		return $this;	
	}

	
	/**
	 * Returns if a email is valid or not
	 * @param string email
	 * @return boolean
	 */
	private function validateEmail($email) {

		if(true === is_string($email)) {
			return false !== filter_var(trim($email), \FILTER_VALIDATE_EMAIL);
		}

		return false;
	}


	/**
	 * Formats an array with emails to string
	 * @param array $emails
	 * @return null|string
	 */
	private function emailsToString($emails) {

		$format = [];

		foreach($emails as $email) {

			if(true === isset($email -> name) && trim($email -> name) !== '') {
				$format[] = sprintf('"%s" <%s>', $email -> name, filter_var($email -> email, FILTER_SANITIZE_EMAIL));
			}
			else {
				$format[] = filter_var($email -> email, FILTER_SANITIZE_EMAIL);
			}
		}

		$emails = implode($format, ',');

		return ('' !== trim($emails)) ? $emails : null;
	}


	/**
	 * Formats the "to" email and returns it
	 * @return string
	 */
	private function formatTo() {

		$to = null;

		if(true === isset($this -> headers['to'])) {
			$to = $this -> emailsToString($this -> headers['to']);
		}

		return $to;
	}


	/**
	 * Formats the headers and returns it as a string
	 * @return string
	 */
	private function formatHeaders() {

		$headers = [];
		$files 	 = true === isset($this -> headers['files']) ? $this -> headers['files'] : [];

		//Prepair emailaddresses
		foreach(['BCC', 'CC', 'Reply-To', 'From'] as $type) {

			if(true === isset($this -> headers[strtolower($type)])) {
				$headers[] = sprintf("%s: %s\r\n", $type, $this -> emailsToString($this -> headers[strtolower($type)]));
			}
		}

		//Notify
		if($notify = $this -> formatNotify()) {
			
			$headers[] = sprintf("Disposition-Notification-To: %s\r\n", $notify);
			$headers[] = sprintf("X-Confirm-Reading-To: %s\r\n", $notify);
		}

		//Priority
		if(true === isset($this -> headers['priority'])) {

			$headers[] = sprintf("X-Priority: %s\r\n", 			$this -> headers['priority'][0]);
			$headers[] = sprintf("X-MSMail-Priority: %s\r\n", 	$this -> headers['priority'][1]);
			$headers[] = sprintf("Importance: %s\r\n", 			$this -> headers['priority'][2]);
		}

		//Custom headers
		if(true === isset($this -> headers['custom'])) {

			foreach($this -> headers['custom'] as $key => $value) {
				$headers[] = sprintf("%s: %s\r\n", $key, $value);
			}
		}

		//Files
		if(count($files) > 0) {
			$headers[] = sprintf("Content-Type: multipart/mixed; boundary=\"Boundary-mixed-%s\"\r\n", $this -> getBoundary());
		}
		else {
			$headers[] = sprintf("Content-Type: multipart/alternative; boundary=\"Boundary-alt-%s\"\r\n\r\n", $this -> getBoundary());
		}

		return implode('', $headers);
	}


	/**
	 * Formats the messages and returns it as a string
	 * @return string
	 */
	private function formatMessage() {

		$message = [];
		$files 	 = true === isset($this -> headers['files']) ? $this -> headers['files'] : [];

		if(count($files) > 0) {
			
			$message[] = sprintf("--Boundary-mixed-%s\r\n", $this -> getBoundary());
			$message[] = sprintf("Content-Type: multipart/alternative; boundary=\"Boundary-alt-%s\"\r\n\r\n", $this -> getBoundary());
		}

		if(true === isset($this -> message['text'])) {

			$message[] = sprintf("--Boundary-alt-%s\r\n", $this -> getBoundary());
			$message[] = "Content-Type: text/plain; charset=\"iso-8859-1\"\r\n";
			$message[] = "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$message[] = sprintf("%s\r\n\r\n", $this -> message['text']);
		}

		if(true === isset($this -> message['html'])) {

			$message[] = sprintf("--Boundary-alt-%s\r\n", $this -> getBoundary());
			$message[] = "Content-Type: text/html; charset=\"iso-8859-1\"\r\n";
			$message[] = "Content-Transfer-Encoding: 7bit\r\n\r\n";
			$message[] = sprintf("%s\r\n\r\n", $this -> message['html']);
		}

		$message[] = sprintf("--Boundary-alt-%s--\r\n\r\n", $this -> getBoundary());

		if(count($files) > 0) {
			
			foreach($files as $attachment) {

				$stream 	= fopen($attachment -> file -> entity() -> getBasepath(), 'rb');
				$data 		= fread($stream, $attachment -> file -> getFilesize());
				$data 		= chunk_split(base64_encode($data));

				$message[] = sprintf("--Boundary-mixed-%s\r\n", $this -> getBoundary());
				$message[] = sprintf("Content-Type: %s; name=\"%s\"\r\n", $attachment -> mime, $attachment -> name);
				$message[] = "Content-Transfer-Encoding: base64\r\n";
				$message[] = "Content-Disposition: attachment \r\n\r\n";
				$message[] = sprintf("%s\r\n", $data);
			}

			$message[] = sprintf("--Boundary-mixed-%s--\r\n", $this -> getBoundary());
		}

		return implode('', $message);
	}


	/**
	 * Formats the notify email and returns it
	 * @return null|string 
	 */
	private function formatNotify() {

		$notify = null;

		if(true === isset($this -> headers['notify'])) {

			$notify = $this -> emailsToString($this -> headers['notify']);

			if(null === $notify) {

				if(true === isset($this -> headers['from'])) {
					$notify = $this -> emailsToString($this -> headers['from']);
				}
				else {
					$notify = ini_get('sendmail_from');
				}
			}
		}
		
		return $notify;
	}


	/**
	 * Generates if needed and returns the boundary
	 * @return string
	 */
	private function getBoundary() {

		if(null === $this -> boundary) {
			$this -> boundary = md5(date('r', time()));
		}

		return $this -> boundary;
	}
}