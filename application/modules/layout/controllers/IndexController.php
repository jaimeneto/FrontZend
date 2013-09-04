<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_IndexController extends Zend_Controller_Action
{
    public function indexAction()
    {
        $this->_forward('index', 'page');
    }
}