<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_UserController extends Zend_Controller_Action
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
        $this->_forward('list');
    }

    public function listAction()
    {
        $form = new Acl_Form_Filter_User();
        $form->setAction($this->view->url());

        $filters = $this->_getParam('filter');
        if ($filters) {
            $form->populate($filters);
        }
        $paginator = $form->search($this->_getParam('p', 1), 25, 10);

        $this->view->paginator = $paginator;
        $this->view->form = $form;
    }

    public function addAction()
    {
        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')
                ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/user');
        }

        $form = new Acl_Form_User();
        $form->setAction($this->view->url());

        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($form->isValid($data)) {
                try {
                    $id = $form->persistData();
                    if ($id) {
                        $this->getHelper('alerts')
                            ->addSuccess('Usuário cadastrado com sucesso');
                        if ($data['apply']) {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/acl/user/edit/id/{$id}");
                        } else {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . '/acl/user/list');
                        }
                    } else {
                        $this->getHelper('alerts')
                            ->addError('Erro ao cadastrar usuário');
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                        ->addError('Erro ao cadastrar usuário: ' .
                                   $e->getMessage());
                }
            } else {
                $this->getHelper('alerts')
                    ->addError('Erro ao cadastrar usuário: ' .
                               'dados inválidos ou faltando');
            }
        }

        $this->view->form = $form;
    }

    public function editAction()
    {
        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')
                ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/user');
        }

        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/user');
        }

        $user = FrontZend_Container::get('AclUser')->findById($id);

        if (!$user) {
            $this->getHelper('alerts')->addError('Usuário inválido');
            $this->getHelper('Redirector')
                ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/user');
        }

        $form = new Acl_Form_User(array('edit' => true));
        $form->setAction($this->view->url());
        $form->populate($user->toArray(true));

        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($form->isValid($data)) {
                try {
                    $id = $form->persistData();
                    if ($id) {
                        $this->getHelper('alerts')
                            ->addSuccess('Usuário alterado com sucesso');
                        if ($data['apply']) {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/acl/user/edit/id/{$id}");
                        } else {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . '/acl/user');
                        }
                    } else {
                        $this->getHelper('alerts')
                            ->addError('Erro ao editar usuário');
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                        ->addError('Erro ao editar usuário: ' .
                                   $e->getMessage());
                }
            } else {
                $this->getHelper('alerts')
                    ->addError('Erro ao editar usuário: ' .
                               'dados inválidos ou faltando');
            }
        }

        $this->view->form = $form;
    }

    public function removeAction()
    {
        if ($this->_getParam('cancel')) {
            $this->_redirect(ADMIN_ROUTE . '/acl/user');
        }

        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/acl/user');
        }

        $data = $this->_request->getPost();
        if ($data) {
            try {
                if(isset($data['delete'])) {
                    if (FrontZend_Container::get('AclUser')->deleteById($id)) {
                        $this->getHelper('alerts')
                             ->addSuccess('Usuário excluído com sucesso');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . '/acl/user');
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar excluir usuário');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/acl/user/remove/id/{$id}");
                    }
                } else if(isset($data['trash'])) {
                    $content = FrontZend_Container::get('AclUser')->findById($id);
                    $content->status = 'D';
                    if (FrontZend_Container::get('AclUser')->save($content)) {
                        $this->getHelper('alerts')
                             ->addSuccess('Usuário enviado para a lixeira ' .
                                          'com sucesso');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . '/acl/user');
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar enviar usuário para ' .
                                        'a lixeira');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/acl/user/remove/id/{$id}");
                    }
                } else if(isset($data['restore'])) {
                    $content = FrontZend_Container::get('AclUser')->findById($id);
                    $content->status = 'I';
                    if (FrontZend_Container::get('AclUser')->save($content)) {
                        $this->getHelper('alerts')
                             ->addSuccess('O usuário foi restaurado sucesso, '
                                 . 'porém ainda está inativo');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . '/acl/user');
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar restaurar usuário');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/acl/user/remove/id/{$id}");
                    }
                }
            } catch(Exception $e) {
                $this->getHelper('alerts')
                     ->addError('Erro ao tentar excluir usuário: ' .
                                $e->getMessage());
                $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . "/acl/user/remove/id/{$id}");
            }
        }

        $this->view->user = FrontZend_Container::get('AclUser')->findById($id);
    }

}
