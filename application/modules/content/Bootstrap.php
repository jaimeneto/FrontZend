<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Bootstrap
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Bootstrap extends Zend_Application_Module_Bootstrap
{
    protected function _initContainer()
    {
        $modelContainer = new Content_Model_Container();
        $modelContainer->init();
    }

}

