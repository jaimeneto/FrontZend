<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Navigation
 * @subpackage Page
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_Navigation_Page_Uri
 */
require_once 'Zend/Navigation/Page/Uri.php';

class FrontZend_Navigation_Page_Url extends Zend_Navigation_Page_Uri
{

    /**
     * Returns URL
     *
     * @return string
     */
    public function getUri()
    {
        $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
        return $baseUrl . '/' . ltrim($this->_uri, '/');
    }

}
