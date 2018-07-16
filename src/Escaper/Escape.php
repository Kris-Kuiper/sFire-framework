<?php

/**
 * sFire Framework
 *
 * @link      https://sfire.nl
 * @copyright Copyright (c) 2014-2018 sFire Framework.
 * @license   http://sfire.nl/license BSD 3-CLAUSE LICENSE
 */

namespace sFire\Escaper;

use sFire\Application\Application;

class Escape {


	/**
     * @var string $encoding
     */
    private static $encoding;


    /**
     * @var integer $flags
     */
    private static $flags = ENT_QUOTES | ENT_SUBSTITUTE;


	/**
     * @var array $encodings
     */
    private static $encodings = [

        'iso-8859-1',   'iso8859-1',    'iso-8859-5',   'iso8859-5',
        'iso-8859-15',  'iso8859-15',   'utf-8',        'cp866',
        'ibm866',       '866',          'cp1251',       'windows-1251',
        'win-1251',     '1251',         'cp1252',       'windows-1252',
        '1252',         'koi8-r',       'koi8-ru',      'koi8r',
        'big5',         '950',          'gb2312',       '936',
        'big5-hkscs',   'shift_jis',    'sjis',         'sjis-win',
        'cp932',        '932',          'euc-jp',       'eucjp',
        'eucjp-win',    'macroman'
    ];


