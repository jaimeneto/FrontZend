<?php

require_once 'functions.php';

// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../'));

if (!is_dir(APPLICATION_PATH . '/data/cache/dbtable')) {
	mkdir(APPLICATION_PATH . '/data/cache/dbtable', 0777, true);
}
if (!is_dir(APPLICATION_PATH . '/data/cache/file')) {
	mkdir(APPLICATION_PATH . '/data/cache/file', 0777, true);
}

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV')
        ? getenv('APPLICATION_ENV') : 'testing'));

// Define site url
if (!defined('SITE_URL') && getenv('SITE_URL')) {
	define('SITE_URL', getenv('SITE_URL'));
}
// Define admin route
defined('ADMIN_ROUTE')
    || define('ADMIN_ROUTE', (getenv('ADMIN_ROUTE')
        ? getenv('ADMIN_ROUTE') : 'fz-admin'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));