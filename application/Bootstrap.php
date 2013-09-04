<?php

/**
 * FrontZend CMS
 *
 * @category   Application
 * @package    Application_Bootstrap
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initPaginator()
    {
        Zend_View_Helper_PaginationControl::setDefaultViewPartial('paginator.phtml');
        Zend_Paginator::setDefaultScrollingStyle('Sliding');
        Zend_Paginator::setDefaultItemCountPerPage(25);
        Zend_Paginator::setDefaultPageRange(8);
    }

    protected function _initPluginLoaderCache()
    {
        $classFileIncCache = APPLICATION_PATH . '/data/cache/pluginLoaderCache.php';
        if (file_exists($classFileIncCache)) {
            include_once $classFileIncCache;
        }
        Zend_Loader_PluginLoader::setIncludeFileCache($classFileIncCache);
    }

    protected function _initZFDebug()
    {
        $this->bootstrap('db');

        $config = $this->getOption('zfdebug');
        if($config && Zend_Auth::getInstance()->hasIdentity() &&
                isset(Zend_Auth::getInstance()->getIdentity()->id_role) &&
                Zend_Auth::getInstance()->getIdentity()->id_role == 'master') {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            $options = array(
                'plugins' => array('Variables',
                    'Database' => array('adapter' => Zend_Db_Table::getDefaultAdapter()),
                    'File' => array('basePath' => APPLICATION_PATH . '../'),
                    'Memory',
                    'Time',
//                    'Cache' => array('backend' => $cache->getBackend()),
                    'Exception'
                )
            );
            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }
    
}