<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Block
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Block_Comments extends Layout_Form_Block
{

    protected function _initElements()
    {
        // title
        $this->addElement('text', 'title', array(
            'label'      => 'Título',
            'maxlength'  => 60,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 60,
                    ),
                ),
            ),
            'append' => '<a href="#" rel="tooltip" ' 
                . 'title="Apenas para referência" data-placement="left">'
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));

        // placeholder
        $this->addElement('text', 'placeholder', array(
            'label' => 'Mensagem padrão',
            'value' => 'Deixe seu comentário...'
        ));

        // limit
        $this->addElement('text', 'limit', array(
            'label'      => 'Quantidade máxima',
            'maxlength'  => 3,
            'class'      => 'input-mini',
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                'Digits',
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 3,
                    ),
                ),
            ),
            'append' => '<a href="#" rel="tooltip" title="Deixe em branco '
                . 'para exibir todos" data-placement="right">'
                . '<span class="glyphicon glyphicon-info-sign"></span></a>'
        ));

        // order
        $this->addElement('radio', 'order', array(
            'label'        => 'Ordem',
            'multiOptions' => array(
                'asc'  => 'Ordem de criação',
                'desc' => 'Mais recentes primeiro'
            ),
            'value'        => 'asc'
        ));

        // allow_guest
        $this->addElement('radio', 'allow_guest', array(
            'label'        => 'Visitantes',
            'multiOptions' => array(
                0 => 'não podem comentar',
                1 => 'podem comentar'
            ),
            'value'        => 0
        ));
    }

}
