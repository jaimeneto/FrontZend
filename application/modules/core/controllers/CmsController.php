<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class CmsController extends Zend_Controller_Action
{

    public function init()
    {
        if (!Acl_Model_Auth::isLoggedIn()) {
            $this->getHelper('alerts')
                 ->addError('Acesso restrito');
            $this->getHelper('Redirector')->gotoRouteAndExit(array(
                'url' => ADMIN_ROUTE), 'login');
        }
    }

    public function indexAction()
    {
        $contentTypes = FrontZend_Container::get('ContentType')->findAll(array(
            'where' => array('id_parent IS NULL')
        ));
        $this->view->contentTypes = $contentTypes;
        
        $roles = FrontZend_Container::get('AclRole')->findAll(array('order' => 'order'));
        $this->view->roles = $roles;
    }

}

