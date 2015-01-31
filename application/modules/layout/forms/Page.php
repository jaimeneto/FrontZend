<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Page extends FrontZend_Module_Form_Abstract
{

    public function initElements()
    {
        $this->addElement('hidden', 'id_layout_page');

        $themes = FrontZend_Container::get('LayoutTheme')->findAll(array(
            'where' => array('env != ?' => 'backend'),
            'order' => 'theme'
        ));
        $multiOptions = array('' => '');
        foreach($themes as $theme) {
            $multiOptions[$theme->env][$theme->id] = (string) $theme;
        }
        $this->addElement('select', 'id_layout_theme', array(
            'label'        => 'Tema',
            'multiOptions' => $multiOptions
        ));

        $this->addElement('text', 'page', array(
            'label'        => 'Página'
        ));

        $contentTypes = FrontZend_Container::get('ContentType')->fetchPairs('type', null,
            'type');
        $multiOptions = array('' => '') + $contentTypes;
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'multiOptions' => $multiOptions,
            'description'  => 'Selecione um tipo de conteúdo '
                            . 'OU um conteúdo específico abaixo'
        ));
        
        $search = new Content_Form_Meta_Relationship_Search(
            'id_content', array(
                'label'         => 'Conteúdo',
                'type'          => 'contents'
            )
        );
        $search->setDecorators($this->_elementDecorators);
        $this->addElement($search);
    }

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
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
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
        $model = new Layout_Model_Page();
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
            if (!$values['id_content']) {
                $values['id_content'] = null;
            }
            if (!$values['id_content_type']) {
                $values['id_content_type'] = null;
            }
            if (!$values['special']) {
                $values['special'] = null;
            }

            $page = FrontZend_Container::get('LayoutPage')->createRow($values);
            return FrontZend_Container::get('LayoutPage')->save($page);
        }
    }

}