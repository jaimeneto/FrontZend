<?php

class Content_Model_Comment extends FrontZend_Module_Model_Abstract
{
    const STATUS_ACTIVE   = 'A';
    const STATUS_INACTIVE = 'I';
    const STATUS_DELETED  = 'D';

    /**
     *
     * @return Zend_Date
     */
    public function getDateCreated()
    {
        return parent::getDate('dt_created');
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
     * @return bool
     */
    public function isActive()
    {
        $status = $this->status;
        return $status == self::STATUS_ACTIVE;
    }

    /**
     * @param  Content_Model_Content $content
     * @return Content_Model_Comment
     */
    public function setContent(Content_Model_Content $content)
    {
        if (!$content->getId()) {
            throw new Core_Model_Exception("Content must be saved before
                associate it to a comment");
        }

        $this->id_content = $content->getId();

        return $this;
    }

    /**
     *
     * @return Content_Model_Content
     */
    public function getContent()
    {
        $idContent = $this->id_content;
        if ($idContent) {
            return Container::get('Content')->findById($idContent);
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
            return Container::get('AclUser')->findById($idUser);
        }
    }

    static public function getInputFilter()
    {
        return array(
            'id_comment' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'id_user' => array(
                'filters' => array(
                    'Int'
                ),
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'acl_user',
//                            'field' => 'id_user',
//                        ),
//                    ),
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

            'answer_to' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'comment',
//                            'field' => 'id_comment',
//                        ),
//                    ),
//                ),
            ),

            'name' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 30,
                        ),
                    ),
                ),
            ),

            'email' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'EmailAddress',
                    ),
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 100,
                        ),
                    ),
                ),
            ),


            'site' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 255,
                        ),
                    ),
                ),
            ),

            'info' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 255,
                        ),
                    ),
                ),
            ),

            'comments' => array(
                'required' => true,
            ),

            'spoiler' => array(
                'filters' => array(
                    'Int'
                ),
                'validators' => array(
                    array(
                        'name'    => 'InArray',
                        'options' => array(
                            'haystack' => array(0, 1),
                        ),
                    ),
                ),
            ),

            'dt_created' => array(
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

            'status' => array(
                'required' => true,
                'validators' => array(
//                    array(
//                        'name'    => 'InArray',
//                        'options' => array(
//                            'haystack' => array('A', 'I', 'D'),
//                        ),
//                    ),
                ),
            ),


        );
    }

}

