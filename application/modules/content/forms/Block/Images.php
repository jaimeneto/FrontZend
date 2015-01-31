<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Images extends Layout_Form_Block
{

    protected function _initElements()
    {
        // title
        $this->addElement('text', 'title', array(
            'label'      => 'Título',
            'maxlength'  => 60,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 60,
                    ),
                ),
            ),
            'append' => '<a href="#" rel="tooltip" ' 
                . 'title="Apenas para referência" data-placement="left">'
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));

        // image
        if ($this->_block) {
            $page = $this->_block->getPage();
            $contentType = $page->getContentType();
            if (!$contentType && $page->id_content) {
                $content = $page->getContent();
                $contentType = $content->getContentType();
            }

            $fields = array();
            if ($contentType) {
                $metafields = $contentType->getMetafields();
                foreach($metafields as $field) {
                    if ($field->datatype == 'file') {
                        $fields[$field->id_metafield] 
                                = $field->getOption('label');
                    }
                }
            }

            if ($fields) {
                $this->addElement('radio', 'field', array(
                    'label'        => 'Campo',
                    'multiOptions' => $fields
                ));
            }
        }
        
        $this->addElement('text', 'width', array(
            'label'  => 'Largura',
            'append' => 'px',
            'class'  => 'input-mini'
        ));
        
        $this->addElement('text', 'height', array(
            'label'  => 'Altura',
            'append' => 'px',
            'class'  => 'input-mini'
        ));
        
        $this->addElement('checkbox', 'fill', array(
            'label'  => 'Preencher imagem'
        ));

    }

}
