<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_Nav extends FrontZend_Module_Model_Abstract
{

    /**
     *
     * @return Content_Model_Nav
     */
    public function getParent()
    {
        $idParent = $this->id_parent;
        if ($idParent) {
            return FrontZend_Container::get('LayoutNav')->findById($idParent);
        }
    }

    /**
     * Retorna a lista de blocos desta página que não são envolvidos por
     * outra área (rows)
     *
     * @param boolean $justVisible [default=true]
     * @return Zend_Db_Table_Rowset
     */
    public function getChildren($justVisible=true)
    {
        $where = array('id_layout_nav = ?' => $this->getId());
        if ($justVisible) {
            $where['visible = ?'] = 1;
        }
        $children = FrontZend_Container::get('LayoutBlock')->findAll(array(
            'where' => $where,
            'order' => 'order'
        ));

        return $children;
    }

    public function getInputFilter()
    {
        return array(            
            'id_layout_nav' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'name' => array(
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
//                            'table' => 'layout_nav',
//                            'field' => 'id_layout_nav',
//                        ),
//                    ),   
                ),
            ),

            'uri' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 255,
                        ),
                    ),
                ),
            ),

            'visible' => array(
//                'validators' => array(
//                    array('InArray', array('haystack' => array(0, 1))),
//                ),
            ),

            'resource' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 0,
                            'max'      => 50,
                        ),
                    ),
                ),
            ),

            'attribs' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                )
            ),

        );
    }
    
}