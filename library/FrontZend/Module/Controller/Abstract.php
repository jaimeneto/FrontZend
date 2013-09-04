<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class FrontZend_Module_Controller_Abstract
    extends Zend_Controller_Action
{
    protected $_modelClass = null;
    protected $_containerAlias = null;

    public function init()
    {       
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        $this->_helper->acl("{$module}_{$controller}_{$action}");
    }

    public function __construct(Zend_Controller_Request_Abstract $request,
        Zend_Controller_Response_Abstract $response,
        array $invokeArgs = array())
    {
        if (!$this->_modelClass) {
            $this->_modelClass = str_replace(
                array('Controller', '_'),
                array('', '_Model_'),
                get_class($this)
            );
        }

        parent::__construct($request, $response, $invokeArgs);
    }

    public function indexAction()
    {
        $this->_forward('list');
    }

    public function listAction()
    {
        $formFilterClass = str_replace('_Model_', '_Form_Filter_',
                $this->_modelClass);

        $form = new $formFilterClass();
        $form->setAction($this->view->url());

        $filters = $this->_getParam('filter');
        if ($filters) {
            $form->populate($filters);
        }
        $paginator = $form->search($this->_getParam('p', 1), 25, 10);

        $this->view->paginator = $paginator;
        $this->view->form = $form;

        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = $this->getRequest()->getControllerName();
        if (!file_exists(APPLICATION_PATH . "/modules/{$moduleName}/views/"
                . "scripts/{$controllerName}/list.phtml")) {
            $this->view->moduleName = $moduleName;
            $this->view->controllerName = $controllerName;
            $this->getHelper('ViewRenderer')->setNoController();
            $this->view->addScriptPath(realpath(dirname(__FILE__) . '/../'
                . 'views/scripts/abstract/'));
        }
    }

    protected function _getForm()
    {
        $formClass = str_replace('_Model_', '_Form_', $this->_modelClass);
        $form = new $formClass();
        $form->setAction($this->view->url());
        return $form;
    }

    public function addAction()
    {
        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = strtolower($this->getRequest()->getControllerName());

        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $form = $this->_getForm();

        $containerAlias = $this->_containerAlias
            ? $this->_containerAlias
            : substr($this->_modelClass, strrpos($this->_modelClass, '_')+1);
        $table = FrontZend_Container::get($containerAlias);
        $model = $table->createRow();
        $id_model = $model->getIdField();

        if ($form->getElement($id_model)) {
            $form->removeElement($id_model);
        }
        
        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($form->isValid($data)) {
                try {
                    $id = $form->persistData();
                    if ($id) {
                        $this->getHelper('alerts')
                             ->addSuccess('Cadastro efetuado com sucesso');
                        if (isset($data['apply'])) {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/{$moduleName}/{$controllerName}"
                                    . "/edit/id/{$id}");
                        } else {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
                        }
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar cadastrar');
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                         ->addError('Erro ao tentar cadastrar: ' .
                                   $e->getMessage());
                }
            } else {
                $this->getHelper('alerts')
                     ->addError('Erro ao tentar cadastrar: ' .
                               'dados inválidos ou faltando');
            }
        }

        $this->view->form = $form;

        if (!file_exists(APPLICATION_PATH . "/modules/{$moduleName}/views/"
                    . "scripts/{$controllerName}/add.phtml")) {
            $this->view->controllerName = $controllerName;
            $this->getHelper('ViewRenderer')->setNoController();
            $this->view->addScriptPath(realpath(dirname(__FILE__)
                    . '/../views/scripts/abstract/'));
        }
    }

    public function editAction()
    {
        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = $this->getRequest()->getControllerName();

        if ($this->_getParam('cancel')) {
            $this->_redirect(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $cacheAlias = str_replace('_Model_', '', $this->_modelClass);
        $model = FrontZend_Container::get($cacheAlias)->findById($id);

        if (!$model) {
            $this->getHelper('alerts')->addError('Registro inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $form = $this->_getForm();
        $form->populate($model->toArray(true));

        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($form->isValid($data)) {
                try {
                    if ($form->persistData()) {
                        $this->getHelper('alerts')
                             ->addSuccess('Alteração feita com sucesso');
                        if ($data['apply']) {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/{$moduleName}/{$controllerName}"
                                    . "/edit/id/{$id}");
                        } else {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
                        }
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar alterar dados: ' .
                                       'dados inválidos ou faltando');
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                         ->addError('Erro ao tentar alterar dados: ' .
                                   $e->getMessage());
                }
            } else {
                $this->getHelper('alerts')
                     ->addError('Erro ao tentar alterar dados: ' .
                               'dados inválidos ou faltando');
            }
        }

        $this->view->form = $form;
        
        if (!file_exists(APPLICATION_PATH . "/modules/{$moduleName}/views/"
                    . "scripts/{$controllerName}/edit.phtml")) {
            $this->view->controllerName = $controllerName;
            $this->getHelper('ViewRenderer')->setNoController();
            $this->view->addScriptPath(realpath(dirname(__FILE__)
                    . '/../views/scripts/abstract/'));
        }
    }

    public function removeAction()
    {
        $moduleName = $this->getRequest()->getModuleName();
        $controllerName = strtolower($this->getRequest()->getControllerName());

        if ($this->_getParam('cancel')) {
            $this->_redirect(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
        }

        $containerAlias = $this->_containerAlias
            ? $this->_containerAlias
            : substr($this->_modelClass, strrpos($this->_modelClass, '_')+1);

        $data = $this->_request->getPost();
        if ($data) {
            try {
                if(isset($data['delete'])) {
                    if (Container::get($containerAlias)->deleteById($id)) {
                        $this->getHelper('alerts')
                             ->addSuccess('Item excluído com sucesso');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar excluir item');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}/remove/id/{$id}");
                    }
                } else if(isset($data['trash'])) {
                    $content = FrontZend_Container::get($containerAlias)->findById($id);
                    $content->status = 'D';
                    if (Container::get($containerAlias)->save($content)) {
                        $this->getHelper('alerts')
                             ->addSuccess('Item enviado para a lixeira com sucesso');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar enviar item para a lixeira');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}/remove/id/{$id}");
                    }
                } else if(isset($data['restore'])) {
                    $content = FrontZend_Container::get($containerAlias)->findById($id);
                    $content->status = 'I';
                    if (Container::get($containerAlias)->save($content)) {
                        $this->getHelper('alerts')
                             ->addSuccess('O item foi restaurado sucesso, '
                                 . 'porém ainda está inativo');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}");
                    } else {
                        $this->getHelper('alerts')
                             ->addError('Erro ao tentar restaurar item');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/{$moduleName}/{$controllerName}/remove/id/{$id}");
                    }
                }
            } catch(Exception $e) {
                $this->getHelper('alerts')
                     ->addError('Erro ao tentar excluir item: ' . $e->getMessage());
                $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . "/{$moduleName}/{$controllerName}/remove/id/{$id}");
            }
        }

        $this->view->content = FrontZend_Container::get($containerAlias)->findById($id);
    }

}

