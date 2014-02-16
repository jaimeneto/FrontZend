<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_DbTable_Block extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'layout_block';
    protected $_primary      = 'id_layout_block';
    protected $_modelClass   = 'Layout_Model_Block';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Page' => array(
            'columns'       => 'id_layout_page',
            'refTableClass' => 'Layout_Model_DbTable_Page',
            'refColumns'    => 'id_layout_page'
        ),
        'Wrapper' => array(
            'columns'       => 'id_wrapper',
            'refTableClass' => 'Layout_Model_DbTable_Block',
            'refColumns'    => 'id_layout_block'
        ),
        'Parent' => array(
            'columns'       => 'id_parent',
            'refTableClass' => 'Layout_Model_DbTable_Block',
            'refColumns'    => 'id_layout_block'
        ),
    );

    /**
     * Delete block areas that has no block columns
     * 
     * @return int Number of deleted areas
     */
    public function deleteEmptyAreas() 
    {
        $select2 = $this->select()
                ->from(array('t2' => $this->getTableName()), 
                        array('COUNT(*)'))
                ->where('id_wrapper = t1.id_layout_block');
        
        $select = $this->select()
                ->from(array('t1' => $this->getTableName()), 
                        array('id_layout_block'))
                ->where('block = ?', 'layout-area')
                ->where("($select2) = 0");
        
        $areas = $this->getAdapter()->fetchCol($select);
        
        $deleted = 0;
        foreach($areas as $areaId) {
            if (FrontZend_Container::get('LayoutBlock')->deleteById($areaId)) {
                $deleted++;
            };
        }
        return $deleted;
    }
    
}