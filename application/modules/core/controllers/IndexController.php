<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        $theme = Acl_Model_Auth::getTheme();
        $layoutPath = APPLICATION_PATH . "/layouts/frontend/{$theme}/scripts";
        $this->getHelper('layout')->setLayoutPath($layoutPath);

        $modulesDir = new DirectoryIterator(APPLICATION_PATH . '/modules');
        foreach($modulesDir as $dir) {
            if (is_dir($dir->getPathname() . '/views/blocks')) {
                $this->view->addScriptPath($dir->getPathname() . '/views');
            }
        }
        $this->view->addScriptPath($layoutPath);

        if (Zend_Auth::getInstance()->hasIdentity()) {
            $this->view->user = Zend_Auth::getInstance()->getIdentity();
        }
        
        $nav = FrontZend_Container::get('LayoutNav')->generateContainer();
        Zend_Registry::set('Zend_Navigation', $nav);
    }

    public function indexAction()
    {
        $slug = $this->_getParam('slug');

        $page = null;
        $content = null;

        $themeName = Acl_Model_Auth::getTheme();
        $theme = FrontZend_Container::get('LayoutTheme')
                    ->findByName($themeName, 'frontend');
        $themeId = $theme->getId();
        
        if ($slug) {
            $content = FrontZend_Container::get('Content')->findBySlug($slug);
            if ($content) {
                $page = FrontZend_Container::get('LayoutPage')
                            ->findByContent($content, true, $themeId);
            }
        } else {
            $page = FrontZend_Container::get('LayoutPage')
                        ->findSpecial('home', $themeId);
        }

        if (!$page) {
            $this->getHelper('Redirector')->gotoUrlAndExit('error');
        }

        $this->_helper->acl("frontend_page-{$page->id}");
        
        $this->view->page = $page;
        $this->view->content = $content;
    }

}

