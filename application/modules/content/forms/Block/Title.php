<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Title extends Layout_Form_Block_Title
{
    protected function _initElements()
    {
        parent::_initElements();
        
        $this->getElement('title')->setRequired(false)->setAttrib('append',
            '<a href="#" rel="tooltip" data-placement="left" '
            . 'title="Apenas para referência. ' 
            . 'O título do conteúdo será exibido">'
            . '<span class="glyphicon glyphicon-info-sign"></span></a>');
        
        $this->getElement('tag')->setValue('h1');
    }
}
