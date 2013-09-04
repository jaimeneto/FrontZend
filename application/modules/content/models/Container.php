<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage Container
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_Container extends FrontZend_Module_Container
{
    public function newContent()
    {
        return new Content_Model_DbTable_Content();
    }

    public function newContentType()
    {
        return new Content_Model_DbTable_ContentType();
    }

    public function newComment()
    {
        return new Content_Model_DbTable_Comment();
    }

    public function newContentMetafield()
    {
        return new Content_Model_DbTable_ContentMetafield();
    }

    public function newContentFile()
    {
        return new Content_Model_DbTable_ContentFile();
    }

    public function newContentRelationship()
    {
        return new Content_Model_DbTable_ContentRelationship();
    }

    public function newContentUser()
    {
        return new Content_Model_DbTable_ContentUser();
    }

    public function newMetafield()
    {
        return new Content_Model_DbTable_Metafield();
    }

}