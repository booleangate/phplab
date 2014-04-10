<?php
/**
 * @author johnsonj
 * @version 20131124 johnsonj
 */

// Database
define("DB_HOST", "localhost");
define("DB_USERNAME", "root");
define("DB_PASSWORD", "");
define("DB_NAME", "kixeye");

// Facebook
define("FB_APP_ID", 126767144061773);
define("FB_APP_SECRET", "21db65a65e204cca7b5afcbad91fea59");

// Dispatcher
define("APP_PATH_ROOT", realpath(getcwd() . "/..") . "/");
define("APP_PATH_MODULE_ROOT", APP_PATH_ROOT . "application/controllers/");

// Timezone
date_default_timezone_set("America/Los_Angeles");
