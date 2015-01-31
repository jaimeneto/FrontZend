<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @package    Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

require_once 'Bootstrap/Form/Horizontal.php';

Abstract class FrontZend_Module_Form_Abstract
    extends Bootstrap_Form_Horizontal
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
        
        parent::__construct($options);
        $this->initElements();
        $this->initButtons();
    }

    abstract public function initElements();

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'ignore'      => true,
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'        => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'apply', array(
            'label'         => 'Aplicar',
            'ignore'        => true,
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'cancel', array(
            'label'         => 'Cancelar',
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'        => true
        ));

        $this->addDisplayGroup(array('save', 'apply', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'class' => 'col-sm-offset-2', 
                    'tag'   => 'div',
                    'style' => 'clear:both'
                ))
            ),
        ));
    }

    public function init()
    {
        $model = new $this->_modelClass;
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
            $model = new $this->_modelClass($values);
            $cacheAlias = str_replace('_Model_', '', $this->_modelClass);
            return FrontZend_Container::get($cacheAlias)->save($model);
        }
    }

}