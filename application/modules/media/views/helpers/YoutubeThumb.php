<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_View_Helper_HtmlElement
 */
require_once 'Zend/View/Helper/HtmlElement.php';

class Media_View_Helper_YoutubeThumb extends Zend_View_Helper_HtmlElement
{
    public function youtubeThumb($idVideo, $width=null, $height=null, $imgNum=0)
    {			   
        $xhtml = '<img class="youtube-thumb" '
               . 'src="http://img.youtube.com/vi/'
               . $this->view->escape($idVideo)
               . '/' . $imgNum . '.jpg" '
               . 'border="0" />';
        
        return $xhtml;
    }



}
