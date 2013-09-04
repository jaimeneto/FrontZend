<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_DbTable_Permission extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'acl_permission';
    protected $_primary      = 'id_permission';
    protected $_modelClass   = 'Acl_Model_Permission';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Role' => array(
            'columns'       => 'id_role',
            'refTableClass' => 'Acl_Model_DbTable_Role',
            'refColumns'    => 'id_role'
        ),
        'Privilege' => array(
            'columns'       => 'id_privilege',
            'refTableClass' => 'Acl_Model_DbTable_Privilege',
            'refColumns'    => 'id_privilege'
        ),
    );
    
    public function getTree()
    {
        $permissions = FrontZend_Container::get('AclPermission')->findAll();
        $tree = array();

        foreach($permissions as $permission) {
            $tree[$permission->id_role][$permission->resource] = 1;
        }
        
        return $tree;
    }
    
    public function getResourcesTree()
    {
        $resources = Zend_Registry::get('Zend_Acl')->getResources();
        asort($resources);
        $resourcesTree = array();
        foreach($resources as $id_resource => $resource) {
            $levels = explode('_', $resource);

            if (isset($levels[2])) {
                $resourcesTree[$levels[0]]['resources'][$levels[1]]
                              ['privileges'][$levels[2]]['id'] = $resource;
                $resourcesTree[$levels[0]]['resources'][$levels[1]]
                              ['privileges'][$levels[2]]['name'] = $levels[2];
            } else if (isset($levels[1])) {
                $resourcesTree[$levels[0]]['resources'][$levels[1]]['id'] = 
                    $resource;
                $resourcesTree[$levels[0]]['resources'][$levels[1]]['name'] = 
                    $levels[1];
            } else {
                $resourcesTree[$levels[0]]['id'] = $resource;
                $resourcesTree[$levels[0]]['name'] = $levels[0];
            }
        } 
        
        return $resourcesTree;
    }
    
}