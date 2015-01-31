<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Block_Title extends Layout_Form_Block
{
    protected function _initElements()
    {       
        // title
        $this->addElement('text', 'title', array(
            'required'   => true,
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
            )
        ));
        
        $this->addElement('radio', 'tag', array(
            'required'     => true,
            'label'        => 'Tag',
            'maxlength'    => 60,
            'inline'       => true,
            'multiOptions' => array(
                'h1'   => 'h1',
                'h2'   => 'h2',
                'h3'   => 'h3',
                'h4'   => 'h4',
                'h5'   => 'h5',
                'h6'   => 'h6',
                'div'  => 'div',
                'span' => 'span'
            ),
            'value' => 'div'
        ));
    }

}
