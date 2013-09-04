<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage Container
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_Container extends FrontZend_Module_Container
{
    public function newLayoutTheme()
    {
        return new Layout_Model_DbTable_Theme();
    }

    public function newLayoutPage()
    {
        return new Layout_Model_DbTable_Page();
    }

    public function newLayoutBlock()
    {
        return new Layout_Model_DbTable_Block();
    }

    public function newLayoutNav()
    {
        return new Layout_Model_DbTable_Nav();
    }

}