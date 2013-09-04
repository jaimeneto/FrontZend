<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_ContentUser extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Content_Model_Content
     */
    public function getContent()
    {
        $idContent = $this->id_content;
        if ($idContent) {
            return FrontZend_Container::get('Content')->findById($idContent);
        }
    }

    /**
     *
     * @return Acl_Model_User
     */
    public function getUser()
    {
        $idUser = $this->id_user;
        if ($idUser) {
            return FrontZend_Container::get('AclUser')->findById($idUser);
        }
    }

    static public function getInputFilter()
    {
        return array(
            'id_content_user' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'id_content' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content',
//                            'field' => 'id_content',
//                        ),
//                    ),
//                ),
            ),

            'id_user' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'acl_user',
//                            'field' => 'id_user',
//                        ),
//                    ),
//                ),
            ),

            'rel_type' => array(
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

        );
    }
    
}