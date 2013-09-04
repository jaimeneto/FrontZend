<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Comments extends Zend_Form
{  
    public function init()
    {
        $this->setDisableLoadDefaultDecorators(true)
            ->setMethod('post')
            ->setAttrib('class', 'content-form-comments bbcode')
            ->addDecorator('FormElements')
            ->addDecorator('Form');

        // id_comment
        $this->addElement('hidden', 'id_comment', array(
            'decorators' => array('ViewHelper')
        ));

        // id_content
        $this->addElement('hidden', 'id_content', array(
            'decorators' => array('ViewHelper')
        ));

        // guest elements
        if (!Zend_Auth::getInstance()->hasIdentity()) {
            $this->_guestElements();
        }

        // comments
        $this->addElement('textarea', 'comments', array(
            'required'    => true,
            'decorators'  => array('ViewHelper'),
            'class'       => 'input-block-level',
            'rows'        => 3
        ));

        // btn_comment
        $this->addElement('submit', 'btn_comment', array(
            'ignore'     => true,
            'label'      => 'comentar',
            'class'      => 'btn',
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array(
                    'tag' => 'div', 'class' => 'btn-wrapper'
                )),
            )
        ));
    }

    protected function _guestElements()
    {
        // name
        $this->addElement('text', 'name', array(
            'label'      => 'Nome',
            'required'   => true,
            'maxlength'  => 30,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array('StringLength', false, array(0, 30))
            ),
            'decorators' => array(
                array('Label', array('requiredSuffix' => ' *')),
                'ViewHelper'
            )
        ));

        // email
        $this->addElement('text', 'email', array(
            'label'      => 'E-mail',
            'required'   => true,
            'maxlength'  => 100,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                'EmailAddress',
                array('StringLength', false, array(0, 100))
            ),
            'decorators' => array(
                array('Label', array('requiredSuffix' => ' *')),
                'ViewHelper'
            )
        ));

        // site
        $this->addElement('text', 'site', array(
            'label'      => 'Site',
            'required'   => true,
            'maxlength'  => 255,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                new FrontZend_Validate_Link(),
                array('StringLength', false, array(0, 255))
            ),
            'decorators' => array(
                'Label',
                'ViewHelper'
            )
        ));

        // info
        $this->addElement('text', 'info', array(
            'label'      => 'Cidade / Estado',
            'required'   => true,
            'maxlength'  => 255,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array('StringLength', false, array(0, 255))
            ),
            'decorators' => array(
                'Label',
                'ViewHelper'
            )
        ));
    }

    public function persistData()
    {
        $values = $this->getValues();
        if ($values) {
            if (Zend_Auth::getInstance()->hasIdentity()) {
                $auth = Zend_Auth::getInstance()->getIdentity();
                $values['id_user'] = $auth->id_user;
            }

            $values['status'] = Content_Model_Comment::STATUS_ACTIVE;

            $comment = FrontZend_Container::get('Comment')->createRow($values);

            return FrontZend_Container::get('Comment')->save($comment);
        }
    }

}