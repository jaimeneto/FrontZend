<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Form_Block_Login extends Layout_Form_Block_Title
{
    protected function _initElements()
    {
        parent::_initElements();

        $this->getElement('title')->setAttrib('append',
            '<a href="#" rel="tooltip" data-placement="left" '
            . 'title="Apenas para referência. ' 
            . 'O título do conteúdo será exibido">'
            . '<i class="icon-info-sign"></i></a>');
    }
}
