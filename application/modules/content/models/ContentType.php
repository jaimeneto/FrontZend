<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_ContentType extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Content_Model_ContentType
     */
    public function getParent()
    {
        $idParent = $this->id_parent;
        if ($idParent) {
            return FrontZend_Container::get('ContentType')->findById($idParent);
        }
    }

    public function getChildren()
    {
        if ($this->getId()) {
            return FrontZend_Container::get('ContentType')->findAll(array(
                'where' => array(
                    'id_parent = ?' => $this->getId()
                )
            ));
        }
    }
    
    public function countContents()
    {
        if ($this->getId()) {
            $count = FrontZend_Container::get('Content')->count(array(
                'id_content_type = ?' => $this->getId()
            ));
            
            if ($count == 0) {
                $children = $this->getChildren();
                if ($children) {
                    $childrenIds = array();
                    foreach($children as $child) {
                        $childrenIds[] = "'{$child->id}'";
                    }

                    if (!$childrenIds) {
                        return 0;
                    }

                    $count = FrontZend_Container::get('Content')->count(array(
                        'id_content_type IN (' . 
                        implode(',', $childrenIds) . ')'
                    ));
                }
            }
            
            return $count;
        }
    }
    
    public function getMetafields()
    {
        $idContentType = $this->id_content_type;
        if ($idContentType) {
            $options = array(
                'where' => array('id_content_type = ?' => $idContentType),
                'order' => array('datatype', 'order')
            );
            $metafields = FrontZend_Container::get('Metafield')->findAll($options);

            $metafieldsArray = array(
                'field'        => array(),
                'relationship' => array(),
                'file'         => array()
            );

            foreach($metafields as $metafield) {
                $metafieldsArray[$metafield->datatype][$metafield->fieldname] = $metafield;
            }

            return (object) $metafieldsArray;
        }
    }

    /**
     * Returns the layout page if it is been created
     * 
     * @return Layout_Model_Page
     */
    public function getLayoutPage()
    {
        return FrontZend_Container::get('LayoutPage')->findByContentType($this->getId());
    }

    static public function getInputFilter()
    {
        return array(            
            'id_content_type' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags',
                    'StringToLower'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 2,
                            'max'      => 15,
                        ),
                    ),
                ),
            ),
            
            'type' => array(
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
                            'max'      => 30,
                        ),
                    ),
                ),
            ),
            
            'plural' => array(
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
                            'max'      => 30,
                        ),
                    ),
                ),
            ),
            
            'id_parent' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content_type',
//                            'field' => 'id_content_type',
//                        ),
//                    ),   
                ),
            ),
        );
    }
    
}