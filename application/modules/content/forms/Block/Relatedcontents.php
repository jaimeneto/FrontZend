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
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));

        // order
        $this->addElement('select', 'order', array(
            'label'        => 'Ordenação',
            'multiOptions' => array(
                'title'             => 'Título',
                'dt_published'      => 'Ordem de publicação',
                'dt_published desc' => 'Últimos publicados',
            )
        ));

    }

    protected function _addElementTemplate($blockName=null)
    {
        parent::_addElementTemplate('content-contents');
    }
    
}
