<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Core_View_Helper_AdminBaseUrl extends Zend_View_Helper_Abstract
{
    public function adminBaseUrl($file = null)
    {
        $url = $file ? ADMIN_ROUTE . "/{$file}" : ADMIN_ROUTE;
        return $this->view->baseUrl($url);
    }

}
