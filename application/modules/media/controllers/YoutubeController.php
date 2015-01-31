<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_YoutubeController extends Zend_Controller_Action
{
    public function init()
    {
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        $this->_helper->acl("{$module}_{$controller}_{$action}");
    }

    public function ajaxSearchModalAction()
    {
        $modalTitle = $this->getParam('title', 'Buscar vídeo no youtube...');
        
        $this->view->modalTitle = $modalTitle;
        
        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render('youtube/ajax-search-modal.phtml')
        ));
    }
    
    /**
     * Busca videos no Youtube e retorna um JSON com os resultados
     * 
     */
    public function ajaxSearchAction()
    {
        $term = $this->_getParam('term');

        $validateLink = new FrontZend_Validate_Link();
        if ($validateLink->isValid($term) && strstr($term, 'youtube.com')) {
            $youtubeId = Media_Model_Youtube::getIdFromUrl($term);
            $this->_helper->json(array(
                'status' => 1,
                'vars' => array(
                    'youtubeId' => $youtubeId
                )
            ));

        } else {

            try {
                $this->view->videoFeed = Media_Model_Youtube::search($term, 24);

                $this->_helper->json(array(
                    'status'  => 1,
                    'content' => $this->view->render('youtube/ajax-search.phtml')
                ));
            } catch (Zend_Gdata_App_HttpException $e) {
                $this->_helper->json(array(
                    'status' => 0,
                    'msg'    => 'Não foi possível acessar o Youtube',
                    'log'    => $e->getMessage()
                ));
            }
        }

        $this->_helper->json(array(
            'status' => 0,
            'msg'    => 'Não foi possível fazer a busca'
        ));
    }

}

