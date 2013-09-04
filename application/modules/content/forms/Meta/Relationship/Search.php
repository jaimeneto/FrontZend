<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Meta_Relationship_Search extends Zend_Form_Element_MultiCheckbox
{
    public $helper = 'formMetaRelationshipSearch';

    protected $_metaOptions = array();

    public function __construct($spec, $options = null)
    {
        $this->_metaOptions = $options;

        if (!isset($options['separator'])) {
            $options['separator'] = '';
        }
        if (!isset($options['text_class'])) {
            $options['text_class'] = 'span10';
        }
        if (!isset($options['text_placeholder'])) {
            $options['text_placeholder'] = 'Digite o título do conteúdo para buscar';
        }
        $options['escape'] = false;

        parent::__construct($spec, $options);
    }

    /**
     * Fill the multiOptions values, than, checks if it is valid
     *
     * @param String|Array $value
     */
    public function isValid($value, $context = null)
    {
        $this->loadMultiOptions($value);
        return parent::isValid($value, $context);
    }

    /**
     * Fill the multiOptions values, than, sets the value
     *
     * @param String|Array $value
     */
    public function setValue($value)
    {
        $this->loadMultiOptions($value);
        return parent::setValue($value);
    }

    /**
     * Verify what is the type of relationship and calls the method that will
     * fill de multiOptions to the multiCheckbox element
     *
     * @param String|Array $value
     */
    public function loadMultiOptions($value)
    {
        if ($value) {
            $multiOptions = $this->getMultiOptions();
            if (!$multiOptions) {
                if ($this->_metaOptions['type'] == 'contents') {
                    $this->loadContents($value);
                } else if ($this->_metaOptions['type'] == 'users') {
                    $this->loadUsers($value);
                }
            }
        }

        return $this;
    }

    /**
     * Loads the contents to add the multiOptions to the multiCheckbox element
     *
     * @param String|Array $options
     * @return Content_Form_Meta_Relationship_Search
     */
    public function loadContents($options)
    {
        $tbContent = new Content_Model_DbTable_Content();
        $multiOptions = $tbContent->fetchPairs(
            'title',
            'id_content IN (' . implode(',', (array) $options) . ')',
            'title'
        );
        $this->setMultiOptions($multiOptions);

        return $this;
    }

    /**
     * Loads the users to add the multiOptions to the multiCheckbox element
     *
     * @param String|Array $options
     * @return Content_Form_Meta_Relationship_Search
     */
    public function loadUsers($options)
    {
        $tbUser = new Acl_Model_DbTable_User();
        $multiOptions = $tbUser->fetchPairs(
            'display_name',
            'id_user IN (' . implode(',', (array) $options) . ')',
            'display_name'
        );
        $this->setMultiOptions($multiOptions);

        return $this;
    }

    /**
     * If value is an array with only one value and the element is not defined
     * to have multiple values, returns the first value
     *
     * @return mixed
     */
    public function getValue()
    {
        $value = parent::getValue();

        if (is_array($value) && count($value) == 1 &&
                (!isset($this->_metaOptions['multiple']) ||
                !$this->_metaOptions['multiple'])) {
            $value = current($value);
        }

        return $value;
    }

}
