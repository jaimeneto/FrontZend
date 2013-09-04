<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_View_Helper_CssVisibility extends Zend_View_Helper_Abstract
{
//    Class             Phones            Tablets         Desktops
//                      767px and below   979px to 768px  Default
//    .visible-phone   	Visible           Hidden          Hidden
//    .visible-tablet 	Hidden            Visible         Hidden
//    .visible-desktop 	Hidden            Hidden          Visible
//    .hidden-phone     Hidden            Visible         Visible
//    .hidden-tablet    Visible           Hidden          Visible
//    .hidden-desktop   Visible           Visible         Hidden

    public function cssVisibility($block)
    {
        if (!$block->hasOption('visibility')) return;

        $visibility = $block->getOption('visibility');

        if (!$visibility) return;

        if (in_array('desktop', $visibility)) {
            if (in_array('tablet', $visibility)) {
                if (!in_array('phone', $visibility)) {
                    return 'hidden-phone';
                }
            } else if (in_array('phone', $visibility)) {
                return 'hidden-tablet';
            } else {
                return 'visible-desktop';
            }
        } else if (in_array('tablet', $visibility)) {
            if (!in_array('phone', $visibility)) {
                return 'visible-tablet';
            } else {
                return 'hidden-desktop';
            }
        } else if (in_array('phone', $visibility)) {
            return 'visible-phone';
        }
    }

}
