<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_User extends FrontZend_Module_Model_Abstract
{
    const STATUS_ACTIVE   = 'A';
    const STATUS_INACTIVE = 'I';
    const STATUS_BLOCKED  = 'B';
    const STATUS_DELETED  = 'D';

    public function isActive()
    {
        return $this->status == self::STATUS_ACTIVE;
    }

    public function __set($columnName, $value)
    {
        if ($columnName == 'password') {
            $md5Validator = new FrontZend_Validate_Md5();
            if (!$md5Validator->isValid($value)) {
                $value = md5($value);
            }
        }

        return parent::__set($columnName, $value);
    }
    
    /**
     *
     * @return Zend_Date
     */
    public function getLastAccess()
    {
        return parent::getDate('dt_lastaccess');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getDateRegistered()
    {
        return parent::getDate('dt_registered');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getDateActivated()
    {
        return parent::getDate('dt_activated');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getDateUpdated()
    {
        return parent::getDate('dt_updated');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getBirthdate()
    {
        return parent::getDate('dt_updated', 'yyyy-MM-dd');
    }

    /**
     *
     * @return Acl_Model_Role
     */
    public function getRole()
    {
        $idRole = $this->id_role;
        if ($idRole) {
            return FrontZend_Container::get('AclRole')->findById($idRole);
        }
    }

    /**
     *
     * @return integer
     */
    public function countComments()
    {
        $idUser = $this->getId();
        if ($idUser) {
            return FrontZend_Container::get('Comment')->count(array(
                'id_user = ?' => $idUser
            ));
        }
    }

    /**
     *
     * @return integer
     */
    public function countContents()
    {
        $idUser = $this->getId();
        if ($idUser) {
            return FrontZend_Container::get('Content')->count(array(
                'id_user = ?' => $idUser
            ));
        }
    }

    /**
     *
     * @return integer
     */
    public function countFiles()
    {
        $idUser = $this->getId();
        if ($idUser) {
            return FrontZend_Container::get('File')->count(array(
                'id_user = ?' => $idUser
            ));
        }
    }

    public function getInputFilter()
    {
        return array(
            'id_user' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'id_role' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'acl_role',
//                            'field' => 'id_role',
//                        ),
//                    ),
//                ),
            ),

            'username' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 30,
                        ),
                    ),
                ),
            ),

            'password' => array(
                'required' => true,
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 32,
                            'max'      => 32,
                        ),
                    ),
                ),
            ),

            'email' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    'EmailAddress',
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max'      => 100,
                        ),
                    ),
                ),
            ),

            'name' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 60,
                        ),
                    ),
                ),
            ),

            'display_name' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 20,
                        ),
                    ),
                ),
            ),

            'gender' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'InArray',
//                        'options' => array(
//                            'haystack' => array('M', 'F'),
//                        ),
//                    ),
                ),
            ),

            'birthdate' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd',
                        ),
                    ),
                ),
            ),

            'avatar' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max'      => 255,
                        ),
                    ),
                ),
            ),

            'dt_registered' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),
                ),
            ),

            'dt_updated' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),
                ),
            ),

            'dt_lastaccess' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),
                ),
            ),

            'dt_activated' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),
                ),
            ),

            'activation_key' => array(
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'max'      => 13,
                        ),
                    ),
                ),
            ),

            'status' => array(
                'required' => true,
                'validators' => array(
//                    array(
//                        'name'    => 'InArray',
//                        'options' => array(
//                            'haystack' => array('A', 'I', 'B'),
//                        ),
//                    ),
                ),
            ),
        );
    }
    
}