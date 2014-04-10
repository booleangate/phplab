<?php
/**
 * Basic
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */
abstract class Controller_Api_Rest_Abstract {
	/**
	 * The controller did not handle an exception.  Handle it here.
	 *
	 * @param Exception $exception
	 */
	public function dispatcherExceptionHandler($exception) {
		self::setStatus(500);
		echo "<pre>$exception</pre>";
		die;
	}

	protected static function setHttpStatus($code) {
		Utils_Http::setStatus($code);
	}

	protected function respond($response) {
		Utils_Http::setContentType("application/json");
		echo json_encode($response);
	}
}
