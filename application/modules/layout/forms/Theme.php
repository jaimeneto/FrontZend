<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Theme extends Bootstrap_Form_Horizontal
{
    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        $this->initElements();
        parent::__construct($options);
        $this->initButtons();
    }

    public function initElements()
    {
        $mainElements = array();

        $this->addElement('hidden', 'env');
        $this->addElement('hidden', 'theme');
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'       => 'Instalar',
            'ignore'      => true,
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'        => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE
        ));

        $this->addElement('submit', 'cancel', array(
            'label'       => 'Cancelar',
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
            'size'        => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'      => true
        ));

        $this->addDisplayGroup(array('save', 'apply', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'tag'   => 'div',
                    'style' => 'clear:both'
                ))
            ),
        ));
    }

    public function init()
    {
        $model = new Layout_Model_Theme();
        $if = $model->getInputFilter();
        foreach ($if as $name => $options) {
            $element = $this->getElement($name);
            if ($element) {
                $element->setOptions($options);
            }
        }
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            $theme = FrontZend_Container::get('LayoutTheme')->createRow($values);
            $theme->active = 0;
            
            return FrontZend_Container::get('LayoutTheme')->save($theme);
        }
    }

}