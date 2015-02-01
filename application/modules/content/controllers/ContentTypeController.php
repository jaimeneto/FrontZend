<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_ContentTypeController extends FrontZend_Module_Controller_Abstract
{
    
    public function listAction()
    {
        $this->view->headTitle()->append('Tipos de conteúdo');
        
        parent::listAction();
    }
    
    public function addAction()
    {
        $this->view->headTitle()->append('Cadastrar tipo de conteúdo');
        
        parent::addAction();
    }
    
    public function editAction()
    {
        $this->view->headTitle()->append('Editar tipo de conteúdo');
        
        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . '/content/content-type');
        }

        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . '/content/content-type');
        }

        $contentType = FrontZend_Container::get('ContentType')->findById($id);

        if (!$contentType) {
            $this->getHelper('alerts')->addError('Tipo de conteúdo inválido');
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . '/content/content-type');
        }

        $form = new Content_Form_ContentType(array('edit' => true));
        $form->setAction($this->view->url());

        if ($contentType->id_parent) {
            $form->populate(
                $contentType->toArray() +
                array('parent' => $contentType->getParent()->type)
            );
        } else {
            $form->removeElement('parent');
            $form->populate($contentType->toArray());
        }

        $data = $this->_request->getPost();
        if ($data) {
            $successes = $errors = 0;

            $data['id_content_type'] = $contentType->id;
            if (isset($data['parent'])) {
                $data['parent'] = $contentType->getParent()->type;
            }
            if ($form->isValid($data)) {
                try {
                    if ($form->persistData()) {
                        $msg = 'Tipo de conteúdo alterado com sucesso';
                        $this->getHelper('alerts')->addSuccess($msg);
                        $successes++;
                    }
                } catch (Exception $e) {
                    $msg = 'Erro ao tentar alterar tipo de conteúdo: ' 
                         . $e->getMessage();
                    $this->getHelper('alerts')->addError($msg);
                    $errors++;
                }
            }

            // Salva configurações dos campos extras, relacionamentos e arquivos
            if (isset($data['meta'])) {
                if ($form->persistMeta()) {
                    if (!$successes) {
                        $msg = 'Meta campos, relacionamentos e arquivos '
                             . 'atualizados com sucesso';
                    }
                    $this->getHelper('alerts')->addSuccess($msg);
                }
            }
        }

        $this->view->contentType = $contentType;
        $this->view->form = $form;
    }

    public function removeAction()
    {
        $this->view->headTitle()->append('Excluir tipo de conteúdo');
        
        parent::removeAction();
    }
    
    public function configPageAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . '/content/content-type');
        }

        $contentType = FrontZend_Container::get('ContentType')->findById($id);

        if (!$contentType) {
            $this->getHelper('alerts')->addError('Tipo de conteúdo inválido');
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . '/content/content-type');
        }

        $page = $contentType->getLayoutPage();

        if ($page) {
            $url = ADMIN_ROUTE . '/layout/page/config/id/' . $page->id;
        } else {
            $this->getHelper('alerts')->addAlert('Ainda não foi foi criada'
                . ' uma página para esse tipo de conteúdo');
            $url = ADMIN_ROUTE . "/layout/page/add/content-type/{$id}";
        }

        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }

    public function ajaxAddMetafieldAction()
    {
        $idContentType = $this->_getParam('type');
        $datatype      = $this->_getParam('datatype');
        $fieldtype     = $this->_getParam('fieldtype');
        $name          = $this->_getParam('name');

        if (!$name) {
            $this->_helper->json(array(
                'status' => 0,
                'msg' => 'Você deve definir um nome para o campo'
            ));
        }

        if (!$fieldtype) {
            $this->_helper->json(array(
                'status' => 0,
                'msg' => 'Você deve selecionar o tipo de campo para o formulário'
            ));
        }

        $contentType = FrontZend_Container::get('ContentType')->findById($idContentType);
        
        $filterSlug = new FrontZend_Filter_Slug();
        $fieldname = $filterSlug->filter($name);

        $metafields = $contentType->getMetafields();

        if (array_key_exists($fieldname, $metafields)) {
            $this->_helper->json(array(
                'status' => 0,
                'msg' => "Já existe um metacampo '{$fieldname}'"
                       . " para o tipo '{$idContentType}'"
            ));
        }

        $metafield = FrontZend_Container::get('Metafield')->createRow(array(
            'id_content_type' => $idContentType,
            'datatype'        => $datatype,
            'fieldname'       => $fieldname,
            'options'         => Zend_Json::encode(array(
                'label' => ucfirst($name),
                'type'  => $fieldtype
            ))
        ));

        $this->view->metafield    = $metafield;
        $this->view->collapsed    = false;

        $template = "content-type/form/field.phtml";
        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render($template),
            'vars'    => array(
                'field' => $filterSlug->filter($name)
            )
        ));

    }

}

