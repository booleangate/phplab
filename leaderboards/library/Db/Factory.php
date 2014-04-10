<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Db_Factory {
	private static $connections = array();

	public static function getInstance($host, $username, $password, $database) {
		$key = $host . $username;

		if (!isset(self::$connections[$key])) {
			self::$connections[$key] = new mysqli($host, $username, $password, $database);
		}

		return self::$connections[$key];
	}
}
