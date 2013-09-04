<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage Container
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Model_Container extends FrontZend_Module_Container
{
    public function newFile()
    {
        return new Media_Model_DbTable_File();
    }

}