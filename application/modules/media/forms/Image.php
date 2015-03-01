<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Form_Image extends Bootstrap_Form_Horizontal
{
    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));

        $this->addPrefixPath('Bootstrap_Form', 'Bootstrap/Form');

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
            'size'     => Bootstrap_Form_Element_Text::SIZE_SMALL
        ));

        $this->addElement('textarea', 'info', array(
            'label' => 'Informações',
            'rows'  => 2
        ));

        $this->addElement('text', 'keywords', array(
            'label' => 'Palavras-chave'
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
                        'class' => 'tab-pane active',
                        'id'    => 'main_elements'
                    ))
                )
            )
        );
    }

    public function initInfo()
    {
        $this->addElement('StaticText', 'creator_name', array(
            'label'    => 'Cadastrador por',
            'class'    => 'form-control',
            'disabled' => true
        ));
        $groupElements[] = 'creator_name';

        $this->addElement('StaticText', 'original_name', array(
            'label'    => 'Nome original',
            'class'    => 'form-control',
            'disabled' => true
        ));
        $groupElements[] = 'original_name';

        $this->addElement('StaticText', 'date_created', array(
            'label'    => 'Data da criação',
            'class'    => 'form-control',
            'disabled' => true
        ));
        $groupElements[] = 'date_created';

        $this->addElement('StaticText', 'date_updated', array(
            'label'    => 'Última atualização',
            'class'    => 'form-control',
            'disabled' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addDisplayGroup(
            $groupElements, 'media_info', array(
                'legend'     => 'Informações',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane',
                        'id'    => 'media_info'
                    ))
                )
            )
        );
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'      => 'Salvar',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'apply', array(
            'label'      => 'Aplicar',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_SUCCESS
        ));

        $this->addElement('submit', 'save_new', array(
            'label'      => 'Salvar nova',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_WARNING
        ));

        $this->addElement('submit', 'revert_original', array(
            'label'      => 'Reverter alterações',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_DANGER
        ));
        
        $this->addElement('submit', 'cancel', array(
            'label'      => 'Cancelar',
            'ignore'     => true,
            'size'       => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT
        ));

        $this->addDisplayGroup(array('save', 'apply', 'save_as', 'cancel'), 'buttons', array(
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
        $this->setAttrib('class', $this->getAttrib('class') . ' tab-content');

        $model = new Media_Model_File();
        $if = $model->getInputFilter();
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