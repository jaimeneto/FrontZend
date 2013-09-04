<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Theme extends Twitter_Bootstrap_Form_Horizontal
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
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancelar',
            'class'  => 'btn-large',
            'ignore' => true
        ));

        $this->addFormActions(array('save', 'apply', 'cancel'));
    }

    public function init()
    {
        $if = Layout_Model_Nav::getInputFilter();
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