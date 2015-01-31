<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_DbTable_Nav extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'layout_nav';
    protected $_primary      = 'id_layout_nav';
    protected $_modelClass   = 'Layout_Model_Nav';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Parent' => array(
            'columns'       => 'id_parent',
            'refTableClass' => 'Content_Model_DbTable_Nav',
            'refColumns'    => 'id_layout_nav'
        ),
    );

    public function save($data)
    {
        if ($data instanceof $this->_modelClass) {
            $data = $data->toArray();
        }

        if (is_array($data)) {
            $orderField = $this->getAdapter()->quoteIdentifier('order');

            // update
            if (isset($data[$this->_primary]) && $data[$this->_primary]) {
                $row = $this->findById($data[$this->_primary]);

                if (isset($data['order'])) {
                    if ($row->order != $data['order'] || 
                            $row->id_parent != $data['id_parent']) {
                        
                        // change order, but keep parent
                        if ($row->id_parent == $data['id_parent']) {
                            $where = array();
                            if (isset($data['id_parent']) && $data['id_parent'])
                            {
                                $where['id_parent = ?'] = $data['id_parent'];
                            } else {
                                $where[] = 'id_parent IS NULL';
                            }
                            if ($row->order > $data['order']) {
                                $where["{$orderField} >= ?"] = $data['order'];
                                $where["{$orderField} < ?"] = $row->order;
                                $moveUp = $this->fetchAll($where);
                            } else if ($row->order < $data['order']) {
                                $where["{$orderField} > ?"] = $row->order;
                                $where["{$orderField} <= ?"] = $data['order'];
                                $moveDown = $this->fetchAll($where);
                            }

                        // change parent
                        } else {
                            $where = array(
                                "{$orderField} >= ?" => $data['order']
                            );
                            if (isset($data['id_parent']) &&
                                    $data['id_parent']) {
                                $where['id_parent = ?'] = $data['id_parent'];
                            } else {
                                $where[] = 'id_parent IS NULL';
                            }
                            $moveUp = $this->fetchAll($where);


                            $where = array(
                                "{$orderField} > ?" => $row->order
                            );
                            if (isset($data['id_parent']) &&
                                    $data['id_parent']) {
                                $where['id_parent = ?'] = $row->id_parent;
                            } else {
                                $where[] = 'id_parent IS NULL';
                            }
                            $moveDown = $this->fetchAll($where);

                        }
                    }
                }

            // insert
            } else {
                $row = $this->createRow();

                if (isset($data['order'])) {
                    $where = array("{$orderField} >= ?" => $data['order']);
                    if (isset($data['id_parent']) && $data['id_parent']) {
                        $where['id_parent = ?'] = $data['id_parent'];
                    } else {
                        $where[] = 'id_parent IS NULL';
                    }
                    $moveUp = $this->fetchAll($where);
                }
            }

            $row->setFromArray($data);
        }

        $id = $row->save();

        if (isset($moveUp) && $moveUp) {
            foreach($moveUp as $nav) {
                $nav->order = $nav->order+1;
                $nav->save();
            }
        }

        if (isset($moveDown) && $moveDown) {
            foreach($moveDown as $nav) {
                $nav->order = $nav->order-1;
                $nav->save();
            }
        }

        return $id;
    }

    public function delete($where)
    {
        $navs = $this->fetchAll($where);
        if ($navs) {
            $moveDown = array();
            $orderField = $this->getAdapter()->quoteIdentifier('order');
            foreach($navs as $nav) {
                $moveDown = $this->fetchAll(array(
                    'id_parent = ?'     => $nav->id_parent,
                    "{$orderField} > ?" => $nav->order
                ));
            }
        }

        $result = parent::delete($where);

        if (isset($moveDown) && $moveDown) {
            foreach($moveDown as $nav) {
                $nav->order = $nav->order-1;
                $nav->save();
            }
        }

        return $result;
    }

    public function getNavTree($navPages, $parent=null)
    {
        $navConfigs = array();
        foreach($navPages as $navPage) {
            if ($navPage->id_parent == $parent) {
                $idNav = "layout_nav_{$navPage->id}";
                $navConfigs[$idNav] = (object) $navPage->toArray();
                $navConfigs[$idNav]->id = $navPage->id;
                if ($navPage->attribs) {
                    $navPageAttribs = Zend_Json::decode($navPage->attribs);
                    foreach($navPageAttribs as $attr => $val) {
                        $navConfigs[$idNav]->$attr = $val;
                    }
                    unset($navConfigs[$idNav]->attribs);
                }
                $pages = $this->getNavTree(clone $navPages, $navPage->id);
                if ($pages) {
                    $navConfigs[$idNav]->pages = $pages;
                }
            }
        }

        return $navConfigs;
    }
    
    private function getContainerPages($navPages, $parent=null)
    {
        $navConfigs = array();
        foreach($navPages as $navPage) {
            if ($navPage->id_parent == $parent) {
                $idNav = "layout_nav_{$navPage->id}";
                $navConfigs[$idNav] = $navPage->toArray();
                $navConfigs[$idNav]['id'] = $idNav;
                unset($navConfigs[$idNav]['id_layout_nav']);
                if ($navPage->attribs) {
                    $navPageAttribs = Zend_Json::decode($navPage->attribs);
                    foreach($navPageAttribs as $attr => $val) {
                        $navConfigs[$idNav][$attr] = $val;
                        unset($navConfigs[$idNav]['attribs']);
                    }
                }
                $pages = $this->getContainerPages(clone $navPages, $navPage->id);
                if ($pages) {
                    $navConfigs[$idNav]['pages'] = $pages;
                }
            }
        }

        return $navConfigs;
    }
    
    public function generateContainer()
    {
        $navPages = $this->fetchAll(null, array('id_parent', 'order ASC'));
        $containerPages = $this->getContainerPages($navPages);
        
        $container = new Zend_Navigation($containerPages);
        
        return $container;
    }
    
}
