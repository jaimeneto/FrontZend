<?php

class Content_Model_DbTable_Comment 
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'comment';
    protected $_primary      = 'id_comment';
    protected $_modelClass   = 'Content_Model_Comment';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'User' => array(
            'columns'       => 'id_user',
            'refTableClass' => 'Acl_Model_DbTable_User',
            'refColumns'    => 'id_user'
        ),
        'Content' => array(
            'columns'       => 'id_content',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns'    => 'id_content'
        )
    );

    /**
     * Busca uma lista de Content_Model_Comment de um conteúdo.
     * O parâmetro pode ser um objeto Content_Model_Content ou o id do conteúdo
     *
     * @param mixed $content
     * @return array
     */
    public function fetchByContent($content)
    {
        $idContent = $content instanceof Content_Model_Content
            ? $content->getId()
            : $content;

        $options = array('id_content = ?' => $idContent);

        return parent::fetchAll($options);
    }
}