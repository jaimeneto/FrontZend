<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Info extends Layout_Form_Block
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

        // maindata
        $this->addElement('multiCheckbox', 'maindata', array(
            'label'        => 'Dados gerais',
            'inline'       => true,
            'multiOptions' => array(
                'title'        => 'Título',
                'slug'         => 'Slug',
                'content_type' => 'Tipo de conteúdo',
                'user'         => 'Usuário que cadastrou',
                'dt_published' => 'Data de publicação',
                'dt_created'   => 'Data de criação',
                'dt_updated'   => 'Data da última alteração',
                'keywords'     => 'Palavras-chave',
            )
        ));

        // metadata
        if ($this->_block) {
            $page = $this->_block->getPage();
            $contentType = $page->getContentType();
            if (!$contentType && $page->id_content) {
                $content = $page->getContent();
                $contentType = $content->getContentType();
            }

            if ($contentType) {
                $metafields = $contentType->getMetafields();

                $metadata = array();
                foreach($metafields as $key => $field) {
                    if ($field->datatype == 'field') {
                        $metadata[$key] = $field->getOption('label');
                    }
                }

                if ($metadata) {
                    $this->addElement('multiCheckbox', 'metadata', array(
                        'label'        => 'Dados extra',
                        'multiOptions' => $metadata,
                        'inline'       => true
                    ));
                }

                $relationships = array();
                foreach($metafields as $key => $field) {
                    if ($field->datatype == 'relationship') {
                        $relationships[$key] = $field->getOption('label');
                    }
                }

                if ($relationships) {
                    $this->addElement('multiCheckbox', 'relationships', array(
                        'label'        => 'Relacionamentos',
                        'multiOptions' => $relationships,
                        'inline'       => true
                    ));
                }
            }
            
        }
    }

}
