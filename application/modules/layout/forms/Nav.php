<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Form_Nav extends Bootstrap_Form_Horizontal
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
        $mainElements = array();

        $this->addElement('hidden', 'id_layout_nav');
        $this->addElement('hidden', 'id_parent');
        $this->addElement('hidden', 'order');

        $this->addElement('text', 'label', array(
            'label' => 'Rótulo'
        ));
        $mainElements[] = 'label';

        $this->addElement('text', 'uri', array(
            'label' => 'Url'
        ));
        $mainElements[] = 'uri';

        $resources = FrontZend_Container::get('AclPermission')->getResourcesTree();
        $multiOptions = array('' => '');
        foreach($resources['frontend']['resources'] as $idResource => $resource)
        {
            $idPage = substr($idResource, strrpos($idResource, '-')+1);
            $page = FrontZend_Container::get('LayoutPage')->findById($idPage);
            $multiOptions['frontend_' . $idResource] = $page->page;
        }

        asort($resources);
        $this->addElement('select', 'resource', array(
            'label'        => 'Recurso',
            'multiOptions' => $multiOptions,
            'append' => '<a href="#" rel="tooltip" '
                . 'title="Exibe se o usuário tiver permissão" data-placement="left">'
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));
        $mainElements[] = 'resource';

        $this->addElement('radio', 'visible', array(
            'label'        => 'Status',
            'inline'       => true,
            'value'        => 1,
            'separator'    => '',
            'multiOptions' => array(
                1 => 'Ativo',
                0 => 'Inativo'
            ),
        ));
        $mainElements[] = 'visible';

        $this->addDisplayGroup(
            $mainElements, 'main_elements', array(
                'legend' => 'Dados gerais',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane active',
                        'id'    => 'main_elements'
                    ))
                )
            ));

        $this->addElement('text', 'class', array(
            'label'     => 'Classe CSS',
            'belongsTo' => 'attribs',
            'filters'   => array(
                'StringTrim',
                'StripTags'
            )
        ));

        $this->addElement('text', 'accesskey', array(
            'label'      => 'Atalho',
            'belongsTo'  => 'attribs',
            'maxlength'  => 1,
            'prepend'    => 'Alt + Shift + ',
            'filters'    => array(
                'StringToLower'
            ),
            'validators' => array(
                'Alnum',
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 1,
                        'max'      => 1,
                    ),
                ),
            )
        ));

        $this->addElement('radio', 'target', array(
            'label'        => 'Abrir',
            'inline'       => true,
            'value'        => '_self',
            'separator'    => '',
            'belongsTo'    => 'attribs',
            'multiOptions' => array(
                '_self'  => 'Na mesma janela',
                '_blank' => 'Em uma nova janela'
            ),
        ));

        $this->addDisplayGroup(
            array('class', 'accesskey', 'target'), 'attributes', array(
                'legend' => 'Atributos',
                'decorators' => array(
                'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane form-horizontal',
                        'id'    => 'attributes'
                    ))
                )
            ));

    }

    public function initButtons()
    {
//        $this->addElement('submit', 'save', array(
//            'label'       => 'Salvar',
//            'class'       => 'btn-lg',
//            'ignore'      => true,
//            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
//        ));
//
//        $this->addElement('submit', 'apply', array(
//            'label'      => 'Aplicar',
//            'class'      => 'btn-lg',
//            'ignore'     => true,
//            'buttonType' => Bootstrap_Form_Element_Submit::BUTTON_SUCCESS
//        ));
//
//        $this->addElement('submit', 'cancel', array(
//            'label'  => 'Cancelar',
//            'class'  => 'btn-lg',
//            'ignore' => true
//        ));
//
//        $this->addDisplayGroup(array('save', 'apply', 'cancel'), 'buttons', array(
//            'decorators' => array(
//                'FormElements', 
//                array('HtmlTag', array(
//                    'class' => 'col-sm-offset-2', 
//                    'tag'   => 'div',
//                    'style' => 'clear:both'
//                ))
//            ),
//        ));
    }

    public function init()
    {
        $model = new Layout_Model_Nav();
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
//        if (isset($values['id_layout_nav'])) {
//            $multiOptions = $this->getElement('id_parent')->getMultiOptions();
//            unset($multiOptions[$values['id_layout_nav']]);
//            $this->getElement('id_parent')->setMultiOptions($multiOptions);
//        }

        if (isset($values['attribs']) && $values['attribs'] && is_string($values['attribs'])) {
            $values['attribs'] = Zend_Json::decode($values['attribs']);
        }

        return parent::populate($values);
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            if (isset($values['id_layout_nav'])) {
                $nav = FrontZend_Container::get('LayoutNav')
                        ->findById($values['id_layout_nav']);
            } else {
                $nav = FrontZend_Container::get('LayoutNav')->createRow();
                if (!$values['order']) {
                    $adapter = FrontZend_Container::get('LayoutNav')->getAdapter();
                    $orderField = $adapter->quoteIdentifier('order');
                    $select = $adapter->select()->from('layout_nav',
                        array("MAX({$orderField})"));
                    if ($values['id_parent']) {
                        $select->where('id_parent = ?', $values['id_parent']);
                    } else {
                        $select->where('id_parent IS NULL');
                    }
                    $maxOrder = $adapter->fetchOne($select);
                    $values['order'] = $maxOrder + 1;
                }
            }

            if (!$values['id_parent']) {
                unset($values['id_parent']);
            }

            if ($values['attribs']) {
                $values['attribs'] = array_filter($values['attribs']);
                $values['attribs'] = Zend_Json::encode($values['attribs']);
            }

            $nav->setFromArray($values);

            return FrontZend_Container::get('LayoutNav')->save($nav);
        }
    }

}