	/**
	 * Set a new encoding
	 * @param string $encoding
	 */
	public static function setEncoding($encoding) {

		if(false === is_string($encoding) || 0 === strlen($encoding)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($encoding)), E_USER_ERROR);
		}

		$encoding = strtolower($encoding);

		if(false === in_array($encoding, static :: $encodings)) {
			return trigger_error(sprintf('Argument 1 passed to %s() is not a supported encoding. Provide an encoding supported by htmlspecialchars()', __METHOD__), E_USER_ERROR);
		}

		static :: $encoding = $encoding;
	}


	/**
	 * Returns the encoding
	 * @return string
	 */
	public static function getEncoding() {

		if(null === static :: $encoding) {
			return Application :: get('encoding', 'utf-8');
		}

		return static :: $encoding;
	}


	/**
	 * Escapes HTML
	 * @param string $string
	 * @return string
	 */
	public static function html($string) {

        if(null === $string) {
            return '';
        }

		if(true === is_numeric($string)) {
    		return $string;
    	}

		if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

        return htmlspecialchars($string, static :: $flags, static :: getEncoding());
    }


    /**
	 * Escapes HTML attribute
	 * @param string $string
	 * @return string
	 */
    public static function attr($string) {

        if(null === $string) {
            return '';
        }

    	if(true === is_numeric($string)) {
    		return $string;
    	}

    	if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

        $string = static :: toUtf8($string);

        if(0 === strlen($string) || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9,\.\-_]/iSu', [__CLASS__, 'htmlAttrMatcher'], $string);

        return static :: fromUtf8($result);
    }


    /**
     * Escape a string for the URI or Parameter contexts. This should not be used to escape an entire URI - only a subcomponent being inserted.
     * @param string $string
     * @return string
     */
    public static function url($string) {

        if(null === $string) {
            return '';
        }

    	if(true === is_numeric($string)) {
    		return $string;
    	}

    	if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

        return rawurlencode($string);
    }


    /**
     * Escape a string for the Javascript context.
     * @param string $string
     * @return string
     */
    public static function js($string) {

        if(null === $string) {
            return '';
        }

    	if(true === is_numeric($string)) {
    		return $string;
    	}

    	if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

        $string = static :: toUtf8($string);

        if(0 === strlen($string) || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9,\._]/iSu', [__CLASS__, 'jsMatcher'], $string);

        return static :: fromUtf8($result);
    }


    /**
     * Escape a string for the CSS context.
     * @param string $string
     * @return string
     */
    public static function css($string) {

        if(null === $string) {
            return '';
        }
        
    	if(true === is_numeric($string)) {
    		return $string;
    	}

    	if(false === is_string($string)) {
			return trigger_error(sprintf('Argument 1 passed to %s() must be of the type string, "%s" given', __METHOD__, gettype($string)), E_USER_ERROR);
		}

        $string = static :: toUtf8($string);

        if(0 === strlen($string) || ctype_digit($string)) {
            return $string;
        }

        $result = preg_replace_callback('/[^a-z0-9]/iSu', [__CLASS__, 'cssMatcher'], $string);

        return static :: fromUtf8($result);
    }


    /**
     * Callback function for preg_replace_callback that applies HTML Attribute escaping to all matches.
     * @param array $matches
     * @return string
     */
    private static function htmlAttrMatcher($matches) {

        $chr = $matches[0];
        $ord = ord($chr);

        //The following replaces characters undefined in HTML with the hex entity for the Unicode replacement character.
        if(($ord <= 0x1f && $chr != "\t" && $chr != "\n" && $chr != "\r") || ($ord >= 0x7f && $ord <= 0x9f)) {
            return '&#xFFFD;';
        }

        if(strlen($chr) > 1) {
            $chr = static :: convert($chr, 'UTF-8', 'UTF-32BE');
        }

        $hex = bin2hex($chr);
        $ord = hexdec($hex);

        $entities = [

	        34 => 'quot', //Quotation mark
	        38 => 'amp',  //Ampersand
	        60 => 'lt',   //Less-than sign
	        62 => 'gt',   //Greater-than sign
	    ];

        if(true === isset($entities[$ord])) {
            return '&' . $entities[$ord] . ';';
        }

        if($ord > 255) {
            return sprintf('&#x%04X;', $ord);
        }

        return sprintf('&#x%02X;', $ord);
    }


    /**
     * Callback function for preg_replace_callback that applies Javascript escaping to all matches.
     * @param array $matches
     * @return string
     */
    private static function jsMatcher($matches) {

        $chr = $matches[0];

        if(1 === strlen($chr)) {
            return sprintf('\\x%02X', ord($chr));
        }

        $chr = static :: convert($chr, 'UTF-8', 'UTF-16BE');
        $hex = strtoupper(bin2hex($chr));

        if(strlen($hex) <= 4) {
            return sprintf('\\u%04s', $hex);
        }

        return sprintf('\\u%04s\\u%04s', substr($hex, 0, 4), substr($hex, 4, 4));
    }


    /**
     * Callback function for preg_replace_callback that applies CSS escaping to all matches.
     * @param array $matches
     * @return string
     */
    private static function cssMatcher($matches) {

        $chr = $matches[0];

        if(1 === strlen($chr)) {
            $ord = ord($chr);
        }
        else {
            
            $chr = static :: convert($chr, 'UTF-8', 'UTF-32BE');
            $ord = hexdec(bin2hex($chr));
        }

        return sprintf('\\%X ', $ord);
    }


    /**
     * Converts string from encoding to another encoding
     * @param string $string
     * @param array|string $from
     * @param string $to
     * @return string
     */
    private static function convert($string, $from, $to) {

        if(true === function_exists('iconv')) {
            $result = iconv($from, $to, $string);
        } 
        elseif(true === function_exists('mb_convert_encoding')) {
            $result = mb_convert_encoding($string, $to, $from);
        }
        else {
			return trigger_error(sprintf('%s requires either the iconv or mbstring extension to be installed', __METHOD__), E_USER_ERROR);
        }

        if($result === false) {
            return '';
        }

        return $result;
    }


    /**
     * Converts a string from UTF-8 to the base encoding
     * @param string $string
     * @return string
     */
    private static function toUtf8($string) {

        if(static :: getEncoding() === 'utf-8') {
            $result = $string;
        }
        else {
            $result = static :: convert($string, static :: getEncoding(), 'UTF-8');
        }

        if(false === static :: isUtf8($result)) {
            return trigger_error(sprintf('String to be escaped was not valid UTF-8 or could not be converted'), E_USER_ERROR);
        }

        return $result;
    }


    /**
     * Converts a string from UTF-8 to the base encoding.
     * @param string $string
     * @return string
     */
    private static function fromUtf8($string) {

        if(static :: getEncoding() === 'utf-8') {
            return $string;
        }

        return static :: convert($string, 'UTF-8', static :: getEncoding());
    }


    /**
     * Checks if a given string appears to be valid UTF-8 or not.
     * @param string $string
     * @return bool
     */
    private static function isUtf8($string) {
        return ($string === '' || preg_match('/^./su', $string));
    }
}
?>