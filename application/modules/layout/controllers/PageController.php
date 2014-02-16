<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_PageController extends FrontZend_Module_Controller_Abstract
{
    protected $_modelClass = 'Layout_Model_Page';
    protected $_containerAlias = 'LayoutPage';

    public function listAction()
    {        
        parent::listAction();
        
        $this->view->headTitle()->append('Páginas');
    }

    protected function _getForm()
    {
        $form = parent::_getForm();

        if ($this->_request->getActionName() == 'add') {
            $idContent = $this->_getParam('content');
            $idContentType = $this->_getParam('content-type');

            if ($idContent) {
                $content = FrontZend_Container::get('Content')->findById($idContent);
                $form->populate(array(
                    'page'       => $content->title,
                    'id_content' => $content->id
                ));
            }
            else if ($idContentType) {
                $contentType = FrontZend_Container::get('ContentType')->findById($idContentType);
                $form->populate(array(
                    'page'            => $contentType->type,
                    'id_content_type' => $contentType->id
                ));
            }
        }

        return $form;
    }

    public function addAction()
    {
        parent::addAction();

        $this->view->headTitle()->append('Cadastrar página');
    }

    public function editAction()
    {
        parent::editAction();

        $this->view->headTitle()->append('Editar página');
    }

    public function configAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        $page = FrontZend_Container::get('LayoutPage')->findById($id);

        if (!$page) {
            $this->getHelper('alerts')->addError('Página inválida');
                $this->getHelper('Redirector')
                     ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        $pages = FrontZend_Container::get('LayoutPage')->findAll(array('order' => 'page'));
        $navConfig = array();
        $navConfig[] = array(
            'label' => 'Especial',
            'uri'   => '#',
            'class' => 'nav-header'
        );
        foreach($pages as $p) {
            if ($p->special) {
                $navConfig[] = array(
                    'label'      => $p->page,
                    'module'     => 'layout',
                    'controller' => 'page',
                    'action'     => 'config',
                    'params'     => array(
                        'id' => $p->id
                    ),
                    'active'     => $p->id == $id
                );
            }
        }
        $navConfig[] = array(
            'label' => 'Tipo de conteúdo',
            'uri'   => '#',
            'class' => 'nav-header'
        );
        foreach($pages as $p) {
            if ($p->id_content_type) {
                $navConfig[] = array(
                    'label'      => $p->page,
                    'module'     => 'layout',
                    'controller' => 'page',
                    'action'     => 'config',
                    'params'     => array(
                        'id' => $p->id
                    ),
                    'active'     => $p->id == $id
                );
            }
        }
        $navConfig[] = array(
            'label' => 'Conteúdo',
            'uri'   => '#',
            'class' => 'nav-header'
        );
        foreach($pages as $p) {
            if ($p->id_content) {
                $navConfig[] = array(
                    'label'      => $p->page,
                    'module'     => 'layout',
                    'controller' => 'page',
                    'action'     => 'config',
                    'params'     => array(
                        'id' => $p->id
                    ),
                    'active'     => $p->id == $id
                );
            }
        }

        $pagesNav = new Zend_Navigation($navConfig);

        $directoryIterator = new DirectoryIterator(APPLICATION_PATH . '/modules');
        $blocks = array();
        foreach($directoryIterator as $module) if (!$module->isDot()) {
            if (is_dir($module->getRealPath() . '/forms/Block')) {
                $formsIterator = new DirectoryIterator($module->getRealPath() 
                               . '/forms/Block');
                foreach($formsIterator as $formBlock) if ($formBlock->isFile()) {
                    $blockName = str_replace('.php', '', $formBlock->getFilename());
                    $blockId = strtolower($module->getFilename() . '-' . $blockName);
                    $blocks[$module->getFilename()][$blockId] = $blockName;
                }
            }
        }

        $this->view->page = $page;
        $this->view->pagesNav = $pagesNav;
        $this->view->blocks = $blocks;
        
        $this->view->headTitle()->append('Configurar página');
    }

    public function updateAction()
    {
        $data = $this->_request->getPost();

        if (!$data) {
            $this->getHelper('alerts')->addError('Página inválida');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        $id = $data['id_layout_page'];

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        $updates = 0;

        if (isset($data['areas'])) {
            $areaOrder = 1;
            foreach($data['areas'] as $dataArea) {
                $area = isset($dataArea['id']) && $dataArea['id'] && is_numeric($dataArea['id'])
                    ? FrontZend_Container::get('LayoutBlock')->findById($dataArea['id'])
                    : FrontZend_Container::get('LayoutBlock')->createRow();

                if (!$area->id) {
                    $area->id_layout_page = $id;
                    $area->block = 'layout-area';
                    $area->visible = 1;
                }
                $area->order = $areaOrder++;

                $idArea = FrontZend_Container::get('LayoutBlock')->save($area);
                if ($idArea) {
                    $updates++;

                    if (isset($dataArea['columns']) && $dataArea['columns']) {
                        $columnOrder = 1;
                        foreach($dataArea['columns'] as $dataColumn) {
                            $column = isset($dataColumn['id']) && $dataColumn['id'] && is_numeric($dataColumn['id'])
                                ? FrontZend_Container::get('LayoutBlock')->findById($dataColumn['id'])
                                : FrontZend_Container::get('LayoutBlock')->createRow();

                            if (!$column->id) {
                                $column->id_layout_page = $id;
                                $column->block = 'layout-column';
                                $column->visible = 1;
                                $column->id_wrapper = $idArea;
                            }
                            $column->setOptions(array('class' => $dataColumn['class']));
                            $column->order = $columnOrder++;

                            $idColumn = FrontZend_Container::get('LayoutBlock')->save($column);
                            if ($idColumn) {
                                $updates++;

                                if (isset($dataColumn['blocks']) && $dataColumn['blocks']) {
                                    foreach($dataColumn['blocks'] as $orderBlock => $idBlock) {
                                        if (is_numeric($idBlock)) {
                                            $block = FrontZend_Container::get('LayoutBlock')->findById($idBlock);
                                        } else {
                                            $block = FrontZend_Container::get('LayoutBlock')->createRow();
                                            $block->id_layout_page = $id;
                                            $block->block = $idBlock;
                                            $block->visible = 0;
                                        }
                                        $block->id_wrapper = $idColumn;
                                        $block->order = $orderBlock+1;

                                        if (FrontZend_Container::get('LayoutBlock')->save($block)) {
                                            $updates++;
                                        }
                                    }
                                }

                            }  // save column

                        }
                    }
                } // save area
            }
        }
        if (isset($data['remove_blocks']) && $data['remove_blocks']) {
            $blocks = explode(',', $data['remove_blocks']);
            foreach($blocks as $blockId) {
                $block = FrontZend_Container::get('LayoutBlock')->findById($blockId);
                if ($block->delete()) {
                    $updates++;
                }
            }
        }

        FrontZend_Container::get('LayoutBlock')->deleteEmptyAreas();

        if ($updates) {
            $this->getHelper('alerts')
                 ->addSuccess('Página atualizada com sucesso');
        } else {
            $this->getHelper('alerts')
                 ->addInfo('Nenhuma alteração foi feita nesta página');
        }

        $this->getHelper('Redirector')
             ->gotoUrlAndExit(ADMIN_ROUTE . "/layout/page/config/id/{$id}");
    }

    public function removeAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        $page = FrontZend_Container::get('LayoutPage')->findById($id);

        if (!$page) {
            $this->getHelper('alerts')->addError('Página inválida');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
        }

        try {
            if ($page->delete()) {
                $this->getHelper('alerts')
                     ->addSuccess('Página excluída com sucesso');
            } else {
                $this->getHelper('alerts')
                     ->addError('Erro ao tentar excluír página');
            }
        } catch(Exception $e) {
            $this->getHelper('alerts')
                 ->addError('Erro ao tentar excluir página: ' . $e->getMessage());
        }

        $this->getHelper('Redirector')
             ->gotoUrlAndExit(ADMIN_ROUTE . '/layout/page/list');
    }

}