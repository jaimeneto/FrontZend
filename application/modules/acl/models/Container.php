<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage Container
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_Container extends FrontZend_Module_Container
{    
    public function newAclUser()
    {
        return new Acl_Model_DbTable_User();
    }
    
    public function newAclRole()
    {
        return new Acl_Model_DbTable_Role();
    }
    
    public function newAclPermission()
    {
        return new Acl_Model_DbTable_Permission();
    }
    
}