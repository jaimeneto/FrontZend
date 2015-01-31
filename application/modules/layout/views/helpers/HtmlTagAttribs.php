<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */
class Layout_View_Helper_HtmlTagAttribs extends Zend_View_Helper_Abstract
{

    public function htmlTagAttribs(Layout_Model_Block $block, 
            array $options=null)
    {
        $attribs = array();

        $class = array(
            'block' ,
            'block-' . $block->block
        );

        $optClass = $block->getOption('class');
        if ($optClass) {
            $class[] = $optClass;
        }
        
        $cssClass = $block->getOption('css_class');
        if ($cssClass) {
            $class[] = $cssClass;
        }
        
        $visibility = $block->getOption('visibility');
        
        if ($visibility) {
            if (in_array(count($visibility), array(1,2))) {
                foreach($visibility as $screenSize) {
                    $class[] = 'visible-' . $screenSize;
                }
            } elseif (count($visibility) == 3) {
                $screenSizes = array('xs', 'sm', 'md', 'lg');
                foreach($screenSizes as $screenSize) {
                    if (!in_array($screenSize, $visibility)) {
                        $class[] = 'hidden-' . $screenSize;
                    }
                }
            }
        }
        
        if (!isset($options['id'])) {
            $cssId = $block->getOption('css_id');
            if ($cssId) {
                $attribs[] = 'id="' . $cssId . '"';
            }
        }
        
        if ($options) {
            foreach($options as $nm => $opt) {
                if ($nm == 'class' && !in_array($opt, $class)) {
                    $class[] = $opt;
                } elseif ($nm != 'id') {
                    $attribs[] = $nm . '="' . $opt . '"';
                }
            }
        }
        
        $attribs[] = 'class="' . implode(' ', $class) . '"';
        
        return implode(' ', $attribs);
    }

}
