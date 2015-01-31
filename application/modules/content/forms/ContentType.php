<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_ContentType extends Bootstrap_Form_Horizontal
{
    protected $_meta = array();

    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));
        
        if (isset($options['edit']) && $options['edit']) {
            $this->_edit = true;
        }
        
        parent::__construct($options);
        
        $this->initElements();
        $this->initButtons();
    }

    public function initElements()
    {
        if ($this->_edit) {
            $this->addElement('StaticText', 'parent', array(
                'label'    => 'Tipo pai',
                'class'    => 'form-control',
                'disabled' => true
            ));

            $this->addElement('StaticText', 'id_content_type', array(
                'label'    => 'Identificação',
                'class'    => 'form-control',
                'disabled' => true
            ));
        } else {
            $tbContentType = new Content_Model_DbTable_ContentType();
            $contentTypes = $tbContentType->fetchPairs('type', 'id_parent IS NULL', 'type');
            $multiOptions = array(
                '' => 'Selecione um tipo de conteúdo...',
                'Tipos de conteúdo' => $contentTypes
            );

            $this->addElement('select', 'id_parent', array(
                'label'        => 'Tipo pai',
                'required'     => true,
                'multiOptions' => $multiOptions
            ));

            $this->addElement('text', 'id_content_type', array(
                'label' => 'Identificação'
            ));
        }

        $this->addElement('text', 'type', array(
            'label' => 'Nome'
        ));

        $this->addElement('text', 'plural', array(
            'label' => 'Plural'
        ));
    }

    public function initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'         => 'Salvar',
            'ignore'        => true,
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
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
        $model = new Content_Model_ContentType();
        $if = $model->getInputFilter();
        foreach ($if as $name => $options) {
            $element = $this->getElement($name);
            if ($element) {
                $element->setOptions($options);
            }
        }
    }

    public function isValid($data)
    {
        if (isset($data['meta'])) {
            $this->_meta = $data['meta'];
        }
        return parent::isValid($data);
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            $contentType = $this->_edit
                ? FrontZend_Container::get('ContentType')->findById($values['id_content_type'])
                : FrontZend_Container::get('ContentType')->createRow();

            if (!$contentType->getId()) {
                $filterSlug = new FrontZend_Filter_Slug();
                $values['id_content_type'] = $filterSlug->filter($values['type']);
            }

            $contentType->setFromArray($values);
            return FrontZend_Container::get('ContentType')->save($contentType);
        }
    }

    public function persistMeta()
    {
        $idContentType = $this->getElement('id_content_type')->getValue();

        if (!$idContentType) {
            return false;
        }

        $currentMetafields = FrontZend_Container::get('Metafield')->fetchPairs(
                'fieldname', array('id_content_type = ?' => $idContentType)
            );

        $metaFields = array_keys($this->_meta);
        
        $deleteMetafields = array_diff($currentMetafields, $metaFields);

        $results = array(
            'inserts' => 0,
            'updates' => 0,
            'deletes' => 0
        );

        if ($deleteMetafields) {
            foreach($deleteMetafields as $idMetaField => $mf) {
                if (FrontZend_Container::get('Metafield')->delete(array(
                    'id_metafield = ?' => $idMetaField
                ))) {
                    $results['deletes']++;
                }
            }
        }
        
        $order = 1;
        foreach($this->_meta as $fieldname => $options) {
            $datatype = $options['datatype'];
            unset($options['datatype']);
            
            if ($options['type'] == 'select') {
                $multiOptionsArray = explode(PHP_EOL, 
                        $options['multiOptions']);
                $options['multiOptions'] = array();
                foreach($multiOptionsArray as $opt) {
                    $keyVal = explode(':',$opt);
                    $options['multiOptions'][$keyVal[0]] = trim($keyVal[1]);
                }
            }

            $updateData = array(
                'options' => Zend_Json::encode($options),
                'order'   => $order++
            );

            if (in_array($fieldname, $currentMetafields)) {
                $metafield = FrontZend_Container::get('Metafield')->findOne(array(
                    'where' => array(
                        'id_content_type = ?'   => $idContentType,
                        'fieldname = ?'         => $fieldname
                    )
                ));

                $metafield->setFromArray($updateData);

                if (FrontZend_Container::get('Metafield')->save($metafield)) {
                    $results['updates']++;
                }
            } else {
                $updateData['datatype'] = $datatype;
                $metafield = FrontZend_Container::get('Metafield')->createRow(
                    $updateData + array(
                        'id_content_type'   => $idContentType,
                        'fieldname'         => $fieldname
                    )
                );
                if (FrontZend_Container::get('Metafield')->save($metafield)) {
                    $results['inserts']++;
                }
            }
        }

        return $results['inserts'] + $results['updates'] + $results['deletes'];
    }

}