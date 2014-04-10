<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class User_Dao {
	private $db;

	public function __construct() {
		$this->db = Db_Factory::getInstance(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	}

	public function createIfNotExists(User $user) {
		$userId = (int)$user->getId();
		$this->db->query("INSERT INTO user (user_id, ctime) VALUES({$userId}, NOW()) ON DUPLICATE KEY UPDATE user_id=user_id");
	}

	public function getCount() {
		$result = $this->db->query("SELECT count(*) as `count` FROM user;");
		$row = $result->fetch_object();

		return is_object($row) ? (int)$row->count : 0;
	}
}
