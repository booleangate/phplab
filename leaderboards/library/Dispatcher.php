<?php
/**
 * Dispatches a Action by loading a class and executing the method that matches the URL.
 *
 * Pattern is:
 * /$module/$class/$method maps to application/controllers/$module/$module_$class/$method"Action"
 *
 * Example:
 *
 * /api/score/ -> application/controllers/Api/Score::indexAction
 * /api/report/top10users -> application/controllers/Api/Api_Report::top10usersAction
 *
 * Optionally, $httpMethod can be used as in infix between the $method name and "Action"
 *
 * POST /api/score/ -> application/controllers/Api/Api_Score::indexPostAction
 * GET /api/report/top10users -> application/controllers/Api/Api_Report::top10usersAction
 *
 * $class can also define dispatcherExceptionHandler(Exception) which will be called in the event of an uncaptured exception.
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */
class Dispatcher {
	const DEFAULT_METHOD_NAME = "index";
	const CLASS_METHOD_SUFFIX = "Action";
	const CLASS_METHOD_EXCEPTION_HANDLER = "dispatcherExceptionHandler";

	private $moduleRootPath;

	public function __construct($moduleRootPath = null) {
		$this->setModuleRootPath($moduleRootPath);
	}

	public function setModuleRootPath($moduleRootPath) {
		$this->moduleRootPath = $moduleRootPath;
		return $this;
	}

	public function dispatch($httpMethod, $url) {
		// Break the URL into expeted parts.  Suppress list errors with @ because we don't care if not all the parts are here (especially $method).
		@list($module, $class, $classMethod) = explode("/", self::getCleanedUrl($url), 3);

		// Ensure that we have a method name
		if (empty($classMethod)) {
			$classMethod = self::DEFAULT_METHOD_NAME;
		}

		$module = self::getModuleName($module);
		$fullyQualifiedClass = self::getClassName($module, $class);
		$path = self::getPath($module, $class);

		// Ensure that the required class definition exists. If not, 404.
		if (!is_file($path)) {
			Utils_Http::setStatus(404);
			die;
		}

		// Load and instantiate the class explicitly (not using autoloading) to enforce the fact that modules should only be in $this->moduleRootPath.
		require_once($path);
		$instance = new $fullyQualifiedClass();

		// Get the class method
		$classMethod = self::getMethod($instance, $httpMethod, $classMethod);

		// No valid class method has been found, error out.
		if ($classMethod === false) {
			Utils_Http::setStatus(404);
			die;
		}

		// Execute the requested method
		try {
			$instance->$classMethod();
		} catch (Exception $e) {
			$exceptionHandlerMethod = self::CLASS_METHOD_EXCEPTION_HANDLER;
			if (method_exists($instance, $exceptionHandlerMethod)) {
				$instance->$exceptionHandlerMethod($e);
			}
		}
	}

	/**
	 * Clean the url by removing the preceeding "/" and the query string (if present).
	 *
	 * @param string $url
	 */
	private static function getCleanedUrl($url) {
		$length = strlen($url);
		$questionMarkIndex = strpos($url, "?");

		if ($questionMarkIndex !== false) {
			$length = $questionMarkIndex - 1;
		}

		return substr($url, 1, $length);
	}

	private static function getModuleName($module) {
		return ucfirst($module);
	}

	/**
	 * Get's the class name prefixed with the module name.
	 *
	 * @param string $module
	 * @param string $class
	 * @return string The fully qualified (instantiable) class name.
	 */
	private static function getClassName($module, $class) {
		return $module . "_" . ucfirst($class);
	}

	private function getPath($module, $class) {
		return $this->moduleRootPath . $module . "/" . $class . ".php";
	}

	/**
	 * Prefer the HTTP method infixed class method. If it's not present, just use the $classMethod as is.  If neither are found, return false.
	 *
	 * @param string $httpMethod
	 * @param string $classMethod
	 */
	private static function getMethod($instance, $httpMethod, $classMethod) {
		// Construct the $httpMethod infixed method
		$method = $classMethod . ucFirst(strtolower($httpMethod)) . self::CLASS_METHOD_SUFFIX;

		// $httpMethod infixed method was found, return that
		if (method_exists($instance, $method)) {
			return $method;
		}

		// Construct the default method name
		$method = $classMethod . self::CLASS_METHOD_SUFFIX;

		if (method_exists($instance, $method)) {
			return $method;
		}

		return false;
	}
}
