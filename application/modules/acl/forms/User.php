<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Form_User extends Twitter_Bootstrap_Form_Horizontal
{
    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));
        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
        $this->addPrefixPath('Twitter_Bootstrap_Form', 'Twitter/Bootstrap/Form');

        if (isset($options['edit']) && $options['edit']) {
            $this->_edit = (bool) $options['edit'];
            unset($options['edit']);
        }
        $this->initElements();
        $this->initInfo();

        parent::__construct($options);

        $this->initButtons();
    }

    public function initElements()
    {
        $tbRole = new Acl_Model_DbTable_Role();
        $roles = array(
            ''                 => 'Selecione um tipo de usuário',
            'Tipos de usuário' => $tbRole->fetchPairs('role')
        );
        $this->addElement('select', 'id_role', array(
            'label'        => 'Tipo de usuário',
            'class'        => 'input-block-level',
            'multiOptions' => $roles,
        ));
        $groupElements[] = 'id_role';

        $this->addElement('text', 'name', array(
            'label' => 'Nome',
            'class' => 'input-block-level',
        ));
        $groupElements[] = 'name';

        $this->addElement('text', 'display_name', array(
            'label' => 'Nome de exibição',
            'class' => 'input-block-level',
        ));
        $groupElements[] = 'display_name';

        $this->addElement('text', 'email', array(
            'label'   => 'E-mail',
            'class'   => 'input-block-level',
            'prepend' => '<i class="icon-envelope"></i>'
        ));
        $groupElements[] = 'email';

        $this->addElement('text', 'avatar', array(
            'label'       => 'Avatar',
            'class'       => 'input-block-level',
            'prepend'     => '<i class="icon-picture"></i>',
            'description' => 'Será usado <a href="http://www.gravatar.com" '
                           . 'target="_blank">gravatar</a>, se o e-mail '
                           . 'estiver associado.'
        ));
        $groupElements[] = 'avatar';

        $this->addElement('text', 'username', array(
            'label' => 'Nome de usuário',
            'class' => 'input-small',
            'prepend' => '<i class="icon-user"></i>'
        ));
        $groupElements[] = 'username';

        $this->addElement('password', 'password', array(
            'label' => 'Senha',
            'class' => 'input-small',
            'prepend' => '<i class="icon-lock"></i>'
        ));
        $groupElements[] = 'password';

        if ($this->_edit) {
            $this->addElement('password', 'password2', array(
                'label'      => 'Repita a senha',
                'class'      => 'input-small',
                'validators' => array(
                    array('Identical', false, array('token' => 'password'))
                ),
                'prepend'    => '<i class="icon-lock"></i>'
            ));
            $groupElements[] = 'password2';
        }

        // RIGHT COLUMN

        $this->addElement('radio', 'gender', array(
            'label'        => 'Sexo',
            'label_class'  => 'inline',
            'separator'    => '',
            'multiOptions' => array(
                'M' => 'Masculino',
                'F' => 'Feminino'
            ),
        ));
        $groupElements[] = 'gender';

        $this->addElement('datetime', 'birthdate', array(
            'label'    => 'Data de aniversário',
            'class'    => 'input-block-level',
            'format'   => 'dd/MM/yyyy',
            'max-year' => Zend_Date::now()->subYear(1)->get('yyyy')
        ));
        $groupElements[] = 'birthdate';
               
        $this->addElement('radio', 'status', array(
            'label'        => 'Status',
            'label_class'  => 'inline',
            'value'        => 'I',
            'separator'    => '',
            'multiOptions' => array(
                'A' => 'Ativo',
                'I' => 'Inativo',
                'B' => 'Bloqueado'
            ),
        ));
        $groupElements[] = 'status';

        if ($this->_edit) {
            $this->addElement('hidden', 'id_user');
            $groupElements[] = 'id_user';
        }

        $this->addDisplayGroup(
            $groupElements, 'main_elements', array(
                'legend'     => 'Dados gerais',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane active',
                        'id'    => 'main_elements'
                    ))
                )
            )
        );
    }

    public function initInfo()
    {
        $this->addElement('UneditableTextfield', 'date_registered', array(
            'label'  => 'Data do cadastro',
            'class'  => 'span7',
            'ignore' => true
        ));
        $groupElements[] = 'date_registered';

        $this->addElement('UneditableTextfield', 'date_activated', array(
            'label'  => 'Data de ativação',
            'class'  => 'span7',
            'ignore' => true
        ));
        $groupElements[] = 'date_activated';

        $this->addElement('UneditableTextfield', 'date_updated', array(
            'label'  => 'Última atualização',
            'class'  => 'span7',
            'ignore' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addElement('UneditableTextfield', 'date_lastaccess', array(
            'label'  => 'Último acesso',
            'class'  => 'span7',
            'ignore' => true
        ));
        $groupElements[] = 'date_lastaccess';

        $this->addElement('UneditableTextfield', 'activation_key', array(
            'label'  => 'Chave de ativação',
            'class'  => 'span7',
            'ignore' => true
        ));
        $groupElements[] = 'activation_key';

        $this->addDisplayGroup(
            $groupElements, 'content_info', array(
                'legend'     => 'Informações',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'class' => 'tab-pane form-horizontal',
                        'id'    => 'content_info'
                    ))
                )
            )
        );
    }

    public function initButtons()
    {
        $this->getElement('id_role')->addDecorator(
            array('column-group' => 'HtmlTag'),
            array(
                'tag' => 'div',
                'class' => 'span7 form-horizontal',
                'openOnly' => true
            )
        );

        $this->getElement($this->_edit ? 'password2' : 'password')
            ->addDecorator(
                array('column-group' => 'HtmlTag'),
                array('tag' => 'div', 'closeOnly' => true)
            );
        $this->getElement('avatar')->getDecorator('Description')
            ->setOption('escape', false);

        $this->getElement('gender')->addDecorator(
            array('column-group' => 'HtmlTag'),
            array(
                'tag' => 'div',
                'class' => 'span3',
                'style' => 'clear:right',
                'openOnly' => true
            )
        );

        $this->getElement('status')->addDecorator(
            array('column-group' => 'HtmlTag'),
            array('tag' => 'div', 'closeOnly' => true));

        // BUTTONS

        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));

        $this->addElement('submit', 'apply', array(
            'label'      => 'Aplicar',
            'class'      => 'btn-large',
            'ignore'     => true,
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS
        ));

        $this->addElement('submit', 'cancel', array(
            'label'  => 'Cancelar',
            'class'  => 'btn-large',
            'ignore' => true
        ));

        $this->addFormActions(array('save', 'apply', 'cancel'));
    }

    public function init()
    {
        $this->setAttrib('class', 'tab-content');

        $if = Acl_Model_User::getInputFilter();

        foreach ($if as $name => $options) {
            if ($name == 'password' && $this->_edit) {
                $options['required'] = false;
            }
            $element = $this->getElement($name);
            if ($element) {
                $element->setOptions($options);
            }
        }
    }

    public function populate(array $values)
    {
        if (isset($values['id_user'])) {
            $user = FrontZend_Container::get('AclUser')->findById($values['id_user']);

            $values['date_registered'] = ucfirst($user->getDateRegistered()
                    ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));

            $values['date_activated'] = $user->dt_activated 
                    ? ucfirst($user->getDateActivated()
                        ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"))
                    : '';
            
            $values['date_updated'] = ucfirst($user->getDateUpdated()
                    ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"));
            
            $values['date_lastaccess'] = $user->dt_lastaccess ? 
                    ucfirst($user->getLastAccess()
                        ->get("EEEE, dd 'de' MMMM 'de' yyyy 'às' HH:mm"))
                    : 'nunca';

            $values['activation_key'] = $user->activation_key;
        }

        return parent::populate($values);
    }

    public function persistData()
    {
        $values = $this->getValues();

        if ($values) {
            $values['dt_updated'] = Zend_Date::now()->get('yyyy-MM-dd HH:mm:ss');
            if (!isset($values['id_user'])) {
                $user = FrontZend_Container::get('AclUser')->createRow();
                $values['dt_registered'] = $values['dt_updated'];
                $values['activation_key'] = uniqid();
            } else {
                unset($values['dt_registered']);
                $user = FrontZend_Container::get('AclUser')->findById($values['id_user']);
            }

            $values['birthdate'] = $this->getElement('birthdate')
                ->getFormatedValue('yyyy-MM-dd');

            $user->setFromArray($values);
            return FrontZend_Container::get('AclUser')->save($user);
        }
    }
    
}