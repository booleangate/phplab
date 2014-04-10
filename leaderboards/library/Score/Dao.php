<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Score_Dao {
	const FIRST_DAY_OF_WEEK = 1; // Monday
	const WEEK_IN_SECONDS = 604800;

	private $db;

	public function __construct() {
		$this->db = Db_Factory::getInstance(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
	}

	public function save(Score $score) {
		// Order matter here. Do not write the new score until checking this week's score progress.
		$this->updateScoreWeeklyGain($score);
		$this->saveScore($score);
	}

	public function getTopScores($count) {
		return $this->getTop("score", $count);
	}

	public function getTopGainers($count) {
		return $this->getTop("score_gain_weekly", $count);
	}

	public function getCountUserToday() {
		$result = $this->db->query("SELECT COUNT(DISTINCT user_id) as `count` FROM score WHERE ctime >= DATE(NOW())");
		$row = $result->fetch_object();

		return is_object($row) ? (int)$row->count : 0;
	}

	private function saveScore(Score $score) {
		$stmt = $this->db->prepare("INSERT INTO score (user_id, value, ctime) VALUES(?, ?, ?)");
		$userId = $score->getUser()->getId();
		$value = $score->getValue();
		$ctime = self::timestampToDateime($score->getCtime());

		$stmt->bind_param("iis", $userId, $value, $ctime);
		$stmt->execute();
		$stmt->close();
	}

	/**
	 * Update the weekly score progress if they've
	 * @param Score $score
	 * @return boolean
	 */
	private function updateScoreWeeklyGain(Score $score) {
		$startOfWeek = $this->getStartOfWeek($score->getCtime());
		$highScoreThisWeek = $this->getWeekHighScore($score->getUser()->getId(), $startOfWeek);

		// If the high score from this week is greater than the score just posted, there is no progress
		if ($highScoreThisWeek >= $score->getValue())
		{
			return false;
		}

		// Get the previous difference
		$stmt = $this->db->prepare("SELECT value, ctime FROM score_gain_weekly WHERE user_id=?");
		$userId = $score->getUser()->getId();
		$stmt->bind_param("i", $userId);
		$stmt->bind_result($previousDiff, $diffCtime);
		$stmt->execute();
		$stmt->fetch();
		$stmt->close();

		// Calculate the new diff
		$diff = $score->getValue() - $highScoreThisWeek;

		// If the last recorded diff is from a previous week or is less than the diff just created, update the stored diff
 		if (strtotime($diffCtime) < $startOfWeek || $previousDiff < $diff) {
			$stmt = $this->db->prepare("INSERT INTO score_gain_weekly (user_id, value, ctime) VALUES(?, ?, ?) ON DUPLICATE KEY UPDATE value=?, ctime=?");
			$ctime = self::timestampToDateime($score->getCtime());

			$stmt->bind_param("iisis", $userId, $diff, $ctime, $diff, $ctime);
			$stmt->execute();

 			return true;
 		}

 		// Nothing was done
		return false;
	}

	private function getWeekHighScore($userId, $startOfCurrentWeek) {
		$stmt = $this->db->prepare("SELECT max(value) FROM score WHERE user_id = ? AND ctime >= ?");
		$ctimeStart = self::timestampToDateime($startOfCurrentWeek);

		$stmt->bind_param("is", $userId, $ctimeStart);
		$stmt->execute();
		$stmt->bind_result($maxScore);
		$stmt->fetch();
		$stmt->close();

		return (int)$maxScore;
	}

	private function getStartOfWeek($now) {
		$datetime = new DateTime();
		$datetime->setTimestamp($now);
		$interval = new DateInterval("P" . (date("N", $now) - self::FIRST_DAY_OF_WEEK) . "D");

		// Remove the time portion from this date
		return strtotime(date("Y-m-d", $datetime->sub($interval)->getTimestamp()));
	}

	private static function timestampToDateime($timestamp) {
		return date("Y-m-d H:i:s", $timestamp);
	}

	private function getTop($table, $count) {
		$result = $this->db->query("SELECT max(value) as value, user_id FROM `{$table}` GROUP BY user_id ORDER BY value DESC, ctime ASC LIMIT " . (int)$count);
		$response = array();

		while ($row = $result->fetch_object()) {
			$response[] = $row;
		}

		return $response;
	}
}
