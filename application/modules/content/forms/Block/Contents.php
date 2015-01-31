<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Contents extends Layout_Form_Block
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

        // relationship
        if ($this->_block) {
            $page = $this->_block->getPage();
            $contentType = $page->getContentType();
            if (!$contentType && $page->id_content) {
                $content = $page->getContent();
                $contentType = $content->getContentType();
            }

            $relationships = array();
            if ($contentType) {
                $metafields = $contentType->getMetafields();
//                foreach($metafields as $key => $field) {
//                    if ($field->datatype == 'relationship' 
//                            && $field->getOption('type') == 'contents') {
//                        $relationships[$key] = $field->getOption('label');
//                    }
//                }
            }

            if ($relationships) {
                $this->addElement('radio', 'relationship', array(
                    'label'        => 'Relacionamento',
                    'multiOptions' => $relationships
                ));
            }

            // user_relationship
            $userRelationships = array();
            if($page->special && $page->special == 'user-profile') {
                $metafields = FrontZend_Container::get('Metafield')->findAll(array(
                    'where' => array(
                        'datatype = ?'   => 'relationship',
                        'options LIKE ?' => '%"type":"users"%'
                    ),
                    'order' => array('datatype', 'order ASC')
                ));

                if ($metafields) {
                    foreach($metafields as $metafield) {
                        $userRelationships[$metafield->fieldname] =
                            $metafield->hasOption('label')
                                ? $metafield->getOption('label')
                                : ucfirst($metafield->fieldname);
                    }
                }

                if ($userRelationships) {
                    $this->addElement('radio', 'user_relationship', array(
                        'label'        => 'Relac. com usuário',
                        'multiOptions' => $userRelationships
                    ));
                }
            }
        }

        // types
        $tbContentType = new Content_Model_DbTable_ContentType();
        $contentTypes = $tbContentType->fetchPairs('plural', array(
            'id_parent IS NOT NULL OR id_content_type IN (?, ?, ?, ?)' =>
            array('section', 'forum', 'forum_topic','link')));

        $this->addElement('multiCheckbox', 'types', array(
            'label'        => 'Tipos de conteúdo',
            'multiOptions' => $contentTypes,
            'separator'    => '',
            'inline'       => true
        ));

        // limit
        $this->addElement('text', 'limit', array(
            'label'      => 'Quantidade máxima',
            'maxlength'  => 3,
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
                        'max'      => 3,
                    ),
                ),
            ),
            'append' => '<a href="#" rel="tooltip" title="Deixe em branco '
                . 'para exibir todos" data-placement="right">'
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));

        // form_filter
        $this->addElement('checkbox', 'form_filter', array(
            'label' => 'Exibir formulário para filtrar conteúdo'
        ));

        // form_result
        $this->addElement('checkbox', 'form_result', array(
            'label' => 'Exibir resultados de uma busca <a rel="tooltip" '
                     . 'title="Utilize um bloco de formulário de busca" '
                     . 'data-placement="right"><span class="glyphicon glyphicon-info-sign">'
                     . '</span></a>',
            'escape' => false,
        ));

        // order
        $this->addElement('select', 'order', array(
            'label'        => 'Ordenação',
            'multiOptions' => array(
                ''                  => '',
                'title'             => 'Título',
                'dt_published'      => 'Ordem de publicação',
                'dt_published desc' => 'Últimos publicados',
            )
        ));

    }

}
