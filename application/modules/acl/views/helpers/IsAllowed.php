<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_View_Helper_IsAllowed extends Zend_View_Helper_Abstract
{
    public function isAllowed($resource)
    {
        $acl = Zend_Registry::get('Zend_Acl');

        if (!$acl->has($resource)) {
            return true;
        }

        $role = Zend_Auth::getInstance()->hasIdentity()
            ? Zend_Auth::getInstance()->getIdentity()->id_role
            : 'guest';

        return $acl->isAllowed($role, $resource);
    }

}
