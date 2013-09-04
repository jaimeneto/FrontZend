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

class Acl_View_Helper_BlockAclLogin extends Zend_View_Helper_Abstract
{
    public function blockAclLogin(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        if (isset($this->view->loginForm)) {
            $loginForm = $this->view->formLogin;
        } else {
            $loginForm = new Acl_Form_Login();
            $loginForm->setAction($this->view->url(array(), 'login'));
        }
        
        $xhtml = $this->view->partial('blocks/' . $block->block . '.phtml', array(
            'block'     => $block,
            'content'   => $content,
            'user'      => $user,
            'loginForm' => $loginForm
        ));

        return $xhtml;
    }
}
