<?php

class Content_Model_DbTable_ContentRelationship
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content_relationship';
    protected $_primary      = 'id_content_relationship';
    protected $_modelClass   = 'Content_Model_ContentRelationship';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'ContentA' => array(
            'columns'       => 'id_content_a',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns'    => 'id_content'
        ),
        'ContentB' => array(
            'columns'       => 'id_content_b',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns'    => 'id_content'
        )
    );

    /**
     * Busca uma lista de Content_Model_ContentRelationship de um conteúdo.
     * O parâmetro pode ser um objeto Content_Model_Content ou o id do conteúdo
     *
     * @param mixed $content
     * @param mixed $rel_type
     * @return array
     */
    public function fetchByContent($content, $rel_type=null)
    {
        $idContent = $content instanceof Content_Model_Content
            ? $content->getId()
            : $content;

        $options = array('where' => array(
            'id_content_a = ? OR id_content_b = ?' => $idContent
        ));

        if ($rel_type) {
            $options['where']['rel_type = ?'] = $rel_type;
        }

        return FrontZend_Container::get('ContentRelationship')->findAll($options);
    }

}
