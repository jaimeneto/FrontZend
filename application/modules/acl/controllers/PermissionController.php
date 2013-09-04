<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_PermissionController extends Zend_Controller_Action
{
    public function init()
    {
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        $this->_helper->acl("{$module}_{$controller}_{$action}");
    }
    
    public function indexAction()
    {
        $this->_forward('manage');
    }

    public function manageAction()
    {        
        $roles = FrontZend_Container::get('AclRole')->findAll(array(
            'order' => 'order',
            'where' => array('id_role != ?' => 'master')
        ));
        $resources = FrontZend_Container::get('AclPermission')->getResourcesTree();

        foreach($resources['frontend']['resources'] as $idResource => $resource)
        {
            $idPage = substr($idResource, strrpos($idResource, '-')+1);
            $page = FrontZend_Container::get('LayoutPage')->findById($idPage);
            $resources['frontend']['resources'][$idResource]['name'] =
                $page->page;
        }

        $this->view->roles = $roles;
        $this->view->resources = $resources;
        $this->view->acl = Zend_Registry::get('Zend_Acl');
        
        $this->view->headTitle()->append('Gerenciar permissões');
    }

    public function saveAction()
    {
        $permissions = $this->_getParam('permissions');
        $permissionsTree = FrontZend_Container::get('AclPermission')->getTree();
        
        if (!$permissions) {
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit('acl/permission/manage');
        }
        
        foreach($permissions as $id_role => $resources) {
            foreach($resources as $resource => $permission) {
                if ($permission) {
                    if (!isset($permissionsTree[$id_role][$resource])) {
                        FrontZend_Container::get('AclPermission')->insert(array(
                            'id_role'  => $id_role,
                            'resource' => $resource
                        ));
                    }
                } else {
                    if (isset($permissionsTree[$id_role][$resource])) {
                        $perm = FrontZend_Container::get('AclPermission')
                                    ->findOne(array('where' => array(
                                        'id_role = ?'  => $id_role,
                                        'resource = ?' => $resource
                                    )));

                        if ($perm) {
                            FrontZend_Container::get('AclPermission')
                                    ->deleteRow($perm->id);
                        }
                    }
                }
            }
        }
        
        $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/permission/manage');
    }
    
}

