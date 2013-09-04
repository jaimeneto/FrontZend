<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_NavController extends Zend_Controller_Action
{
    public function init()
    {
        $this->_helper->acl('layout_nav');
    }

    public function indexAction()
    {
        $this->_forward('manage');
    }

    public function manageAction()
    {
        $this->view->headTitle()->append('Menus');

        $navPages = FrontZend_Container::get('LayoutNav')->fetchAll(null,
                        array('id_parent', 'order ASC'));

        $navConfigs = $this->_getNavTree($navPages);

        $this->view->navConfigs = $navConfigs;
    }

    protected function _getNavTree($navPages, $parent=null)
    {
        $navConfigs = array();
        foreach($navPages as $navPage) {
            if ($navPage->id_parent == $parent) {
                $idNav = "layout_nav_{$navPage->id}";
                $navConfigs[$idNav] = (object) $navPage->toArray();
                $navConfigs[$idNav]->id = $navPage->id;
                if ($navPage->attribs) {
                    $navPageAttribs = Zend_Json::decode($navPage->attribs);
                    foreach($navPageAttribs as $attr => $val) {
                        $navConfigs[$idNav]->$attr = $val;
                    }
                    unset($navConfigs[$idNav]->attribs);
                }
                $pages = $this->_getNavTree(clone $navPages, $navPage->id);
                if ($pages) {
                    $navConfigs[$idNav]->pages = $pages;
                }
            }
        }

        return $navConfigs;
    }

    public function ajaxAddAction()
    {
        $id_parent = $this->_getParam('id_parent');
        $order = $this->_getParam('order');

        $form = new Layout_Form_Nav();
        $form->removeElement('id_layout_nav');

        if ($id_parent) {
            $form->populate(array('id_parent' => $id_parent));
        }
        if ($order) {
            $form->populate(array('order' => $order));
        }

        $this->view->form = $form;

        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render('nav/modal-form.phtml')
        ));
    }

    public function ajaxEditAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $nav = FrontZend_Container::get('LayoutNav')->findById($id);

        if (!$nav) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Item de menu inválido'
            ));
        }

        $form = new Layout_Form_Nav();
        $form->populate($nav->toArray());

        $this->view->form = $form;

        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render('nav/modal-form.phtml')
        ));
    }

    public function ajaxSaveAction()
    {
        $form = new Layout_Form_Nav();

        $data = $this->getRequest()->getPost();
        if ($data) {
            if (!isset($data['id_layout_nav'])) {
                $form->removeElement('id_layout_nav');
            }
            if ($form->isValid($data)) {
                try {
                    $id = $form->persistData();
                    if ($id) {
                        $nav = FrontZend_Container::get('LayoutNav')->findById($id);
                        $this->view->nav = $nav;
                        $json = array(
                            'status'  => 1,
                            'msg'     => 'Item de menu cadastrado com sucesso',
                            'data'    => $nav->toArray(),
                            'content' =>
                                $this->view->render('nav/ajax-save.phtml'),
                        );
                    } else {
                        $json = array(
                            'status' => 0,
                            'msg'    => 'Erro ao tentar cadastrar item de menu'
                        );
                    }
                } catch(Exception $e) {
                    $json = array(
                        'status'  => 0,
                        'msg'     => 'Erro ao tentar cadastrar item de menu',
                        'console' => $e->getMessage()
                    );
                }
            } else {
                $json = array(
                    'status' => 0,
                    'msg'    => 'Erro ao tentar cadastrar item de menu: ' .
                               'dados inválidos ou faltando'
                );
            }
        }

        $this->_helper->json($json);
    }

    public function ajaxRemoveAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        try {
            $where = array('id_layout_nav = ?' => $id);
            if (FrontZend_Container::get('LayoutNav')->delete($where)) {
                $this->_helper->json(array(
                    'status' => 1,
                    'mag'    => 'Item de menu excluído com sucesso'
                ));
            }
        } catch(Exception $e) {
            $this->_helper->json(array(
                'status'  => 0,
                'msg'     => 'Erro ao excluir item de menu',
                'console' => $e->getMessage()
            ));
        }
    }

    public function ajaxToggleAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $nav = FrontZend_Container::get('LayoutNav')->findById($id);

        if (!$nav) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Item de menu inválido'
            ));
        }

        try {
            $nav->visible = $nav->visible == 1 ? 0 : 1;
            if (FrontZend_Container::get('LayoutNav')->save($nav)) {
                $this->_helper->json(array(
                    'status' => 1,
                    'data'   => array(
                        'visible' => $nav->visible
                    )
                ));
            }
        } catch(Exception $e) {
            $this->_helper->json(array(
                'status'  => 0,
                'msg'     => 'Erro ao alterar visibilidade de item de menu',
                'console' => $e->getMessage()
            ));
        }
    }

    public function ajaxMoveAction()
    {
        $data = $this->getRequest()->getPost();
        if ($data) {
            try {
                $nav = FrontZend_Container::get('LayoutNav')->findById($data['id_layout_nav']);
                unset($data['id_layout_nav']);
                $nav->setFromArray($data);

                if (FrontZend_Container::get('LayoutNav')->save($nav)) {
                    $json = array(
                        'status'  => 1,
                        'msg'     => 'Item de menu movido com sucesso',
                    );
                } else {
                    $json = array(
                        'status' => 0,
                        'msg'    => 'Erro ao tentar mover item de menu'
                    );
                }
            } catch(Exception $e) {
                $json = array(
                    'status'  => 0,
                    'msg'     => 'Erro ao tentar mover item de menu',
                    'console' => $e->getMessage()
                );
            }
        } else {
            $json = array(
                'status' => 0,
                'msg'    => 'Dados inválidos'
            );
        }

        $this->_helper->json($json);
    }

}