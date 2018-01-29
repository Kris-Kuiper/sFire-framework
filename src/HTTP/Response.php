<?php
/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */
 
namespace sFire\HTTP;

use sFire\System\File;

class Response {
	
	/**
	 * Sets the response header by type and value
	 * @param string $type
	 * @param string $value
	 */
	public static function addHeader($type, $value, $code = null) {

		if(false == is_string($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		if(false == is_string($value)) {
			return trigger_error(sprintf('Argument 2 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($value)), E_USER_ERROR);
		}

		if(null !== $code && false === ('-' . intval($code) == '-' . $code)) {
			return trigger_error(sprintf('Argument 3 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($code)), E_USER_ERROR);
		}

		if(null === $code) {
			return header($type . ':' . $value, true);
		}

		header($type . ':' . $value, true, $code);
	}


	/**
	 * Remove response header by type
	 * @param string $type
	 */
	public static function removeHeader($type) {
		
		if(false == is_string($type)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($type)), E_USER_ERROR);
		}

		header_remove($type);
	}


	/**
	 * Remove all response headers
	 */
	public static function removeHeaders() {
		header_remove();
	}


	/**
	 * Give a file for the client to download
	 * @param sFire\System\File $file
	 */
	public static function file(File $file) {

		if(null !== $file -> entity()) {

			static :: addHeader('Content-Type', $file -> entity() -> getMime());
			static :: addHeader('Content-Transfer-Encoding', 'binary');
			static :: addHeader('Expires', '0');
			static :: addHeader('Pragma', 'public');
			static :: addHeader('Content-Disposition', 'attachment; filename="' . $file -> entity() -> getBasename() . '"');
			static :: addHeader('Content-Length', $file -> entity() -> getFilesize());
			static :: addHeader('Cache-Control', 'no-cache, no-store, max-age=0, must-revalidate');
			static :: addHeader('Expires', 'Tue, 25 Oct '. (date('Y') - 10) .' 05:00:00 GMT');
			static :: addHeader('Pragma', 'no-cache');

			if($fd = @fopen($file -> entity() -> getBasepath(), 'r')) {

				ob_clean();
				flush();

				while(!feof($fd)) {
					echo fread($fd, 2048);
				}
				
				fclose($fd);
			}
		}
	}


	/**
	 * Sets the HTTP response status by code with optional custom status text
	 * @param int $code
	 */
	public static function setStatus($code) {

		if(false === ('-' . intval($code) == '-' . $code)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type integer, "%s" given', __METHOD__, gettype($code)), E_USER_ERROR);
		}

		$protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.0';

		switch($code) {

			case 100 : header($protocol . ' 100 Continue'); break;
			case 101 : header($protocol . ' 101 Switching Protocols'); break;
			case 102 : header($protocol . ' 102 Processing'); break;
			case 200 : header($protocol . ' 200 OK'); break;
			case 201 : header($protocol . ' 201 Created'); break;
			case 202 : header($protocol . ' 202 Accepted'); break;
			case 203 : header($protocol . ' 203 Non-Authoritative Information'); break;
			case 204 : header($protocol . ' 204 No Content'); break;
			case 205 : header($protocol . ' 205 Reset Content'); break;
			case 206 : header($protocol . ' 206 Partial Content'); break;
			case 207 : header($protocol . ' 207 Multi-Status'); break;
			case 208 : header($protocol . ' 208 Already Reported'); break;
			case 226 : header($protocol . ' 226 IM Used'); break;
			case 300 : header($protocol . ' 300 Multiple Choices'); break;
			case 301 : header($protocol . ' 301 Moved Permanently'); break;
			case 302 : header($protocol . ' 302 Found'); break;
			case 303 : header($protocol . ' 303 See Other'); break;
			case 304 : header($protocol . ' 304 Not Modified'); break;
			case 305 : header($protocol . ' 305 Use Proxy'); break;
			case 306 : header($protocol . ' 306 Switch Proxy'); break;
			case 307 : header($protocol . ' 307 Temporary Redirect'); break;
			case 308 : header($protocol . ' 308 Permanent Redirect'); break;
			case 400 : header($protocol . ' 400 Bad Request'); break;
			case 401 : header($protocol . ' 401 Unauthorized'); break;
			case 402 : header($protocol . ' 402 Payment Required'); break;
			case 403 : header($protocol . ' 403 Forbidden'); break;
			case 404 : header($protocol . ' 404 Not Found'); break;
			case 405 : header($protocol . ' 405 Method Not Allowed'); break;
			case 406 : header($protocol . ' 406 Not Acceptable'); break;
			case 407 : header($protocol . ' 407 Proxy Authentication Required'); break;
			case 408 : header($protocol . ' 408 Request Timeout'); break;
			case 409 : header($protocol . ' 409 Conflict'); break;
			case 410 : header($protocol . ' 410 Gone'); break;
			case 411 : header($protocol . ' 411 Length Required'); break;
			case 412 : header($protocol . ' 412 Precondition Failed'); break;
			case 413 : header($protocol . ' 413 Payload Too Large'); break;
			case 414 : header($protocol . ' 414 Request-URI Too Long'); break;
			case 415 : header($protocol . ' 415 Unsupported Media Type'); break;
			case 416 : header($protocol . ' 416 Requested Range Not Satisfiable'); break;
			case 417 : header($protocol . ' 417 Expectation Failed'); break;
			case 418 : header($protocol . ' 418 I\'m a teapot'); break;
			case 419 : header($protocol . ' 419 Authentication Timeout'); break;
			case 420 : header($protocol . ' 420 Method Failure'); break;
			case 421 : header($protocol . ' 421 Misdirected Request'); break;
			case 422 : header($protocol . ' 422 Unprocessable Entity'); break;
			case 423 : header($protocol . ' 423 Locked'); break;
			case 424 : header($protocol . ' 424 Failed Dependency'); break;
			case 426 : header($protocol . ' 426 Upgrade Required'); break;
			case 428 : header($protocol . ' 428 Precondition Required'); break;
			case 429 : header($protocol . ' 429 Too Many Requests'); break;
			case 431 : header($protocol . ' 431 Request Header Fields Too Large'); break;
			case 440 : header($protocol . ' 440 Login Timeout '); break;
			case 444 : header($protocol . ' 444 No Response'); break;
			case 449 : header($protocol . ' 449 Retry With'); break;
			case 450 : header($protocol . ' 450 Blocked by Windows Parental Controls'); break;
			case 451 : header($protocol . ' 451 Unavailable For Legal Reasons'); break;
			case 494 : header($protocol . ' 494 Request Header Too Large'); break;
			case 495 : header($protocol . ' 495 Cert Error'); break;
			case 496 : header($protocol . ' 496 No Cert'); break;
			case 497 : header($protocol . ' 497 HTTP to HTTPS'); break;
			case 498 : header($protocol . ' 498 Token expired/invalid'); break;
			case 499 : header($protocol . ' 499 Client Closed Request'); break;
			case 500 : header($protocol . ' 500 Internal Server Error'); break;
			case 501 : header($protocol . ' 501 Not Implemented'); break;
			case 502 : header($protocol . ' 502 Bad Gateway'); break;
			case 503 : header($protocol . ' 503 Service Unavailable'); break;
			case 504 : header($protocol . ' 504 Gateway Timeout'); break;
			case 505 : header($protocol . ' 505 HTTP Version Not Supported'); break;
			case 506 : header($protocol . ' 506 Variant Also Negotiates'); break;
			case 507 : header($protocol . ' 507 Insufficient Storage'); break;
			case 508 : header($protocol . ' 508 Loop Detected'); break;
			case 509 : header($protocol . ' 509 Bandwidth Limit Exceeded'); break;
			case 510 : header($protocol . ' 510 Not Extended'); break;
			case 511 : header($protocol . ' 511 Network Authentication Required'); break;
			case 520 : header($protocol . ' 520 Unknown Error'); break;
			case 522 : header($protocol . ' 522 Origin Connection Time-out'); break;
			case 598 : header($protocol . ' 598 Network read timeout error'); break;
			case 599 : header($protocol . ' 599 Network connect timeout error'); break;
		}
	}
}
?>