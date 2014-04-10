<?php

/**
 * 1) Load config
 * 2) Initialize whatever
 * 3) Dispatch request
 *
 * @author johnsonj
 * @version 20131124 johnsonj
 */

require_once("../config.php");

// Setup include path for the library and application modules.
set_include_path(
	"../application/controllers" . PATH_SEPARATOR .
	"../library/" . PATH_SEPARATOR .
	get_include_path()
);

// Setup the class autoloader.
function __autoload($className) {
	require_once(str_replace("_", "/", $className) . ".php");
}

// Dispatch request
$dispatcher = new Dispatcher();
$dispatcher->setModuleRootPath(APP_PATH_MODULE_ROOT)
	->dispatch($_SERVER["REQUEST_METHOD"], $_SERVER["REQUEST_URI"]);
