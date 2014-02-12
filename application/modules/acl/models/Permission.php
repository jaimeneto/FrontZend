<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_Permission extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Acl_Model_Role
     */
    public function getRole()
    {
        $idRole = $this->id_role;
        if ($idRole) {
            return FrontZend_Container::get('AclRole')->findById($idRole);
        }
    }
    
    public function getInputFilter()
    {
        return array(
            'id_permission' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_role' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'acl_role',
//                            'field' => 'id_role',
//                        ),
//                    ),
//                ),
            ),

            'resource' => array(
                'required' => true,
            ),
            
        );
    }
    
}