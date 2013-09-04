<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_Role extends FrontZend_Module_Model_Abstract
{
    public function countUsers()
    {
        return FrontZend_Container::get('AclUser')->count(array(
            'id_role = ?' => $this->getId()
        ));
    }
    
    /**
     *
     * @return Acl_Model_Role
     */
    public function getParent()
    {
        $idParent = $this->id_parent;
        if ($idParent) {
            return FrontZend_Container::get('AclRole')->findById($idParent);
        }
    }

    public function inheritsFrom($id_role)
    {
        if ($this->getId() == $id_role) {
            return false;
        }
        if ($id_role == 'guest' || $id_role == 'master') {
            return true;
        }

        $role = FrontZend_Container::get('AclRole')->findById($id_role);
        if ($role) {
            $id_role = $role->getId();
            while($id_role != 'guest') {
                if ($role->id_parent == $this->getId()) {
                    return true;
                }
                $role = $role->getParent();
                if (!$role) break;
            }
        }

        return false;

    }

    public static function getInputFilter()
    {
        return array();
    }

}
