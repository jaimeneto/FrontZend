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

class Layout_View_Helper_ShowMenu extends Zend_View_Helper_Abstract
{
    public function showMenu($container, $template=null,
        Content_Model_Content $content = null, $user = null)
    {
        $options = array('container' => $container);
        if ($template) {
            $options['template'] = $template;
        }

        $blockLayoutNav = FrontZend_Container::get('LayoutBlock')->createRow();
        $blockLayoutNav->visible = true;
        $blockLayoutNav->setOptions($options);
        echo $this->view->blockLayoutNav($blockLayoutNav, $content, $user);
    }
}
