<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_BlockController extends Zend_Controller_Action
{
    public function init()
    {
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        $this->_helper->acl("{$module}_{$controller}_{$action}");
    }

    public function ajaxToggleAction()
    {
//        $this->_helper->acl->verifyAjax('layout', 'config', 'Permissão negada');

        $id = $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $block = FrontZend_Container::get('LayoutBlock')->findById($id);

        if (!$block) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Bloco inválido'
            ));
        }
        
        try {
            $block->toggle();
            FrontZend_Container::get('LayoutBlock')->save($block);
        } catch(FrontZend_Exception $e) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => $e->getMessage()
            ));
        } catch(Exception $e) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Ocorreu um erro'
            ));
        }

        $this->_helper->json(array(
            'status'  => 1,
            'vars'    => array(
                'visible' => $block->visible
            )
        ));
    }

    public function ajaxSetupAction()
    {
//        $this->_helper->acl->verifyAjax('layout', 'config', 'Permissão negada');

        $values = $this->_request->getPost();
        $id = isset($values['id_layout_block'])
            ? $values['id_layout_block']
            : $this->_getParam('id');

        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $block = FrontZend_Container::get('LayoutBlock')->findById($id);

        if (!$block) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Bloco inválido'
            ));
        }

        $form = Layout_Form_Block::factory($block);
        $this->view->form = $form;

        if ($values) {
            try {
                if ($form->isValid($values)) {
                    if ($form->persistData()) {
                        $this->_helper->json(array(
                            'status' => 1,
                            'msg'    => 'Configurações do bloco alteradas com sucesso'
                        ));
                    } else {
                        $this->_helper->json(array(
                            'status'  => 0,
                            'content' => $this->view->render("block/ajax-setup.phtml"),
                            'msg'     => 'Erro ao tentar salvar configurações do bloco'
                        ));
                    }
                } else {
                    $this->_helper->json(array(
                        'status'  => 0,
                        'content' => $this->view->render("block/ajax-setup.phtml"),
                        'msg'     => 'Dados inválidos ou faltando'
                    ));
                }

            } catch(FrontZend_Exception $e) {
                $this->_helper->json(array(
                    'status' => 0,
                    'msg'    => $e->getMessage()
                ));
            } catch(Exception $e) {
                $this->_helper->json(array(
                    'status'  => 0,
                    'msg'     => 'Ocorreu um erro',
                    'console' => $e->getTraceAsString()
                ));
            }
        }

        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render("block/ajax-setup.phtml")
        ));
    }

}

