<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_Content extends FrontZend_Module_Model_Abstract
{
    const STATUS_ACTIVE   = 'A';
    const STATUS_INACTIVE = 'I';
    const STATUS_DELETED  = 'D';
    const STATUS_LOCKED   = 'L';

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
     * @return Zend_Date
     */
    public function getDatePublished()
    {
        return parent::getDate('dt_published');
    }

    /**
     *
     * @return array
     */
    public function getKeywords()
    {
        $keywords = $this->keywords;
        $keywordsArray = explode(',', $keywords);
        foreach($keywordsArray as &$value) {
            $value = trim($value);
        }
        return $keywordsArray;
    }

    /**
     * Test if status is active or locked and date published is greater then
     * current datetime
     *
     * @return boolean
     */
    public function isPublished()
    {
        $datePublished = $this->getDatePublished();
        return $this->getId() && ($this->status == self::STATUS_ACTIVE ||
                $this->status == self::STATUS_LOCKED) &&
                Zend_Date::now()->compare($datePublished) >= 0;
    }

    /**
     *
     * @return Content_Model_ContentType
     */
    public function getContentType()
    {
        $idContentType = $this->id_content_type;
        if ($idContentType) {
            return FrontZend_Container::get('ContentType')->findById($idContentType);
        }
    }

    /**
     * Another way to call getContentType
     *
     * @return Content_Model_ContentType
     */
    public function getType()
    {
        return $this->getContentType();
    }

    /**
     *
     * @return Content_Model_UserGroup
     */
    public function getGroup()
    {
        $idGroup = $this->id_group;
        if ($idGroup) {
            return FrontZend_Container::get('UserGroup')->findById($idGroup);
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

    public function countComments()
    {
        $count = 0;
        if ($this->getId()) {
            $count = FrontZend_Container::get('Comment')->count(
                array('id_content = ?' => $this->getId())
            );
        }
        return $count;
    }

    /**
     *
     * @return array
     */
    public function getMetafields()
    {
        $idContent = $this->getId();
        if ($idContent) {
            return FrontZend_Container::get('ContentMetafield')->findAll(
                array('where' => array('id_content = ?' => $idContent))
            );
        }
    }

    /**
     *
     * @param String $fieldname
     * @return Content_Model_ContentMetafield
     */
    public function getMetafield($fieldname)
    {
        $contentMetafields = $this->getMetafields();
        if ($contentMetafields) {
            foreach($contentMetafields as $contentMetafield) {
                $metafield = $contentMetafield->getMetafield();
                if ($metafield->fieldname == $fieldname) {
                    return $contentMetafield;
                }
            }
        }
    }

    /**
     *
     * @return array
     */
    public function getRelationships($fieldname=null)
    {
        $idContent = $this->getId();
        if ($idContent) {
            $where = array(
                'id_content_a = ? OR id_content_b = ?' => $idContent
            );
            if ($fieldname) {
                $where['rel_type = ?'] = $fieldname;
            }
            return FrontZend_Container::get('ContentRelationship')->findAll(
                array('where' => $where)
            );
        }
    }

    /**
     *
     * @param String $fieldname
     * @return Content_Model_ContentRelationship
     */
    public function getRelationship($fieldname)
    {
        $contentRelationships = $this->getRelationships();
        if ($contentRelationships) {
            foreach($contentRelationships as $contentRelationship) {
                if ($contentRelationship->rel_type == $fieldname) {
                    return $contentRelationship;
                }
            }
        }
    }

    /**
     *
     * @param String $fieldname
     * @return array
     */
    public function getUsers($fieldname=null)
    {
        $idContent = $this->getId();
        if ($idContent) {
            $where = array('id_content = ?' => $idContent);
            if ($fieldname) {
                $where['rel_type = ?'] = $fieldname;
            }
            return FrontZend_Container::get('ContentUser')->findAll(array(
                'where' => $where
            ));
        }
    }

    /**
     *
     * @return array
     */
    public function getFiles($description=null)
    {
        $idContent = $this->getId();
        if ($idContent) {
            $where = array('id_content = ?' => $idContent);

            if ($description) {
                $where['description = ?'] = $description;
            }

            return FrontZend_Container::get('ContentFile')->findAll(array(
                'where' => $where,
                'order' => 'order ASC'
            ));
        }
    }

    /**
     * Retorna a página do layout, se já foi criada.
     * Pode procurar pelo tipo de conteúdo, caso não encontre específica do
     * conteúdo, se o primeiro parâmetro for true
     *
     * @param boolean $findByContentType Se não encontrar, procura pelo tipo
     * @return Layout_Model_Page
     */
    public function getLayoutPage($findByContentType=true)
    {
        return FrontZend_Container::get('LayoutPage')->findByContent(
            $this->getId(), $findByContentType);
    }

//    /**
//     * Verifica se o usuário tem acesso ao conteúdo
//     *
//     * @return boolean
//     */
//    public function accessCheck()
//    {
//        // Verifica se o conteúdo tem acesso limitado a um grupo de usuários
//        $idGroup = $this->id_group;
//        if (!$idGroup) {
//            return true;
//        }
//
//        // Verifica se o usuário logado faz parte do grupo de usuários
//        // que tem acesso a este conteúdo
//        $authGroups = Zend_Auth::getInstance()->hasIdentity()
//                ? Zend_Auth::getInstance()->getIdentity()->groups
//                : array();
//
//        return $authGroups && in_array($idGroup, $authGroups);
//    }

    /**
     *
     * @return Array
     */
    public function toArray($includeMetafields=false)
    {
        $contentArray = parent::toArray();

        if ($includeMetafields) {
            $contentMetafields = $this->getMetafields();
            foreach($contentMetafields as $contentMetafield) {
                $metafield = $contentMetafield->getMetafield();
                if ($metafield->getOption('multiple')) {
                    $contentArray['meta'][$metafield->fieldname][]
                        = $contentMetafield->id_metafield;
                } else {
                    $contentArray['meta'][$metafield->fieldname]
                        = $contentMetafield->value;
                }
            }

            $contentRelationships = $this->getRelationships();
            foreach($contentRelationships as $contentRelationship) {
                $idContent = $this->getId() == $contentRelationship->id_content_a
                    ? $contentRelationship->id_content_b
                    : $contentRelationship->id_content_a;
                $contentArray['meta'][$contentRelationship->rel_type][]
                    = $idContent;
            }

            $contentUsers = $this->getUsers();
            foreach($contentUsers as $contentUser) {
                $contentArray['meta'][$contentUser->rel_type][]
                    = $contentUser->id_user;
            }

            $contentFiles = $this->getFiles();
            foreach($contentFiles as $contentFile) {
//                $metafield = $contentFile->getMetafield();
//                if ($metafield->getOption('multiple')) {
                    $contentArray['meta'][$contentFile->description][]
                        = $contentFile->id_file;
//                } else {
//                    $contentArray['meta'][$contentFile->description]
//                        = $contentFile->id_file;
//                }
            }

//            pr($contentArray,1);
        }

        return $contentArray;
    }

    static public function getInputFilter()
    {
        return array(
            'id_content' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_content_type' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content_type',
//                            'field' => 'id_content_type',
//                        ),
//                    ),
//                ),
            ),
            
            'id_user' => array(
                'required' => true,
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
            
            'title' => array(
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
                            'max'      => 255,
                        ),
                    ),
                ),
            ),
            
            'slug' => array(
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
                            'max'      => 255,
                        ),
                    ),
                ),
            ),
            
            'text' => array(
                'filters' => array(
                    'StringTrim'
                ),
            ),
            
            'excerpt' => array(
                'filters' => array(
                    'StringTrim'
                ),
            ),
            
            'keywords' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags',
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
            
            'dt_published' => array(
                'required' => true,
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
//                            'haystack' => array('A', 'I', 'L', 'D'),
//                        ),
//                    ),
                ),
            ),
            
            'fixed' => array(
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
            
            'id_group' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'group',
//                            'field' => 'id_group',
//                        ),
//                    ),                            
                ),
            ),
        );
    }
    
}