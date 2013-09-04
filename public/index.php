<?php

// Define paths to public directory
define('PUBLIC_PATH', dirname(__FILE__));

require_once '../application/configs/init.php';

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run

$configs = array(
    APPLICATION_PATH . '/configs/application.ini',
);
$modulesDir = new DirectoryIterator(APPLICATION_PATH . '/modules');
foreach($modulesDir as $dir) {
    if (file_exists($dir->getPathname() . '/configs/module.ini')) {
        $configs[] = $dir->getPathname() . '/configs/module.ini';
    }
}
$configs[] = APPLICATION_PATH . '/configs/local.ini';

$application = new Zend_Application(
    APPLICATION_ENV,
    array('config' => $configs)
);
$application->bootstrap()
            ->run();