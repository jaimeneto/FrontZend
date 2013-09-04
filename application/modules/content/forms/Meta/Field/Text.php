<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Meta_Field_Text extends Zend_Form_Element_Text
{
    public function __construct($spec, $options = null)
    {
        if (!isset($options['attribs']['class'])) {
            $options['attribs']['class'] = 'input-block-level';
        }
        
        parent::__construct($spec, $options);
    }
}
