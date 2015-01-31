<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Form_User extends Bootstrap_Form_Horizontal
{
    protected $_edit = false;

    public function __construct($options = null)
    {
        $this->setAttrib('id', strtolower(__CLASS__));
        $this->addPrefixPath('FrontZend_Form', 'FrontZend/Form');
        $this->addPrefixPath('Bootstrap_Form', 'Bootstrap/Form');

        if (isset($options['edit']) && $options['edit']) {
            $this->_edit = (bool) $options['edit'];
            unset($options['edit']);
        }
        
        parent::__construct($options);
        
        $this->initElements();
        $this->initInfo();
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
            'multiOptions' => $roles,
        ));
        $groupElements[] = 'id_role';

        $this->addElement('text', 'name', array(
            'label' => 'Nome'
        ));
        $groupElements[] = 'name';

        $this->addElement('text', 'display_name', array(
            'label' => 'Nome de exibição'
        ));
        $groupElements[] = 'display_name';

        $this->addElement('text', 'email', array(
            'label'         => 'E-mail',
            'prepend'       => '<span class="glyphicon glyphicon-envelope"></span>'
        ));
        $groupElements[] = 'email';

        $this->addElement('text', 'avatar', array(
            'label'         => 'Avatar',
            'prepend'       => '<span class="glyphicon glyphicon-picture"></span>',
            'description'   => 'Será usado <a href="http://www.gravatar.com" '
                             . 'target="_blank">gravatar</a>, se o e-mail '
                             . 'estiver associado.'
        ));
        $groupElements[] = 'avatar';

        $this->addElement('text', 'username', array(
            'label'     => 'Nome de usuário',
            'class'     => 'input-small',
            'prepend'   => '<span class="glyphicon glyphicon-user"></span>'
        ));
        $groupElements[] = 'username';

        $this->addElement('password', 'password', array(
            'label' => 'Senha',
            'class' => 'input-small',
            'prepend' => '<span class="glyphicon glyphicon-lock"></span>'
        ));
        $groupElements[] = 'password';

        if ($this->_edit) {
            $this->addElement('password', 'password2', array(
                'label'      => 'Repita a senha',
                'class'      => 'input-small',
                'validators' => array(
                    array('Identical', false, array('token' => 'password'))
                ),
                'prepend'    => '<span class="glyphicon glyphicon-lock"></span>'
            ));
            $groupElements[] = 'password2';
        }

        // RIGHT COLUMN

        $this->addElement('radio', 'gender', array(
            'label'        => 'Sexo',
            'inline'       => true,
            'multiOptions' => array(
                'M' => 'Masculino',
                'F' => 'Feminino'
            ),
        ));
        $groupElements[] = 'gender';

        $this->addElement('datetime', 'birthdate', array(
            'label'    => 'Data de aniversário',
            'format'   => 'dd/MM/yyyy',
            'max-year' => Zend_Date::now()->subYear(1)->get('yyyy')
        ));
        $groupElements[] = 'birthdate';
               
        $this->addElement('radio', 'status', array(
            'label'        => 'Status',
            'value'        => 'I',
            'inline'       => true,
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
                        'role'  => 'tabpanel',
                        'class' => 'tab-pane form-horizontal active',
                        'id'    => 'main_elements'
                    ))
                )
            )
        );
    }

    public function initInfo()
    {
        $this->addElement('StaticText', 'date_registered', array(
            'label'    => 'Data do cadastro',
            'class'    => 'form-control',
            'ignore'   => true,
            'disalbed' => true
        ));
        $groupElements[] = 'date_registered';

        $this->addElement('StaticText', 'date_activated', array(
            'label'    => 'Data de ativação',
            'class'    => 'form-control',
            'ignore'   => true,
            'disalbed' => true
        ));
        $groupElements[] = 'date_activated';

        $this->addElement('StaticText', 'date_updated', array(
            'label'    => 'Última atualização',
            'class'    => 'form-control',
            'ignore'   => true,
            'disalbed' => true
        ));
        $groupElements[] = 'date_updated';

        $this->addElement('StaticText', 'date_lastaccess', array(
            'label'    => 'Último acesso',
            'class'    => 'form-control',
            'ignore'   => true,
            'disalbed' => true
        ));
        $groupElements[] = 'date_lastaccess';

        $this->addElement('StaticText', 'activation_key', array(
            'label'    => 'Chave de ativação',
            'class'    => 'form-control',
            'ignore'   => true,
            'disalbed' => true
        ));
        $groupElements[] = 'activation_key';

        $this->addDisplayGroup(
            $groupElements, 'content_info', array(
                'legend'     => 'Informações',
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array(
                        'tag'   => 'div',
                        'role'  => 'tabpanel',
                        'class' => 'tab-pane form-horizontal',
                        'id'    => 'content_info'
                    ))
                )
            )
        );
    }

    public function initButtons()
    {
        $this->getElement('avatar')->getDecorator('Description')
            ->setOption('escape', false);

        // BUTTONS

        $this->addElement('submit', 'save', array(
            'label'       => 'Salvar',
            'ignore'      => true,
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'apply', array(
            'label'         => 'Aplicar',
            'ignore'        => true,
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
        ));

        $this->addElement('submit', 'cancel', array(
            'label'         => 'Cancelar',
            'buttonType'    => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
            'size'          => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'        => true
        ));
        
        $this->addDisplayGroup(array('save', 'apply', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'class' => 'col-sm-offset-2', 
                    'tag'   => 'div',
                    'style' => 'clear:both'
                ))
            ),
        ));
    }

    public function init()
    {
        $this->setAttrib('class', 'tab-content');

        $model = new Acl_Model_User();
        $if = $model->getInputFilter();
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