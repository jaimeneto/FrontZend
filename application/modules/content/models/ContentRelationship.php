<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_ContentRelationship extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Content_Model_Content
     */
    public function getContentA()
    {
        $idContentA = $this->id_content_a;
        if ($idContentA) {
            return FrontZend_Container::get('Content')->findById($idContentA);
        }
    }

    /**
     *
     * @return Content_Model_Content
     */
    public function getContentB()
    {
        $idContentB = $this->id_content_b;
        if ($idContentB) {
            return FrontZend_Container::get('Content')->findById($idContentB);
        }
    }

    /**
     *
     * @param integer $idContent 
     */
    public function getRelatedContent($idContent)
    {
        $idContentA = $this->id_content_a;
        return $idContent == $idContentA
            ? $this->getContentB()
            : $this->getContentA();
    }

    public function getInputFilter()
    {
        return array(            
            'id_content_relationship' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_content_a' => array(
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

            'id_content_b' => array(
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

            'rel_type' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 20,
                        ),
                    ),
                ),
            ),
            
        );
    }
    
}