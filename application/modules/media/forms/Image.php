<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Form_Image extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        $this->addPrefixPath('Twitter_Bootstrap_Form', 'Twitter/Bootstrap/Form');

        if (isset($options['edit']) && $options['edit']) {
            $this->_edit = true;
        }

        $this->initElements();
        if ($this->_edit) {
            $this->initInfo();
        }
        parent::__construct($options);
        $this->initButtons();
    }

    public function initElements()
    {
        $this->addElement('text', 'credits', array(
            'label'    => 'Créditos',
            'class'    => 'input-block-level'
        ));

        $this->addElement('textarea', 'info', array(
            'label' => 'Informações',
            'class' => 'input-block-level',
            'rows'  => 2
        ));

        $this->addElement('text', 'keywords', array(
            'label' => 'Palavras-chave',
            'class' => 'input-block-level'
        ));

        $this->addElement('hidden', 'id_file');

        $this->addDisplayGroup(
            array_keys($this->getElements()),
            'main_elements',
            array(
                'legend'     => 'Dados gerais',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane form-horizontal active',
                        'id'    => 'main_elements'
                    ))
                )
            )
        );
    }

    public function initInfo()
    {
        $this->addElement('UneditableTextfield', 'creator_name', array(
            'label'  => 'Cadastrador por',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'creator_name';

        $this->addElement('UneditableTextfield', 'original_name', array(
            'label'  => 'Nome original',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'original_name';

        $this->addElement('UneditableTextfield', 'date_created', array(
            'label'  => 'Data da criação',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'date_created';

        $this->addElement('UneditableTextfield', 'date_updated', array(
            'label'  => 'Última atualização',
            'class'  => 'input-block-level',
            'ignore' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addDisplayGroup(
            $groupElements, 'media_info', array(
                'legend'     => 'Informações',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane form-horizontal',
                        'id'    => 'media_info'
                    ))
                )
            )
        );
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
        $this->setAttrib('class', 'tab-content');

        $if = Media_Model_File::getInputFilter();
        foreach ($if as $name => $options) {
            $element = $this->getElement($name);
            if ($element) {
                $element->setOptions($options);
            }
        }
    }

    public function populate(array $values)
    {
        if (isset($values['id_file'])) {
            $file = FrontZend_Container::get('File')->findById($values['id_file']);

            $values['creator_name'] = $file->getUser()->name;
            $values['date_created'] = ucfirst($file->getDateCreated()
                    ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));
            $values['date_updated'] = ucfirst($file->getDateUpdated()
                    ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));
        }

        return parent::populate($values);
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            $file = isset($values['id_file'])
                ? FrontZend_Container::get('File')->findById($values['id_file'])
                : FrontZend_Container::get('File')->createRow($values);
            return FrontZend_Container::get('File')->save($file);
        }
    }

}