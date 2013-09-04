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

}