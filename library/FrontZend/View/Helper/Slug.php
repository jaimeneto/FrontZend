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
 * Converts a string to be used on an url with valid characters
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 */
class FrontZend_View_Helper_Slug extends Zend_View_Helper_Abstract
{
    /**
     * Filters the provided string.
     *
     * @param  string $value
     * @return string
     * @throws Zend_View_Exception
     */
    public function slug($value)
    {
        require_once 'FrontZend/Filter/Slug.php';
        $filter = new FrontZend_Filter_Slug();
        $slug = $filter->filter($value);

        return $slug;
    }

}
