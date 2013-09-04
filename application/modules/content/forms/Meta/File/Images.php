<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/** Zend_Form_Element_Multi */
require_once 'Zend/Form/Element/Multi.php';

class Content_Form_Meta_File_Images extends Zend_Form_Element_Multi
{
    // TODO Não está funcionando o validator
    protected $_registerInArrayValidator = false;

    /**
     * Use formFiles view helper by default
     * @var string
     */
    public $helper = 'formFiles';

    /**
     * MultiCheckbox is an array of values by default
     * @var bool
     */
    protected $_isArray = true;

    /**
     * Load default decorators
     *
     * Disables "for" attribute of label if label decorator enabled.
     *
     * @return Zend_Form_Element_Files
     */
    public function loadDefaultDecorators()
    {
        if ($this->loadDefaultDecoratorsIsDisabled()) {
            return $this;
        }
        parent::loadDefaultDecorators();
        $this->addDecorator('Label', array('tag' => 'div',
                                           'disableFor' => true));
        return $this;
    }

    /**
     * Can this element have more than one file?
     *
     * @return boolean
     */
    public function getMultiple()
    {
        return (bool) $this->getAttrib('multiple');
    }

    /**
     * Sets the element to have one or more files
     *
     * @param  boolean $bool
     * @return Admin_Form_Element_Files
     */
    public function setMultiple($bool)
    {
        $this->setAttrib('multiple', (bool) $bool);

        return $this;
    }

    /**
     * Is this element sortable?
     *
     * @return boolean
     */
    public function getSortable()
    {
        return (bool) $this->getAttrib('sortable');
    }

    /**
     * Sets if the element is sortable
     *
     * @param  boolean $bool
     * @return Admin_Form_Element_Files
     */
    public function setSortable($bool)
    {
        $this->setAttrib('sortable', (bool) $bool);

        return $this;
    }

}
