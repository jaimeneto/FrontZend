<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_CommentController extends FrontZend_Module_Controller_Abstract
{

    public function listAction()
    {
        $this->view->headTitle()->append('Comentários');
        
        parent::listAction();
    }
    
}
