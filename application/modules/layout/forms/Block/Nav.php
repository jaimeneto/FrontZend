<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Block_Nav extends Layout_Form_Block
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
            'append' => '<a href="#" rel="tooltip" data-placement="left" '
                      . 'title="Apenas para referência">'
                      . '<i class="icon-info-sign"></i></a>'
        ));

        //container
        $multiOptions = FrontZend_Container::get('LayoutNav')->fetchPairs('label');
        $this->addElement('select', 'container', array(
            'label'        => 'Menu',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));

        //template

        $path = APPLICATION_PATH . '/layouts/frontend/'
              . Acl_Model_Auth::getTheme() . '/scripts/blocks/layout-nav';
        if (!is_dir($path)) {
            $path = APPLICATION_PATH . '/modules/layout/views/blocks/layout-nav';
        }
        $dir = new DirectoryIterator($path);
        $multiOptions = array(
            '' => '',
        );
        foreach($dir as $file) if($file->isFile()) {
            $multiOptions[$file->getFilename()] = $file->getFilename();
        }
        $this->addElement('select', 'template', array(
            'label'        => 'Modelo',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));

    }

}
