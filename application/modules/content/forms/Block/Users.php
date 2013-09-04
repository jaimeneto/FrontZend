<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Users extends Layout_Form_Block
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

            $relationships = array();
            if ($contentType) {
                $metafields = $contentType->getMetafields();
                if ($metafields->relationship) {
                    foreach($metafields->relationship as $key => $relationship) {
                        if ($relationship->getOption('type') == 'users') {
                            $relationships[$key] = $relationship->getOption('label');
                        }
                    }
                }
            }

            if ($relationships) {
                $this->addElement('radio', 'image', array(
                    'label'        => 'Imagem',
                    'multiOptions' => $relationships
                ));
            }
            
        }
    }

}
