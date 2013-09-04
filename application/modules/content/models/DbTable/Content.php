<?php

class Content_Model_DbTable_Content
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content';
    protected $_primary      = 'id_content';
    protected $_modelClass   = 'Content_Model_Content';
    protected $_enablePrefix = true;

    protected $_dependentTables = array(
        'Content_Model_DbTable_ContentFile',
        'Content_Model_DbTable_ContentRelationship',
        'Content_Model_DbTable_LayoutPage',
        'Content_Model_DbTable_ContentUser',
        'Content_Model_DbTable_ContentMetafield',
        'Content_Model_DbTable_Comment',
    );

    protected $_referenceMap = array(
        'User' => array(
            'columns' => 'id_user',
            'refTableClass' => 'Acl_Model_DbTable_User',
            'refColumns' => 'id_user'
        ),
        'Type' => array(
            'columns' => 'id_content_type',
            'refTableClass' => 'Content_Model_DbTable_ContentType',
            'refColumns' => 'id_content_type'
        )
    );

    /**
     * Busca um Content_Model_Content pelo slug
     *
     * @param String $slug
     * @return Content_Model_Content
     */
    public function findBySlug($slug)
    {
        return FrontZend_Container::get('Content')
                ->findOne(array('where' => array('slug = ?' => $slug)));
    }

}

