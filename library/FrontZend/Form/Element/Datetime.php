<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Form
 * @subpackage Element
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

require_once 'Zend/Form/Element/Text.php';
require_once 'Zend/Validate/Date.php';

class FrontZend_Form_Element_Datetime extends Zend_Form_Element_Text
{
    /**
     * Default form view helper to use for rendering
     * @var string
     */
    public $helper = 'formDatetime';

    public function __construct($spec, $options = null)
    {
        $this->addFilter('StringTrim');
        $this->_setFormatValidator('yyyy-MM-dd HH:mm');
        $this->setAttrib('class', 'formDate');
        
        parent::__construct($spec, $options);
    }

    protected function _setFormatValidator($format)
    {
        $this->addValidator(new Zend_Validate_Date(array('format' => $format)));
        $this->setAttrib('format', $format);
    }

    /**
     * Gets the max year
     *
     * @return string
     */
    public function getMaxYear()
    {
        return $this->getAttrib('max-year');
    }

    /**
     * Sets the max year
     *
     * @param  string $year
     * @return FrontZend_Form_Element_Date
     */
    public function setMaxYear($year)
    {
        $this->setAttrib('max-year', $year);

        return $this;
    }

    /**
     * Gets the date format
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->getAttrib('format');
    }

    /**
     * Sets the date format
     *
     * @param  string $format
     * @return FrontZend_Form_Element_Date
     */
    public function setFormat($format)
    {
        $this->_setFormatValidator($format);

        return $this;
    }

    public function getValue()
    {
        $value = null;
        if ($this->_value) {
            $date = new Zend_Date($this->_value);
            $value = $date->get($this->getFormat());
        }

        return $value;
    }

    public function getFormatedValue($format)
    {
        $date = new Zend_Date($this->_value);
        return $date->get($format);
    }

    public function isValid($value, $context = null)
    {
        $this->getValidator('Date')->setFormat($this->getFormat());
        if (is_array($value)) {
            $value = new Zend_Date($value);
        }
        if ($value instanceof Zend_Date) {
            $value = $value->get($this->getFormat());
        }
        return parent::isValid($value, $context);
    }

    /**
     * Sets date value. Parameter can be instance of Zend_Date, String or array
     * with keys: year, month, day, hour, minute, second
     *
     * @param mixed $value
     * @return String
     */
    public function setValue($value)
    {
        if (is_string($value)) {
            $value = new Zend_Date($value);
        }
        if ($value instanceof Zend_Date) {
            $value = $value->get($this->getFormat());
        }
        $this->_value = $value;
        return $this;
    }

}