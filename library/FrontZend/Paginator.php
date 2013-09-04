<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Paginator
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Description of Paginator
 *
 * @author Jaime Neto
 */
class FrontZend_Paginator extends Zend_Paginator_Adapter_DbSelect
{
    /**
     *
     * @var String
     */
    protected $_modelClass;

    public function __construct(Zend_Db_Select $select, $className)
    {
        if (!class_exists($className)) {
            throw new FrontZend_Exception("Class '{$className}' not exists");
        } else if(!is_subclass_of($className, 'FrontZend_Module_Model_Abstract')) {
            throw new FrontZend_Exception("Class '{$className}' is not subclass"
                                      . ' of FrontZend_Module_Model_Abstract');
        }
        $this->_modelClass = $className;

        parent::__construct($select);
    }

    public function getModelClass()
    {
        return $this->_modelClass;
    }

    public function getItems($offset, $itemCountPerPage)
    {
        $results = parent::getItems($offset, $itemCountPerPage);

        $items = array();
        if ($results) {
            $modelClass = $this->getModelClass();
            foreach ($results as $data) {
                $item = new $modelClass(array('data' => $data));
                $items[$item->getId()] = $item;
            }
        }
        
        return $items;
    }

}