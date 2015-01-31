<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_CodeController extends FrontZend_Module_Controller_Abstract
{
    public function listAction()
    {
        $this->view->headTitle()->append('Editor de layout');
        
        $theme = Acl_Model_Auth::getTheme();
        $path = str_replace('|', '/', $this->_getParam('path', $theme));
        $layout = substr($path, 0, strpos($path, '/'));
        $basepath = APPLICATION_PATH . "/layouts/frontend/{$layout}";

//        $tree = scandir(APPLICATION_PATH . '/layouts/frontend');
//        $tree = array_flip($tree);
//        unset($tree['.'], $tree['..']);
//
//        foreach($tree as $dir => $n) {
//            $directoryTree = $this->_directoryTree(APPLICATION_PATH
//                   . '/layouts/frontend' . DIRECTORY_SEPARATOR . $dir, $path);
//
//            $tree[$dir] = new Zend_Navigation($directoryTree);
//        }

        $directoryTree = $this->_directoryTree(APPLICATION_PATH
                   . '/layouts/', $path);
        $tree = new Zend_Navigation($directoryTree);

        $form = new Layout_Form_Code();

        if ($this->_request->isPost()) {
            if ($form->isValid($this->_request->getPost())) {
                if ($form->persistData()) {
                    $this->getHelper('alerts')->addError(
                        'Arquivo atualizado com sucesso');
                }
            } else {
                $this->getHelper('alerts')->addError(
                    'Erro ao tentar alterar arquivo');
            }
        }

        $form->populate(array('path' => $path));
        if (!$form->getElement('code')->getLabel()) {
            $form = '';
        }

        $this->view->layout = $layout;
        $this->view->tree = $tree;
        $this->view->form = $form;
    }

    private function _directoryTree($path, $currentPath=null)
    {
        $di = new DirectoryIterator($path);
        $dirs = array();
        foreach($di as $dir) {
            $dirPath = ltrim(str_replace(APPLICATION_PATH . '/layouts', '',
                $dir->getPathname()), '/\\');


            $uri = $this->view->url(array(
                    'action' => 'list',
                    'path'   => str_replace(array(
                        APPLICATION_PATH . '/layouts/',
                        DIRECTORY_SEPARATOR), array('', '|'), $dirPath)
                ));

            if($dir->isDir() && !$dir->isDot() && substr($dir->getFilename(),0,1) != '.' 
                    && !in_array($dir->getFilename(), array('img', 'tmp'))) {
                $dirs[$dir->getFilename()] = array(
                    'label'  => $dir->getFilename(),
                    'uri'    => $uri,
                    'active' => $dirPath == str_replace('/', DIRECTORY_SEPARATOR, $currentPath),
                    'pages'  => $this->_directoryTree($dir->getPathname(), $currentPath)
                );
            } else if ($dir->isFile() && $dir->getFilename() != 'theme.ini') {

                $dirs[$dir->getFilename()] = array(
                    'label'  => $dir->getFilename(),
                    'uri'    => $uri,
                    'active' => $dirPath == str_replace('/', DIRECTORY_SEPARATOR, $currentPath)
                );
            }
        }
//        ksort($dirs);
        return $dirs;
    }

}