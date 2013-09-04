<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_ContentMetafield extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Content_Model_Content
     */
    public function getContent()
    {
        $idContent = $this->id_content;
        if ($idContent) {
            return FrontZend_Container::get('Content')->findById($idContent);
        }
    }

    /**
     *
     * @return Content_Model_Metafield
     */
    public function getMetafield()
    {
        $idMetafield = $this->id_metafield;
        if ($idMetafield) {
            return FrontZend_Container::get('Metafield')->findById($idMetafield);
        }
    }

    static public function getInputFilter()
    {
        return array(            
            'id_content_metafield' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_metafield' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'metafield',
//                            'field' => 'id_metafield',
//                        ),
//                    ),
//                ),
            ),

            'id_content' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content',
//                            'field' => 'id_content',
//                        ),
//                    ),
//                ),
            ),

            'value' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                )
            ),
            
        );
    }
    
}