<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_Block extends FrontZend_Module_Model_Abstract
{
    /**
     * Recebe um array de opções do bloco e converte para json.
     * Se o block herda de outro, salva apenas os valores direntes do bloco pai
     *
     * @param array $options
     * @return Layout_Model_Block
     */
    public function setOptions(array $options)
    {
        if ($this->id_parent) {
            $parentOptions = $this->getParent()->getOptions();
            $visibility = $options['visibility'] != $parentOptions['visibility']
                    ? $options['visibility']
                    : null;
            unset($parentOptions['visibility'], $options['visibility']);
            $options = array_diff($options, $parentOptions);
            if ($visibility) {
                $options['visibility'] = $visibility;
            }
        }
        $this->options = Zend_Json::encode($options);
        return $this;
    }

    /**
     * Retorna as opções do bloco convertidas em array.
     * Se o bloco herdad de outro, retorna também as opções do bloco pai.
     *
     * @return array
     */
    public function getOptions()
    {
        $options = (array) Zend_Json::decode($this->options);
        if ($this->id_parent) {
            $parentOptions = $this->getParent()->getOptions();
            $options = array_merge($parentOptions, array_filter($options));
        }
        return $options;
    }

    public function hasOption($option)
    {
        $options = $this->getOptions();
        return isset($options[$option]);
    }

    public function setOption($option, $value)
    {
        $options = $this->getOptions();
        $options[$option] = $value;
        return $this->setOptions($options);
    }
    
    /**
     * Retorna o valor de uma opção
     *
     * @param string $option
     * @param mixed $default
     * @return mixed
     */
    public function getOption($option, $default=null)
    {
        $options = $this->getOptions();
        return isset($options[$option]) ? $options[$option] : $default;
    }

    public function getAttribs()
    {
        $options = $this->getOptions();
        $attribs = array();
        
        if ($options['css_id']) {
            $attribs[] = 'id="' . $options['css_id'] . '"';
        }
        
        $class = array(
            'block' ,
            strtolower(str_replace('_', '-', get_class($this)))
        );
        
        if ($options['css_class']) {
            $class[] = $options['css_class'];
        }
        
        if ($options['visibility']) {
            switch (count($visibility)) {
                case 1:
                case 2:
                    foreach($visibility as $screenSize) {
                        $class[] = 'visible-' . $screenSize;
                    }
                    break;
                case 3:
                    $screenSizes = array('xs', 'sm', 'md', 'lg');
                    foreach($screenSizes as $screenSize) {
                        if (!in_array($screenSize, $visibility)) {
                            $class[] = 'hidden-' . $screenSize;
                        }
                    }
                    break;
            }
        }
        
        $attribs[] = 'class="' . implode(' ', $class) . '"';
        
        return implode(' ', $attribs);
    }
    
    /**
     *
     * @return Layout_Model_Page
     */
    public function getPage()
    {
        $idPage = $this->id_layout_page;
        if ($idPage) {
            return FrontZend_Container::get('LayoutPage')->findById($idPage);
        }
    }

    /**
     *
     * @return Layout_Model_Block
     */
    public function getWrapper()
    {
        $idWrapper = $this->id_wrapper;
        if ($idWrapper) {
            return FrontZend_Container::get('LayoutBlock')->findById($idWrapper);
        }
    }

    /**
     *
     * @return Layout_Model_Block
     */
    public function getParent()
    {
        $idParent = $this->id_parent;
        if ($idParent) {
            return FrontZend_Container::get('LayoutBlock')->findById($idParent);
        }
    }

    /**
     *
     * @param boolean $justVisible [default=false]
     * @return array
     */
    public function getBlocks($justVisible=false)
    {
        $where = array(
            'id_wrapper = ?' => $this->getId()
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

    public function isVisible()
    {
        return (boolean) $this->visible;
    }

    /**
     * Toggle block visibility
     *
     * @return Layout_Model_Block
     */
    public function toggle()
    {
        $this->visible = $this->isVisible() ? 0 : 1;
        return $this;
    }

    public function getInputFilter()
    {
        return array(            
            'id_layout_block' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'id_layout_page' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'layout_page',
//                            'field' => 'id_layout_page',
//                        ),
//                    ),
                ),
            ),

            'id_layout_wrapper' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'layout_block',
//                            'field' => 'id_layout_block',
//                        ),
//                    ),
                ),
            ),

            'id_parent' => array(
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'layout_block',
//                            'field' => 'id_layout_block',
//                        ),
//                    ),
                ),
            ),

            'block' => array(
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
                            'max'      => 50,
                        ),
                    ),
                ),
            ),

            'order' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'visible' => array(
                'required' => true,
                'validators' => array(
//                    array(
//                        'name'    => 'InArray',
//                        'options' => array(
//                            'haystack' => array(0, 1),
//                        ),
//                    ),
                ),
            ),

            'options' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim'
                ),
            ),

        );
    }
    
}