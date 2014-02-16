<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/Abstract.php';

class Content_View_Helper_BlockContentComments extends Zend_View_Helper_Abstract
{      
    public function blockContentComments(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        if (!$content) {
            return;
        }
        
        $form = new Content_Form_Comments();
        $form->setAction($this->view->baseUrl('comments/add'));
        $form->getElement('id_content')->setValue($content->id);
        $form->getElement('comments')->setAttrib('placeholder',
            $block->getOption('placeholder'));

        $comments = array();
        if ($content->countComments() > 0) {
            $pageNumber = Zend_Controller_Front::getInstance()
                                ->getRequest()->getParam('p', 1);
            
            $comments = FrontZend_Container::get('Comment')->findAll(array(
                'where' => array('id_content = ?' => $content->id_content),
                'order' => 'dt_created ' . $block->getOption('order'),
                'limit' => $block->getOption('limit'),
                'page'  => $pageNumber
            ));
        }

        $auth = Zend_Auth::getInstance()->hasIdentity()
                ? Zend_Auth::getInstance()->getIdentity()
                : null;

        return $this->view->partial('blocks/content-comments.phtml', array(
            'block'    => $block,
            'content'  => $content,
            'comments' => $comments,
            'form'     => $form,
            'auth'     => $auth
        ));
    }
}