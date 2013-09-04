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
                if ($metafields->relationship) {
                    foreach($metafields->relationship as $key => $relationship) {
                        if ($relationship->getOption('type') == 'contents') {
                            $relationships[$key] = $relationship->getOption('label');
                        }
                    }
                }
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
            'label_class'  => 'inline span2'
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
                . '<i class="icon-info-sign"></i></a>'
        ));

        // form_filter
        $this->addElement('checkbox', 'form_filter', array(
            'input_label' => 'Exibir formulário para filtrar conteúdo'
        ));

        // form_result
        $this->addElement('checkbox', 'form_result', array(
            'input_label'  => 'Exibir resultados de uma busca <a rel="tooltip" '
                            . 'title="Utilize um bloco de formulário de busca" '
                            . 'data-placement="right"><i class="icon-info-sign">'
                            . '</i></a>',
            'escape'       => false,
        ));

        // order
        $this->addElement('select', 'order', array(
            'label'        => 'Ordenação',
            'class'        => 'input-block-level',
            'multiOptions' => array(
                ''                  => '',
                'title'             => 'Título',
                'dt_published'      => 'Ordem de publicação',
                'dt_published desc' => 'Últimos publicados',
            )
        ));

        //template
        $multiOptions = array(
            '' => '',
        );
        $theme = Acl_Model_Auth::getTheme();
        if (is_dir(APPLICATION_PATH . '/layouts/frontend/' . $theme .
            '/scripts/blocks/content-contents')) {
            $dir = new DirectoryIterator(APPLICATION_PATH . '/layouts/frontend/'
                 . $theme . '/scripts/blocks/content-contents');
            foreach($dir as $file) if($file->isFile()) {
                $multiOptions[$theme][$file->getFilename()] = $file->getFilename();
            }
        }

        $dir = new DirectoryIterator(APPLICATION_PATH . '/modules/content/views'
                 . '/blocks/content-contents');
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
