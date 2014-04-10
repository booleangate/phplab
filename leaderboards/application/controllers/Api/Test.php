<?php
/**
 * Generates random test data for the API
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Api_Test extends Controller_Api_Rest_Abstract {
	const DEFAULT_GENERATE_SCORE_COUNT = 10000;
	const RANDOM_DATE_START_OFFSET = 1814400; // 3 weeks
	const RANDOM_MAX_USER_ID = 100000;
	const RANDOM_MAX_SCORE = 2147483647; // max mysql int size

	public function generatePostAction() {
		set_time_limit(0);

		$scoreApi = new Api_Score();
		$scoreCount = isset($_GET["count"]) ? (int)$_GET["count"] : self::DEFAULT_GENERATE_SCORE_COUNT;
		$startTime = time();

		for ($i = 0; $i < $scoreCount; ++$i) {
			$score = self::getRandomScore();
			$scoreApi->addScore($score);
		}

		$duration = time() - $startTime;

		Utils_Http::setStatus(201);

		$this->respond(array(
			"scoresCreated" => $scoreCount,
			"durationSeconds" => $duration
		));
	}

	private static function getRandomScore() {
		$user = new User(mt_rand(1, self::RANDOM_MAX_USER_ID));
		$score = new Score($user, mt_rand(1, self::RANDOM_MAX_SCORE), self::getRandomDate());
		return $score;
	}

	private static function getRandomDate() {
		$now = time();
		$startTime = $now - self::RANDOM_DATE_START_OFFSET;

		return mt_rand($startTime, $now);
	}
}
