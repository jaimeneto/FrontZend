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
require_once 'Zend/Validate/Regex.php';


/**
 * @category   FrontZend
 * @package    FrontZend_Validate
 */
class FrontZend_Validate_Md5 extends Zend_Validate_Regex
{
    /**
     * Regular expression pattern
     *
     * @var string
     */
    protected $_pattern = '/^[a-f0-9]{32}$/';

    /**
     * @return void
     */
    public function __construct() {}
}

