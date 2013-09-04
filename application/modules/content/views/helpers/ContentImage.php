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
 * @author Jaime Neto
 */
class Content_View_Helper_ContentImage extends Zend_View_Helper_Abstract
{      
    public function contentImage(Content_Model_Content $content,
        $description, $width=null, $height=null, $enableEmpty = true)
    {
        $contentFiles = $content->getFiles($description);

        if (!$contentFiles) {
            return $enableEmpty
                ? '<div class="' . $description . '"></div>'
                : '';
        }

        $contentFile = $contentFiles->current();

        if (!$contentFile) {
            return $enableEmpty
                ? '<div class="' . $description . '"></div>'
                : '';
        }

        $file = $contentFile->getFile();

        $path = $file->getPath();
        $path = $width && $height
            ? $this->view->thumb($file, $width, $height)
            : $this->view->baseUrl($path);

        $xhtml = '<div class="' . $description . '">'
            . '<img src="' . $path . '" alt="' . $file->info . '" />'
            . '</div>';

        return $xhtml;
    }
}
