<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Utils_Http {
	private static $statuses = array(
		"200" => "OK",
		"201" => "Created",
		"400" => "Bad Request",
		"401" => "Unauthorized",
		"403" => "Forbidden",
		"404" => "Not Found",
		"405" => "Method Not Allowed",
		"408" => "Request Timeout",
		"409" => "Conflict",
		"410" => "Gone",
		"500" => "Internal Server Error",
		"501" => "Not Implemented",
		"503" => "Service Unavailable"
	);

	public static function setStatus($code) {
		if (!isset(self::$statuses[$code])) {
			return false;
		}

		header("HTTP/1.0 {$code} " . self::$statuses[$code]);
	}

	public static function setContentType($type) {
		header("Content-Type: {$type}");
	}
}
