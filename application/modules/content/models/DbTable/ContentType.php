<?php

class Content_Model_DbTable_ContentType
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content_type';
    protected $_primary      = 'id_content_type';
    protected $_modelClass   = 'Content_Model_ContentType';
    protected $_enablePrefix = true;

    protected $_dependentTables = array(
        'Content_Model_DbTable_Content',
    );

    protected $_referenceMap = array(
        'Parent' => array(
            'columns' => 'id_parent',
            'refTableClass' => __CLASS__,
            'refColumns' => 'id_content_type'
        ),
    );

    public function save($data)
    {
        if ($data instanceof $this->_modelClass) {
            $data = $data->toArray();
        }

        if (is_array($data)) {
            $row = $this->findById($data[$this->_primary]);

            if (!$row) {
                $row = $this->createRow();
            }

            $row->setFromArray($data);
        }

        return $row->save();
    }

}

