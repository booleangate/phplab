<?php
/**
 * Score recording methods
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Api_Score extends Controller_Api_Rest_Abstract {
	public function indexPostAction() {
		$signedRequest = isset($_POST["signed_request"]) ? $_POST["signed_request"] : false;
		$scoreValue = isset($_POST["user_score"]) ? (int)$_POST["user_score"] : 0;

		// Confirm that we have all of our required data
		if (!$signedRequest || !$scoreValue ) {
			Utils_Http::setStatus(400);
			return;
		}

		// Decode and validate the signed request
		try {
			$request = Utils_Facebook::decodeSignedRequest($signedRequest, FB_APP_SECRET);
		} catch(Exception $e) {
			// Just swallow the exception since invalid requests are handled below.
		}

		// Invalid signed request, request is not authorized.
		if (!$request) {
			Utils_Http::setStatus(401);
			return;
		}

		// Valid signed request, but it is expired.
		if (Utils_Facebook::isExpiredRequest($request)) {
			Utils_Http::setStatus(408);
			return;
		}

		$user = new User($request["user_id"]);
		$score = new Score($user, $scoreValue, $request["issued_at"]);

		$this->addScore($score);
	}

	public function addScore(Score $score) {
		$userDao = new User_Dao();
		$scoreDao = new Score_Dao();

		// Ensure the user exists.  In most normal systems we could assume access to a global user database,
		// but we don't have that here.  So we store the user ID so that we can simplify reporting later on
		// (sacrifice space for speed).
		$userDao->createIfNotExists($score->getUser());

		// Write the score
		try {
			$scoreDao->save($score);
			Utils_Http::setStatus(201);
			return;
		} catch (Exception $e) {
			error_log("Could not save score $score. Exception: $e");
		}

		Utils_Http::setStatus(500);
	}
}
