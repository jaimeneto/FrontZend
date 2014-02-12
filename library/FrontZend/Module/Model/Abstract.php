<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class FrontZend_Module_Model_Abstract
    extends Zend_Db_Table_Row_Abstract
{
    const DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    public function __construct(array $config = array())
    {
        if (!isset($config['table']) && !$this->_tableClass) {
            $this->_tableClass =
                str_replace('_Model_', '_Model_DbTable_', get_class($this));
        }
        parent::__construct($config);
    }

    public function __get($columnName)
    {
        if ($columnName == 'id') {
            return $this->getId();
        }
        return parent::__get($columnName);
    }

    public function getPrimaryKey($useDirty = true)
    {
        return $this->_getPrimaryKey($useDirty);
    }
    
    public function getIdField()
    {
        return current($this->_primary);
    }

    public function getId()
    {
        return current($this->getPrimaryKey());
    }

    /**
     *
     * @return Zend_Date
     */
    public function getDate($columnName, $format=self::DATETIME_FORMAT)
    {
        $date = $this->{$columnName};

        return $date
            ? new Zend_Date($date, $format)
            : null;
    }

    public function getInputFilter();
}