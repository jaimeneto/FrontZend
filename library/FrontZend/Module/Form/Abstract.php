<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @package    Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

Abstract class FrontZend_Module_Form_Abstract
    extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_modelClass = null;
    
    public function __construct($options = null)
    {
        if (!$this->_modelClass) {
            $this->_modelClass = str_replace(
                '_Form_',
                '_Model_', get_class($this)
            );
        }
        
        $this->setAttrib('id', strtolower(__CLASS__));
        
        $this->initElements();
        parent::__construct($options);
        $this->initButtons();
    }

    abstract public function initElements();

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'apply', array(
            'label'      => 'Aplicar',
            'class'      => 'btn-large',
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancelar',
            'class'  => 'btn-large',
            'ignore' => true
        ));

        $this->addDisplayGroup(
            array('save', 'apply', 'cancel'), 'form_actions', array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array(
                    array('Actions', array('style' => 'clear:both'))
                )
            )
        );
    }

    public function init()
    {
        $model = new $this->_modelClass;
        $if = $model::getInputFilter();
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
            $model = new $this->_modelClass($values);
            $cacheAlias = str_replace('_Model_', '', $this->_modelClass);
            return FrontZend_Container::get($cacheAlias)->save($model);
        }
    }

}