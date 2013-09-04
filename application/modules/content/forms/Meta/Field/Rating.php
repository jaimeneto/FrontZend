<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Meta_Field_Rating extends Zend_Form_Element_Radio
{
    public function __construct($spec, $options = null)
    {
        $half = isset($options['half']) ? $options['half'] : 0;
        $multiOptions = $options['multiOptions'][$half];
        unset($options['half'], $options['multiOptions']);

        $this->setMultiOptions($multiOptions);
        $this->setAttrib('label_class', 'rating inline')
             ->setSeparator('');
        
        parent::__construct($spec, $options);
    }

}
