<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_FilterBlockContents extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_types = array();

    public function __construct($options = null)
    {
        if (isset($options['types']) && $options['types']) {
            $this->_types = $options['types'];
            unset($options['types']);
        }

        parent::__construct($options);
    }

    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true)
            ->setMethod('post')
            ->setAttrib('class', 'site-form-content-filter')
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        $this->addElement('text', 'title', array(
            'label'     => 'Título',
            'class'     => 'input-block-level',
            'belongsTo' => 'filters'
        ));

        if ($this->_types) {
            foreach($this->_types as $type) {
                $metafields = FrontZend_Container::get('Metafield')->fetchByContentType($type, 'relationship');

                foreach($metafields as $field) {
                    if ($field->getOption('type') == 'contents' &&
                            $field->getOption('related') != $type) {

                        $multiOptions = FrontZend_Container::get('Content')->fetchPairs(
                            'title', array('id_content_type = ?' => $field->getOption('related')
                            ), 'title');

                        $this->addElement('multiCheckbox', $field->fieldname,
                            array(
                                'label'        => $field->getOption('label'),
                                'multiOptions' => $multiOptions,
                                'class'        => 'checkbox',
                                'belongsTo'    => 'filters',
                                'separator'    => '',
                                'label_class'  => 'inline'
                            )
                        );
                    } // if
                } //foreach
            } //foreach
        } // if

        $this->addElement('radio', 'order', array(
            'label'        => 'Ordenar',
            'multiOptions' => array(
                'dt_published DESC' => 'Mais recentes',
                'dt_published'      => 'Publicação',
                'title'             => 'Título'
            ),
            'value'        => 'dt_published DESC',
            'separator'    => '',
            'label_class'  => 'inline'
        ));

        // btn_comment
        $this->addElement('submit', 'btn_filter', array(
            'label'      => 'Filtrar',
            'ignore'     => true,
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'div'))
            )
        ));
    }

}