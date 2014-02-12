<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_Page extends FrontZend_Module_Model_Abstract
{
    /**
     *
     * @return Layout_Model_Theme
     */
    public function getTheme()
    {
        $idLayoutTheme = $this->id_layout_theme;
        if ($idLayoutTheme) {
            return FrontZend_Container::get('LayoutTheme')->findById($idLayoutTheme);
        }
    }

    /**
     *
     * @return Content_Model_ContentType
     */
    public function getContentType()
    {
        $idContentType = $this->id_content_type;
        if ($idContentType) {
            return FrontZend_Container::get('ContentType')->findById($idContentType);
        }
    }

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
     * Retorna a lista de blocos desta página que não são envolvidos por
     * outra área (rows)
     *
     * @param boolean $justVisible [default=false]
     * @return array
     */
    public function getBlocks($justVisible=false)
    {
        $where = array(
            'id_layout_page = ?' => $this->getId(),
            'id_wrapper IS NULL'
        );
        if ($justVisible) {
            $where['visible = ?'] = 1;
        }
        $blocks = FrontZend_Container::get('LayoutBlock')->findAll(array(
            'where' => $where,
            'order' => 'order'
        ));

        return $blocks;
    }

    public function getInputFilter()
    {
        return array(            
            'id_layout_page' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'page' => array(
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
            
            'id_content' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content',
//                            'field' => 'id_content',
//                        ),
//                    ),   
                ),
            ),

            'id_content_type' => array(
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

            'special' => array(
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

        );
    }
    
}