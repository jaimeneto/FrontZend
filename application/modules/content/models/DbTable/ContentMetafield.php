<?php

class Content_Model_DbTable_ContentMetafield 
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content_metafield';
    protected $_primary      = 'id_content_metafield';
    protected $_modelClass   = 'Content_Model_ContentMetafield';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Metafield' => array(
            'columns' => 'id_metafield',
            'refTableClass' => 'Content_Model_DbTable_Metafield',
            'refColumns' => 'id_metafield'
        ),
        'Content' => array(
            'columns' => 'id_content',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns' => 'id_content'
        )
    );

    /**
     * Busca uma lista de Content_Model_ContentMetafield de um conteúdo.
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

    /**
     * Busca uma lista de Content_Model_ContentMetafield de um metafield.
     * O parâmetro pode ser um objeto Content_Model_Metafield ou o id do metafield
     *
     * @param mixed $metafield
     * @return array
     */
    public function fetchByMetafield($metafield)
    {
        $idMetafield = $metafield instanceof Content_Model_Metafield
            ? $metafield->getId()
            : $metafield;

        $options = array('id_metafield = ?' => $idMetafield);

        return parent::fetchAll($options);
    }

}
