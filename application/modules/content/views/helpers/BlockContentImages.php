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
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/Abstract.php';

class Content_View_Helper_BlockContentImages extends Zend_View_Helper_Abstract
{
    public function blockContentImages(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        /* OPTIONS:
         * title, image, width, height, fill, template, visibility
         */

        $image = $block->getOption('image');
        $contentImages = $content->getFiles($image);
        $images = array();
        if ($contentImages) {
            foreach($contentImages as $contentImage) {
                $file = $contentImage->getFile();
                $width = $block->getOption('width');
                $height = $block->getOption('height');
                $fill = $block->getOption('fill');

                $images[$file->id] = $width || $height
                    ? $this->view->thumb($file, $width, $height, $fill)
                    : $file->getPath();
            }
        }

        // Define vars to be used on template
        $vars = array(
            'block'   => $block,
            'content' => $content,
            'user'    => $user,
            'images'  => $images
        );
        
        try {
            $template = $block->getOption('template');
            $script = "blocks/content-images/{$template}";
            $xhtml = $this->view->partial($script, $vars);
        } catch(Zend_View_Exception $e) {
            // If defined template is not found, calls the default one
            $script = 'blocks/content-images/default.phtml';
            $xhtml = $this->view->partial($script, $vars);
        }

        return $xhtml;
    }
}
