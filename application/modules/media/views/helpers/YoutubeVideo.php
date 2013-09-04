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

class Media_View_Helper_YoutubeVideo extends Zend_View_Helper_HtmlElement
{
    private $_defaultOptions = array(
        'showinfo' => 0,
        'rel'      => 0,
        'wmode'    => 'transparent',
        'autohide' => 0,
        'autoplay' => 0
    );

    public function youtubeVideo($idVideo, $width=320, $height=240,
        array $options=null, array $attribs=null)
    {
        $options = array_merge($this->_defaultOptions, (array) $options);
        $arrayOptions = array();
        foreach($options as $opt => $val) {
            $arrayOptions[] = "{$opt}={$val}";
        }

        $xhtml = '<iframe id="youtube_video_' . $idVideo . '" '
               . 'class="youtube-video" src="http://www.youtube.com/embed/'
               . $idVideo . '?' . implode('&amp;', $arrayOptions) . '" '
               . $this->_htmlAttribs($attribs) . 'width="' . $width . '" '
               . 'height="' . $height . '" frameborder="0" '
               . 'allowfullscreen> </iframe>';
        
        return $xhtml;
    }

}