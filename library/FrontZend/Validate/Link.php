<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Validate
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_Validate_Abstract
 */
require_once 'Zend/Validate/Abstract.php';


/**
 * @category   FrontZend
 * @package    FrontZend_Validate
 */
class FrontZend_Validate_Link extends Zend_Validate_Abstract
{
    const INVALID = 'linkInvalid';

	/**
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID => "'%value%' is not a valid URL"
    );

    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern = '/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i';

    /**
     * @return void
     */
    public function __construct() {}

    /**
     * Defined by Zend_Validate_Interface
     *
     * Returns true if and only if $value matches against the pattern option
     *
     * @param  string $value
     * @throws Zend_Validate_Exception if there is a fatal error in pattern matching
     * @return boolean
     */
    public function isValid($value)
    {
        $valueString = (string) $value;

        $this->_setValue($valueString);

        $validator = new Zend_Validate_Callback(array('Zend_Uri', 'check'));
        if (!$validator->isValid($valueString)) {
            $this->_error(self::INVALID);
            return false;
        }
        return true;
    }

}

