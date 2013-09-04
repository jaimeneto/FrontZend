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

        $navAdmin = new Zend_Navigation($navConfigs);

        $role = Zend_Auth::getInstance()->getIdentity()->id_role;
        $this->view->navigation()->setRole($role);

        return $this->view->navigation()->menu()->renderNavbar($navAdmin);
    }
}