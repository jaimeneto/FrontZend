<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/Abstract.php';


/**
 * Shortens the string down to maximum length.
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 */
class FrontZend_View_Helper_StringLength extends Zend_View_Helper_Abstract
{
    /**
     * Filters the provided string.
     *
     * @param  string $value
     * @return string
     */
    public function stringLength($string, $maxLength=null, $offset=null, 
            $suffix=null)
    {
        require_once 'FrontZend/Filter/StringLength.php';
        $filterStringLength = new FrontZend_Filter_StringLength($maxLength,
                $offset, $suffix);
        $string = $filterStringLength->filter($string);

        return $string;
    }

}
