<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_ContentFile extends FrontZend_Module_Model_Abstract
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
     * @return Media_Model_File
     */
    public function getFile()
    {
        $idFile = $this->id_file;
        if ($idFile) {
            return FrontZend_Container::get('File')->findById($idFile);
        }
    }

    /**
     *
     * @return Content_Model_Metafield
     */
    public function getMetafield()
    {
        $description = $this->description;
        if ($description) {
            $content = $this->getContent();
            if ($content) {
                $tbMetafield = new Content_Model_DbTable_Metafield();
                return $tbMetafield->findOne(array(
                    'where' => array(
                        'datatype = ?'  => 'file',
                        'fieldname = ?' => $description,
                        'id_content_type = ?' => $content->id_content_type
                    )
                ));
            }
        }
    }

    static public function getInputFilter()
    {
        return array(            
            'id_content_file' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_file' => array(
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

            'description' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                )
            ),

            'legend' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                )
            ),

        );
    }
    
}