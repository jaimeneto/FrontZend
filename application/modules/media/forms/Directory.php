<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Form_Directory extends Bootstrap_Form_Horizontal
{

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        parent::__construct($options);
        
        $this->initElements();
        $this->initButtons();
    }
    
    public function initElements()
    {
        $this->addElement('hidden', 'path');
        
        $this->addElement('text', 'name', array(
            'label'      => 'Nome',
            'validators' => array('AlNum')
        ));
        
        $this->addDisplayGroup(array('path', 'name'), 'main_elements', array(
            'decorators' => array(
                'FormElements'
            ),
        ));
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'      => 'Salvar',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));
        
        $this->addElement('submit', 'cancel', array(
            'label'      => 'Cancelar',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT
        ));

        $this->addDisplayGroup(array('save', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'tag'   => 'div',
                    'style' => 'clear:both'
                ))
            ),
        ));
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            return FrontZend_Container::get('File')
                        ->addDir($values['name'], $values['path']);
        }
    }

}