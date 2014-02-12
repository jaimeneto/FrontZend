<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Page extends Twitter_Bootstrap_Form_Horizontal
{
    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

//        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
//        $this->addPrefixPath('Twitter_Bootstrap_Form', 'Twitter/Bootstrap/Form');
        $this->addPrefixPath('Content_Form_Meta_Relationship', 'Content/Form/Meta/Relationship');

        $this->initElements();
        parent::__construct($options);
        $this->initButtons();
    }

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
            'class'        => 'span4',
            'multiOptions' => $multiOptions
        ));

        $this->addElement('text', 'page', array(
            'label' => 'Página',
            'class' => 'span4'
        ));

        $contentTypes = FrontZend_Container::get('ContentType')->fetchPairs('type', null,
            'type');
        $multiOptions = array('' => '') + $contentTypes;
        $this->addElement('select', 'id_content_type', array(
            'label'        => 'Tipo de conteúdo',
            'class'        => 'span4',
            'multiOptions' => $multiOptions,
            'description'  => 'Selecione um tipo de conteúdo '
                            . 'OU um conteúdo específico abaixo'
        ));

        $this->addElement(new Content_Form_Meta_Relationship_Search(
            'id_content', array(
                'label'        => 'Conteúdo',
                'text_class'   => 'input-block-level',
                'type'         => 'contents'
            )
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

        $this->addFormActions(array('save', 'apply', 'cancel'));
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