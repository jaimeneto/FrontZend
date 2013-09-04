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
                if ($metafields->file) {
                    foreach($metafields->file as $key => $field) {
                        $fields[$key] = $field->getOption('label');
                    }
                }
            }

            if ($fields) {
                $this->addElement('radio', 'image', array(
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
            'input_label'  => 'Preencher imagem'
        ));

        //template
        $multiOptions = array(
            '' => '',
        );
        $theme = Acl_Model_Auth::getTheme();
        if (is_dir(APPLICATION_PATH . '/layouts/frontend/' . $theme .
            '/scripts/blocks/content-images')) {
            $dir = new DirectoryIterator(APPLICATION_PATH . '/layouts/frontend/'
                 . $theme . '/scripts/blocks/content-images');
            foreach($dir as $file) if($file->isFile()) {
                $multiOptions[$theme][$file->getFilename()] = $file->getFilename();
            }
        }

        $dir = new DirectoryIterator(APPLICATION_PATH . '/modules/content/views'
                 . '/blocks/content-images');
        foreach($dir as $file) if($file->isFile()) {
            if (!isset($multiOptions[$theme][$file->getFilename()])) {
                $multiOptions['standard'][$file->getFilename()] = $file->getFilename();
            }
        }

        $this->addElement('select', 'template', array(
            'label'        => 'Modelo',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));
    }

}
