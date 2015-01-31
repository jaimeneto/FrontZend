<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Meta_Field_Select extends Zend_Form_Element_Multi
{
    public function __construct($spec, $options = null)
    {
        if(isset($options['multiOptions'])) {
            if (!$options['required'] && !$options['multiple']) {
                $options['multiOptions'] = array_merge(
                    array('' => 'Nenhuma das opções'), $options['multiOptions']);
            }
        }

        if (isset($options['display']) && $options['display'] == 'list') {
            if (isset($options['multiple']) && $options['multiple']) {
                $this->helper = 'formMultiCheckbox';
                $this->_isArray = true;
            } else {
                $this->helper = 'formRadio';
            }
            $this->_separator = '';
            $options['inline'] = true;
            unset($options['multiple']);
        } else {
            $options['class'] = 'form-control';
            $this->helper = 'formSelect';
        }
        
        unset($options['display']);
        
        parent::__construct($spec, $options);
    }

}
