<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Code extends Twitter_Bootstrap_Form_Vertical
{
    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

//        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
//        $this->addPrefixPath('Twitter_Bootstrap_Form', 'Twitter/Bootstrap/Form');

        $this->initElements();
        parent::__construct($options);
        $this->initButtons();
    }

    public function initElements()
    {
        $this->addElement('hidden', 'path');

        $this->addElement('textarea', 'code', array(
            'class' => 'input-block-level',
            'rows'  => 20
        ));
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancelar',
            'class'  => 'btn-large',
            'ignore' => true
        ));

        $this->addFormActions(array('save', 'cancel'));
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