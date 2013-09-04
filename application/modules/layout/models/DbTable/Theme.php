<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_DbTable_Theme extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'layout_theme';
    protected $_primary      = 'id_layout_theme';
    protected $_modelClass   = 'Layout_Model_Theme';
    protected $_enablePrefix = true;

    protected $_dependentTables = array(
        'Layout_Model_DbTable_Page',
    );

    public function findActive($env='frontend')
    {
        return FrontZend_Container::get('LayoutTheme')->findOne(array('where' => array(
            'active = ?' => '1',
            'env = ?'    => $env
        )));
    }

    public function activate($theme)
    {
        if (!($theme instanceof $this->_modelClass)) {
            $theme = FrontZend_Container::get('LayoutTheme')->findById($theme);
        }

        $activeTheme = $this->findActive($theme->env);

        $activeTheme->active = 0;
        if (FrontZend_Container::get('LayoutTheme')->save($activeTheme)) {
            $theme->active = 1;
            return FrontZend_Container::get('LayoutTheme')->save($theme);
        }
    }

}

