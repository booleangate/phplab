<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Utils_Facebook {
	/**
	 * Decodes a Facebook signed request
	 * @param string $request
	 * @return NULL|array
	 * @see https://developers.facebook.com/docs/facebook-login/using-login-with-games/
	 */
	public static function decodeSignedRequest($request, $secret) {
		list($encodedSignature, $encodedPayload) = explode(".", $request, 2);

		// decode the data
		$sig = self::base64UrlDecode($encodedSignature);
		$data = json_decode(self::base64UrlDecode($encodedPayload), true);

		// confirm the signature
		$expectedSignature = hash_hmac("sha256", $encodedPayload, $secret, $raw = true);

		if ($sig !== $expectedSignature) {
			throw new Exception("Bad Signed JSON signature!");
		}

		return $data;
	}

	public static function isExpiredRequest($request) {
		return $request && $request["expires"] < time();
	}

	private static function base64UrlDecode($input) {
		return base64_decode(strtr($input, "-_", "+/"));
	}
}
