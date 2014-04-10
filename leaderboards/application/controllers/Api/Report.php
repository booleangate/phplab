<?php
/**
 * Score recording methods
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Api_Report extends Controller_Api_Rest_Abstract {
	const SCORE_COUNT = 10;

	private $scoreDao;

	public function __construct() {
		$this->scoreDao = new Score_Dao();
	}

	public function topScorersAction() {
		$this->respond(array(
			"scores" => $this->scoreDao->getTopScores(self::SCORE_COUNT)
		));
	}

	public function topGainersAction() {
		$this->respond(array(
			"scores" => $this->scoreDao->getTopGainers(self::SCORE_COUNT)
		));
	}

	public function countUsersAction() {
		$userDao = new User_Dao();

		$this->respond(array(
			"count" => $userDao->getCount()
		));
	}

	public function countUsersTodayAction() {
		$this->respond(array(
			"count" => $this->scoreDao->getCountUserToday()
		));
	}
}
