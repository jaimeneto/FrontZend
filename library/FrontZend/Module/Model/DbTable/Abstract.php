<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Model_DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class FrontZend_Module_Model_DbTable_Abstract
    extends Zend_Db_Table_Abstract implements Countable
{
    protected $_prefix;
    protected $_modelClass;
    protected $_enablePrefix = false;

    const DATETIME_FORMAT = 'yyyy-MM-dd HH:mm:ss';

    public function __construct($config = array())
    {
        if (is_array($this->_primary)) {
            throw new FrontZend_Exception('$_primary must be a string');
        }
        
        if (!is_subclass_of($this->_rowClass,'FrontZend_Module_Model_Abstract'))
        {
            $config['rowClass'] = str_replace('DbTable_', '', get_class($this));
        }
        parent::__construct($config);
    }

    public function setPrefix($prefix)
    {
        $this->_prefix = rtrim($prefix, '_') . '_';
    }

    protected function _setupTableName()
    {
        parent::_setupTableName();

        if ($this->_enablePrefix) {
            if (!$this->_prefix) {
                $bootstrap =
                    Zend_Controller_Front::getInstance()->getParam('bootstrap');
                $options = $bootstrap->getOptions();
                if (isset($options['resources']['db']['prefix'])) {
                    $this->_prefix = $options['resources']['db']['prefix'];
                }
            }
            
            if ($this->_prefix) {
                $this->_name = $this->_prefix . $this->_name;
            }
        }
    }

    public function getPrimaryKey()
    {
        return $this->_primary;
    }

    /**
     * Salva o objeto no banco de dados.
     * Retorna a chave primária do registro em caso de insert
     * ou a quantidade de linhas alteradas em caso de update.
     *
     * @param mixed $data
     * @return integer
     */
    public function save($data)
    {
        if ($data instanceof $this->_modelClass) {
            $data = $data->toArray();
        }

        if (is_array($data)) {
            $row = (isset($data[$this->_primary]) && $data[$this->_primary])
                ? $this->findById($data[$this->_primary])
                : $this->createRow();

            $row->setFromArray($data);
        }

        return $row->save();
    }

    /**
     * Busca um FrontZend_Module_Model_Abstract pelo id
     *
     * @param int $id
     * @return FrontZend_Module_Model_Abstract
     */
    public function findById($id)
    {
        return $this->find($id)->current();
    }

    /**
     * Exclui um registro pelo id
     *
     * @param mixed $id
     * @return int  O número de registros excluídos
     */
    public function deleteById($id)
    {
        if (is_array($this->_primary)) {
            throw new FrontZend_Exception(
                'Não é possível usar deleteById com chaves concatenadas'
            );
        }
        $where = array("{$this->_primary} = ?" => $id);
        return $this->delete($where);
    }

    /**
     * Retorna uma lista de objetos FrontZend_Module_Model_Abstract
     * Caso receba uma $option['page'], retorna um Zend_Paginator
     *
     * @param array $options
     * @return array|Zend_Paginator
     */
    public function findAll($options=null)
    {
        $page = isset($options['page']) && $options['page'] &&
                is_numeric($options['page']) ? $options['page'] : null;
        $range = isset($options['range']) && $options['range'] &&
                is_numeric($options['range']) ? $options['range'] : null;
        $limit = isset($options['limit']) && $options['limit'] &&
                is_numeric($options['limit']) ? $options['limit'] : null;
        $where = isset($options['where']) && $options['where']
                ? $options['where'] : null;
        $order = isset($options['order']) && $options['order']
                ? $options['order'] : null;

        if ($page || $range) {
            $select = $this->select();
            $select->from($this->_name);

            if ($where) {
                if (is_array($where)) {
                    foreach($where as $cond => $value) {
                        if (is_numeric($cond)) {
                            $select->where($value);
                        } else {
                            $select->where($cond, $value);
                        }
                    }
                } else {
                    $select->where($where);
                }
            }

            if ($order) {
                $select->order($order);
            }

            if ($limit == 1) {
                $select->limit(1);
                $limit = null;
            }
            return $this->_getPaginator($select, $page, $limit, $range);
        }
        
        return $this->fetchAll($where, $order, $limit);
    }

    public function findOne($options)
    {
        $options['limit'] = 1;
        $results = $this->findAll($options);
        return $results->count() > 0 ? $results->current() : array();
    }

    protected function _getPaginator(Zend_Db_Select $select, $page=null,
        $limit=null, $range=null)
    {
        $adapter = new FrontZend_Paginator($select, $this->getRowClass());
        $paginator = new Zend_Paginator($adapter);

        if ($page && is_numeric($page)) {
            $paginator->setCurrentPageNumber($page);
        }

        if ($limit && is_numeric($limit)) {
            $paginator->setItemCountPerPage($limit);
        }

        if ($range && is_numeric($range)) {
            $paginator->setPageRange($range);
        }

        return $paginator;
    }

    protected function _querySelect(Zend_Db_Select $select)
    {
        $results = $this->getAdapter()->fetchAll($select);
//        pr($results,1);
        $objects = array();
        if ($results) {
            foreach ($results as $data) {
//                pr($data,1);
                $object = new $modelClass($data);
                $objects[$object->getId()] = $object;
            }
        }
        return $objects;
    }

    public function fetchPairs($field, $where=null, $order=null)
    {
        $primaryKey = $this->getPrimaryKey();
        
        if (is_array($primaryKey)) {
            throw new FrontZend_Exception('can not fetchPairs with array as $_primary');
        }
        
        $select = $this->select()
            ->from($this->_name, array($primaryKey,$field));

        if ($where) {
            if (is_array($where)) {
                foreach($where as $cond => $value) {
                    if (is_numeric($cond)) {
                        $select->where($value);
                    } else {
                        $select->where($cond, $value);
                    }
                }
            } else {
                $select->where($where);
            }
        }

        $select->order($order ? $order : $field);

        return $this->getAdapter()->fetchPairs($select);
    }

    public function count($where=null)
    {
        $select = $this->select()->from($this->_name, 
            array('count' => 'COUNT(*)'));
        
        if ($where) {
            foreach((array) $where as $cond => $bind) {
                if (is_numeric($cond)) {
                    $select->where($bind);
                } else if ($bind) {
                    $select->where($cond, $bind);
                } else {
                    return 0;
                }
            }
        }
        return $this->getAdapter()->fetchOne($select);
    }

}