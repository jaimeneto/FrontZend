<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_DbTable_User extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'acl_user';
    protected $_primary      = 'id_user';
    protected $_modelClass   = 'Acl_Model_User';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Role' => array(
            'columns'       => 'id_role',
            'refTableClass' => 'Acl_Model_DbTable_Role',
            'refColumns'    => 'id_role'
        ),
    );

}