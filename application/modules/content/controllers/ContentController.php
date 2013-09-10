<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_ContentController extends FrontZend_Module_Controller_Abstract
{
   
    public function addAction()
    {
        $this->view->headTitle()->append('Adicionar conteúdo');

        parent::addAction();
    }

    /**
     * @overwrites parent::editAction();
     */
    public function editAction()
    {
        $id = $this->_getParam('id');
        
        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/content/content');
        }
        
        $content = FrontZend_Container::get('Content')->findById($id);

        if (!$content) {
            $this->getHelper('alerts')->addError('Conteúdo inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/content/content');
        }

        if ($this->_getParam('cancel')) {
            $this->_redirect(ADMIN_ROUTE . '/content/content/list' .
                             '?filter[id_content_type]=' .
                             $content->id_content_type);
        }

        $form = new Content_Form_Content(array(
            'contentType' => $content->id_content_type
        ));
        $form->setAction($this->view->url());
        $form->populate($content->toArray(true));
        
        $data = $this->getRequest()->getPost();
        if ($data) {
            if ($form->isValid($data)) {
                try {
                    $form->persistData();
                    if ($id) {
                        $this->getHelper('alerts')
                            ->addSuccess('Conteúdo alterado com sucesso');
                        if ($data['apply']) {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                ADMIN_ROUTE . "/content/content/edit/id/{$id}");
                        } else {
                            $this->getHelper('Redirector')->gotoUrlAndExit(
                                    ADMIN_ROUTE . '/content/content/list' .
                                    '?filter[id_content_type]=' .
                                    $content->id_content_type);
                        }
                    } else {
                        $this->getHelper('alerts')
                            ->addError('Erro ao editar conteúdo: ' .
                                       'dados inválidos ou faltando');
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                        ->addError('Erro ao editar conteúdo: ' .
                                   $e->getMessage());
//                    pr($e->getTraceAsString(),1);
                }
            } else {
                $this->getHelper('alerts')
                    ->addError('Erro ao editar conteúdo: ' .
                               'dados inválidos ou faltando');
            }
        }
        
        $this->view->form = $form;
        
        $this->view->headTitle()->append('Editar conteúdo');
    }

    public function configPageAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/content/content');
        }

        $content = FrontZend_Container::get('Content')->findById($id);

        if (!$content) {
            $this->getHelper('alerts')->addError('Conteúdo inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/content/content');
        }

        $page = $content->getLayoutPage(false);

        if ($page) {
            $url = ADMIN_ROUTE . '/layout/page/config/id/' . $page->id;
        } else {
            $this->getHelper('alerts')->addAlert('Ainda não foi foi criada'
                . ' uma página para esse conteúdo');
            $url = ADMIN_ROUTE . "/layout/page/add/content/{$id}";
        }

        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }

    public function ajaxSearchAction()
    {
        $term = $this->_getParam('term');
        $type = $this->_getParam('type');
        $id   = $this->_getParam('id');

        $options = array(
            'where' => array(
                'title LIKE ? OR keywords LIKE ?' => "%{$term}%"
            ),
            'limit' => 10,
            'joins' => false
        );
        if ($type) {
            $options['where']['id_content_type = ?'] = $type;
        }
        if ($id) {
            $options['where']['id_content != ?'] = $id;
        }
        $contents = FrontZend_Container::get('Content')->findAll($options);

        $data = array();
        if ($contents) {
            $filterStringLength = new FrontZend_Filter_StringLength(100);

            foreach ($contents as $content) {
                $value = $type
                    ? $content->title
                    : '[' . $content->getType()->type . '] ' . $content->title;

                $dtPublished = $content->getDatePublished()->get('dd/MM/yyyy');
                $data[] = array(
                    'id'            => $content->id_content,
                    'value'         => $value,
                    'filteredValue' => $filterStringLength->filter($value),
                    'date'          => $dtPublished,
                    'slug'          => $content->slug
                );
            }
        }

        $this->_helper->json($data);
    }

}

