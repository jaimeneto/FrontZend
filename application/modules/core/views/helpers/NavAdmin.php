<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_View_Helper_HtmlElement
 */
require_once 'Zend/View/Helper/HtmlElement.php';

class Core_View_Helper_NavAdmin extends Zend_View_Helper_HtmlElement
{
    //put your code here

    public function navAdmin()
    {
        if (!Acl_Model_Auth::isLoggedIn()) {
            return;
        }
        
        $navConfigFiles = array();
        $modulesDir = new DirectoryIterator(APPLICATION_PATH . '/modules');
        foreach($modulesDir as $dir) {
            if($dir->isDir() && !$dir->isDot()) {
                $pathname = $dir->getPathname();
                if (file_exists("{$pathname}/configs/navigation.ini")) {
                    $navConfigFiles[] = "{$pathname}/configs/navigation.ini";
                }
            }
        }

        $navConfigs = array();
        if ($navConfigFiles) {
            foreach($navConfigFiles as $navConfigFile) {
                $config = new Zend_Config_Ini($navConfigFile, 'admin');
                $navConfigs += $config->toArray();
            }
        }

        $navConfigs = array(
            'home' => array(
                'id'       => 'home',
                'label'    => 'Início',
                'uri'      => ADMIN_ROUTE,
                'pages'    => $navConfigs
            )
        );
        
        $request = Zend_Controller_Front::getInstance()->getRequest();
        $activeUrls = array(ADMIN_ROUTE . '/' . $request->getModuleName());
        $activeUrls[1] = $activeUrls[0] . '/' . $request->getControllerName();
        $activeUrls[2] = $activeUrls[1] . '/' . $request->getActionName();
        
        $navAdmin = new Zend_Navigation($navConfigs);
        $baseUrl = $this->view->baseUrl('/');
        $active = false;
        foreach ($navAdmin->findById('home')->getPages() as $page) {
            $href = str_replace($baseUrl, '', $page->getHref());
            if (in_array($page->getHref(), $activeUrls)) {
                $page->setActive(true);
            }
            foreach($page->getPages() as $subpage) {
                $href = str_replace($baseUrl, '', $subpage->getHref());
                if (in_array($href, $activeUrls)) {
                    $subpage->setActive(true);
                    break;
                }
            }
            if ($active) break;
        }
        
        Zend_Registry::set('Zend_Navigation', $navAdmin);
        
        $role = Zend_Auth::getInstance()->getIdentity()->id_role;
        $this->view->navigation()->setRole($role);

        return $this->view->navbar($navAdmin->findById('home'));
    }
}