<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_ThemeController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->acl('layout_theme');
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $this->view->headTitle()->append('Temas');
        
        $themesList = FrontZend_Container::get('LayoutTheme')->fetchAll();

        $themes = array(
            'frontend' => array(),
            'backend' => array(),
        );
        if ($themesList) {
            foreach($themesList as $theme) {
                $themes[$theme->env][$theme->theme] = $theme;
            }
        }

        $toInstall = array();
        $directoryIterator = new DirectoryIterator(APPLICATION_PATH . '/layouts');
        foreach($directoryIterator as $environment) {
            if ($environment->isDir() && !$environment->isDot()) {
                $env = $environment->getFilename();
                $envIterator = new DirectoryIterator($environment->getPathname());
                foreach($envIterator as $theme) {
                    $cfgPath = $theme->getPathname() . '/theme.ini';
                    if ($theme->isDir() && !$theme->isDot()
                        && !isset($themes[$env][$theme->getFilename()])
                        && file_exists($cfgPath)) {
                        $themeCfg = new Zend_Config_Ini($cfgPath);
                        $toInstall[$env][$theme->getFilename()] = 
                            (object) $themeCfg->toArray();
                    }
                }
            }
        }
        
        $this->view->themes = $themes;
        $this->view->toInstall = $toInstall;
    }

    public function installAction()
    {
        $this->view->headTitle()->append('Instalar tema');
        
        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/theme');
        }

        $env = $this->_getParam('env');
        $theme = $this->_getParam('theme');

        if (!$env || !$theme) {
            $this->getHelper('alerts')->addError('Dados inválidos');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/theme');
        }

        $cfgPath = APPLICATION_PATH . "/layouts/{$env}/{$theme}/theme.ini";
        if (!file_exists($cfgPath)) {
            $this->getHelper('alerts')->addError('Caminho inválido: ');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/theme');
        }

        $config = new Zend_Config_Ini($cfgPath);
        $layout = (object) $config->toArray();
        $layout->theme = $theme;

        $form = new Layout_Form_Theme();
        $form->setAction($this->view->url());
        $form->populate(array(
            'env'   => $env,
            'theme' => $theme
        ));

        $data = $this->_request->getPost();
        if ($data) {
            try {
                if ($form->isValid($data)) {
                    if ($form->persistData()) {
                        $this->getHelper('alerts')->addSuccess(
                            'Tema instalado com sucesso');
                        $this->getHelper('Redirector')
                            ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/theme');
                    }
                } else {
                    $this->getHelper('alerts')
                        ->addError('Dados inválidos ou faltando');
                }
            } catch(Exception $e) {
                $this->getHelper('alerts')->addError(
                    'Erro ao tentar instalar tema: ' . $e->getMessage());
            }
        }

        $this->view->layout = $layout;
        $this->view->form = $form;
    }

    public function ajaxActivateAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $theme = FrontZend_Container::get('LayoutTheme')->findById($id);

        if (!$theme) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Tema inválido'
            ));
        }

        try {
            FrontZend_Container::get('LayoutTheme')->activate($theme);
            if ($theme->env == 'frontend') {
                Zend_Auth::getInstance()->getIdentity()->theme = $theme->theme;
            }
        } catch(Exception $e) {
            $this->_helper->json(array(
                'status'  => 0,
                'msg'     => 'Erro ao tentar ativar tema',
                'console' => $e->getMessage()
            ));
        }

        $this->_helper->json(array(
            'status'  => 1
        ));
    }

    public function ajaxTestAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $theme = FrontZend_Container::get('LayoutTheme')->findById($id);

        if (!$theme || $theme->env != 'frontend') {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Tema inválido'
            ));
        }

        if (Zend_Auth::getInstance()->getIdentity()->theme == $theme->theme) {
            $activeTheme = FrontZend_Container::get('LayoutTheme')->findActive();
            Zend_Auth::getInstance()->getIdentity()->theme = $activeTheme->theme;
        } else {
            Zend_Auth::getInstance()->getIdentity()->theme = $theme->theme;
        }

        $this->_helper->json(array(
            'status' => 1,
            'active' => Zend_Auth::getInstance()->getIdentity()->theme == $theme->theme
        ));
    }

}