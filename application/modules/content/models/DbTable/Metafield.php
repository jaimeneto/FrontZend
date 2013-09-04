<?php

class Content_Model_DbTable_Metafield 
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'metafield';
    protected $_primary      = 'id_metafield';
    protected $_modelClass   = 'Content_Model_Metafield';
    protected $_enablePrefix = true;

    protected $_dependentTables = array(
        'Content_Model_DbTable_ContentMetafield',
    );

    protected $_referenceMap = array(
        'ContentType' => array(
            'columns' => 'id_content_type',
            'refTableClass' => 'Content_Model_DbTable_ContentType',
            'refColumns' => 'id_content_type'
        )
    );

    public function save($data)
    {
        if (!(isset($data[$this->_primary]) && $data[$this->_primary])) {
            $data['fieldname'] = str_replace(' ', '_', $data['fieldname']);
        }
        return parent::save($data);
    }

    /**
     * Busca uma lista de metafields de um tipo de conteúdo.
     * O parâmetro pode ser um objeto Content_Model_ContentType
     * ou o id do tipo de conteúdo
     *
     * @param mixed $contentType
     * @return array
     */
    public function fetchByContentType($contentType, $datatype=null)
    {
        $idContentType = $contentType instanceof Content_Model_ContentType
            ? $contentType->getId()
            : $contentType;

        $where = array('id_content_type = ?' => $idContentType);
        
        if ($datatype) {
            $where['datatype = ?'] = $datatype;
        }
        
        $options = array('where' => $where);

        return parent::findAll($options);
    }

}

