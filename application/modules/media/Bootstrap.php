<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Bootstrap
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

require_once 'PicMaster/Pic.php';

class Media_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initPaths()
    {
        Media_Model_File::setBasePath('files');
        Media_Model_File::setThumbsPath('cache/thumbs');
    }

    protected function _initContainer()
    {
        $modelContainer = new Media_Model_Container();
        $modelContainer->init();
    }
}

