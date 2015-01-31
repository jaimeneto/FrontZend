<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Code extends Bootstrap_Form_Vertical
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

        $this->addElement('textarea', 'code', array(
            'rows'  => 20
        ));
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'ignore'      => true,
            'size'        => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'cancel', array(
            'label'         => 'Cancelar',
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'        => true
        ));

        $this->addDisplayGroup(array('save', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'tag'   => 'div',
                    'style' => 'clear:both; margin-top: 20px;'
                ))
            ),
        ));
    }

    public function populate(array $values)
    {
        if (isset($values['path']) && $values['path']) {
            $fullpath = APPLICATION_PATH . '/layouts/' . $values['path'];
            if (is_file($fullpath) && is_readable($fullpath)) {
                $this->getElement('code')->setLabel($values['path']);
                $values['code'] = file_get_contents($fullpath);
            }
        }

        return parent::populate($values);
    }

    public function persistData()
    {
        $values = $this->getValues();

        if ($values) {
            $fullpath = APPLICATION_PATH . '/layouts/' . $values['path'];
            return file_put_contents($fullpath, $values['code']);
        }
    }

}