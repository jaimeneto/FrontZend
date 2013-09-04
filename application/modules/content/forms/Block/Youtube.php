<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Youtube extends Layout_Form_Block
{

    protected function _initElements()
    {
        // title
        $this->addElement('text', 'title', array(
            'label'      => 'Título',
            'maxlength'  => 60,
            'class'      => 'input-block-level',
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
                . '<i class="icon-info-sign"></i></a>'
        ));

        // youtube
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
                if ($metafields->field) {
                    foreach($metafields->field as $key => $field) {
                        if ($field->getOption('type') == 'youtube') {
                            $fields[$key] = $field->getOption('label');
                        }
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

        // width
        $this->addElement('text', 'width', array(
            'label'      => 'Largura',
            'maxlength'  => 4,
            'class'      => 'input-mini',
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                'Digits',
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 4,
                    ),
                ),
            ),
            'value' => '320',
            'append' => 'px'
        ));

        // height
        $this->addElement('text', 'height', array(
            'label'      => 'Altura',
            'maxlength'  => 4,
            'class'      => 'input-mini',
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                'Digits',
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 4,
                    ),
                ),
            ),
            'value' => '240',
            'append' => 'px'
        ));

        //template
        $this->addElement('radio', 'template', array(
            'label'        => 'Forma de exibição',
            'multiOptions' => array(
                'screen' => 'Exibir diretamente o video',
                'dialog' => 'Abrir em uma janela dialog',
                'popup'  => 'Abrir em uma janela popup',
                'link'   => 'Abrir na página do youtube'
            ),
            'value' => 'screen.phtml'
        ));

    }

}
