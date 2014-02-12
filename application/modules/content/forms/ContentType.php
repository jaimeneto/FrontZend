<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_ContentType extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_meta = array();

    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));
        
        if (isset($options['edit']) && $options['edit']) {
            $this->_edit = true;
        }
        
        $this->initElements();
        parent::__construct($options);
        $this->initButtons();
    }

    public function initElements()
    {
        if ($this->_edit) {
            $this->addElement('text', 'parent', array(
                'label'    => 'Tipo pai',
                'class'    => 'input-xlarge',
                'disabled' => 'disabled',
                'ignore'   => true
            ));

            $this->addElement('text', 'id_content_type', array(
                'label'    => 'Identificação',
                'class'    => 'input-xlarge',
                'disabled' => 'disabled'
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
                'class'        => 'input-xlarge',
                'multiOptions' => $multiOptions
            ));

            $this->addElement('text', 'id_content_type', array(
                'label' => 'Identificação',
                'class' => 'input-xlarge'
            ));
        }

        $this->addElement('text', 'type', array(
            'label' => 'Nome',
            'class' => 'input-xlarge'
        ));

        $this->addElement('text', 'plural', array(
            'label' => 'Plural',
            'class' => 'input-xlarge'
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

        if (!isset($this->_meta['field'])) {
            $this->_meta['field'] = array();
        }
        if (!isset($this->_meta['relationship'])) {
            $this->_meta['relationship'] = array();
        }
        if (!isset($this->_meta['file'])) {
            $this->_meta['file'] = array();
        }
        
        $metaFields = array_merge(array_keys($this->_meta['field']),
                      array_keys($this->_meta['relationship']),
                      array_keys($this->_meta['file']));

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

        foreach($this->_meta as $datatype => $metafields) {
            $order = 1;
            foreach($metafields as $fieldname => $options) {
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
        }
        return $results['inserts'] + $results['updates'] + $results['deletes'];
    }

}