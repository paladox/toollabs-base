<?php
/**
 * Interact with request and session data for incoming web request
 *
 * This file is inspired by MediaWiks' WebRequest class.
 *
 * https://svn.wikimedia.org/viewvc/mediawiki/trunk/phase3/includes/WebRequest.php?view=markup&pathrev=82694
 *
 * @since 0.1.0
 * @author Krinkle, 2011-2014
 * @license Public domain, WTFPL
 * @package toollabs-base
 */

class Request {
	/** @var Array */
	protected $raw;

	function __construct( Array $raw ) {
		$this->raw = $raw;
	}

	public function getRawVal( $arr, $key, $default ) {
		return isset( $arr[$key] ) ? $arr[$key] : $default;
	}

	/**
	 * @return string|null
	 */
	public function getVal( $key, $default = null ) {
		$val = $this->getRawVal( $this->raw, $key, $default );
		if ( is_array( $val ) ) {
			$val = $default;
		}
		if ( is_null( $val ) ) {
			return null;
		} else {
			return (string)$val;
		}
	}

	/**
	 * @return array|null
	 */
	public function getArray( $name, $default = null ) {
		$val = $this->getRawVal( $this->raw, $name, $default );
		if ( is_null( $val ) ) {
			return null;
		} else {
			return (array) $val;
		}
	}

	/**
	 * Is the key is set, no matter the value. Useful when dealing with HTML checkboxes.
	 * @return bool
	 */
	public function hasKey( $key ) {
		return array_key_exists( $key, $this->raw );
	}

	/**
	 * @return int
	 */
	public function getInt( $key, $default = 0 ) {
		return intval( $this->getVal( $key, $default ) );
	}

	/**
	 * @return bool
	 */
	public function getFuzzyBool( $key, $default = false ) {
		return $this->hasKey( $key ) && $this->getVal( $key ) !== 'false';
	}

	public function getCookie( $key, $default = null ) {
		global $kgConf;
		return $this->getRawVal( $_COOKIE, $kgConf->getCookiePrefix() . $key, $default );
	}

	public function setCookie( $key, $value, $expire = 0 ) {
		global $kgConf;

		if ( $value === null && $expire === 0 ) {
			// Delete cookie
			$expire = -1;
		}

		$options = is_array( $expire ) ? $expire : array( 'expire' => $expire );
		$options += array(
			'expire' => 0,
			'path' => '/',
			'domain' => '',
			'secure' => false,
			// By default disallow access by JavaScript to cookies set here
			'httpOnly' => true,
		);
		// http://www.php.net/setcookie
		return setcookie(
			$kgConf->getCookiePrefix() . $key,
			$value,
			$options['expire'],
			$options['path'],
			$options['domain'],
			$options['secure'],
			$options['httpOnly']
		);
	}

	/**
	 * Get data from session
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function getSessionData( $key ) {
		self::ensureSession();
		if ( !isset( $_SESSION[ $key ] ) ) {
			return null;
		}
		return $_SESSION[$key];
	}

	/**
	 * Set session data
	 *
	 * @param string $key
	 * @param mixed $data
	 */
	public function setSessionData( $key, $data ) {
		self::ensureSession();
		$_SESSION[ $key ] = $data;
	}

	/**
	 * @return bool
	 */
	public function wasPosted() {
		return isset( $_SERVER['REQUEST_METHOD'] ) && $_SERVER['REQUEST_METHOD'] == 'POST';
	}

	/**
	 * @return string
	 */
	public function getQueryString(){
		return http_build_query( $this->raw );
	}

	/**
	 * Detect the protocol from $_SERVER.
	 * This is for use prior to Setup.php, when no WebRequest object is available.
	 * At other times, use the non-static function getProtocol().
	 *
	 * @return array
	 */
	public function getProtocol() {
		if ( ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) ||
			( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' ) ) {
			return 'https';
		} else {
			return 'http';
		}
	}

	/**
	 * Lazy-initialise the session
	 */
	protected static function ensureSession() {
		// If the cookie or session id is already set we already have a session and should abort
		if ( isset( $_COOKIE[session_name()] ) || session_id() ) {
			return;
		}

		session_start();
	}

}
