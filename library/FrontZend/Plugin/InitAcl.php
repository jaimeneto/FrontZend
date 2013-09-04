<?php

class FrontZend_Plugin_InitAcl extends Zend_Controller_Plugin_Abstract
{
    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $acl = new Zend_Acl();
        
        $contentTypes = FrontZend_Container::get('ContentType')->findAll();

        $modulesDir = new DirectoryIterator(APPLICATION_PATH . '/modules');
        foreach($modulesDir as $moduleDir) {
            if (is_readable($moduleDir->getPathname() . '/configs/acl.ini')) {

                $aclConfig = new Zend_Config_Ini($moduleDir->getPathname()
                        . '/configs/acl.ini', 'production');

                foreach($aclConfig->resources as $resource) {

                    $parent = strstr($resource, '_')
                            ? substr($resource, 0, strrpos($resource, '_'))
                            : null;

                    if ($resource == 'content_content') {
                        foreach($contentTypes as $contentType) {
                            $resourceName = "{$resource}-{$contentType->id}";
                                $acl->addResource($resourceName, 'content');
                        }
                    } elseif($parent == 'content_content') {
                        foreach($contentTypes as $contentType) {
                            $resourceName = "content_content-{$contentType->id}"
                                . str_replace('content_content', '', $resource);
                            $parentName = "content_content-{$contentType->id}";
                            $acl->addResource($resourceName, $parentName);
                        }
                    }
                    $acl->addResource($resource, $parent);
                }

            }
        }

        $layoutPages = FrontZend_Container::get('LayoutPage')->findAll();
        foreach($layoutPages as $page) {
            $resourceName = "frontend_page-{$page->id}";
            $acl->addResource($resourceName, 'frontend');
        }

        $roles = FrontZend_Container::get('AclRole')->findAll(array('order' => 'order'));
        foreach($roles as $role) {
            $acl->addRole($role->id, $role->id_parent);
        }

        $permissions = FrontZend_Container::get('AclPermission')->getTree();
        foreach($permissions as $role => $resources) {
            $acl->allow($role, array_keys($resources));
            $deny = array_diff($acl->getResources(), array_keys($resources));
            if ($deny) {
                $acl->deny($role, $deny);
            }
        }
        $acl->allow('master');
        
        Zend_Registry::set('Zend_Acl', $acl);

        Zend_View_Helper_Navigation_HelperAbstract::setDefaultAcl($acl);
        Zend_View_Helper_Navigation_HelperAbstract::setDefaultRole('guest');
    }
}