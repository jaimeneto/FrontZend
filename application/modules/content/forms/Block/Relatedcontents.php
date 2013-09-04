<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Relatedcontents extends Layout_Form_Block
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
                
                $options = array(
                    'where' => array(
                        'datatype = ?'   => 'relationship',
                        'options LIKE ?' => '{"type":"contents"%',
//                        'options LIKE ?' => '%"related":"' . $contentType->id . '"%'
                    ),
                    'order' => array('order')
                );
                $metafields = FrontZend_Container::get('Metafield')->findAll($options);
                if ($metafields) {
                    foreach($metafields as $relationship){
                        $contentType = FrontZend_Container::get('ContentType')
                            ->findById($relationship->getOption('related'));
                        $relationships[$relationship->id] =
                            $contentType->plural
                            . ' (Relacionamento: '
                            . $relationship->getOption('label') 
                            . ')';
                    }
                }
            }

            if ($relationships) {
                $this->addElement('radio', 'related', array(
                    'label'        => 'Conteúdo relacionado',
                    'multiOptions' => $relationships
                ));
            }
        }

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

        // order
        $this->addElement('select', 'order', array(
            'label'        => 'Ordenação',
            'class'        => 'input-block-level',
            'multiOptions' => array(
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
                $multiOptions['default'][$file->getFilename()] = $file->getFilename();
            }
        }

        $this->addElement('select', 'template', array(
            'label'        => 'Modelo',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));
    }

}
