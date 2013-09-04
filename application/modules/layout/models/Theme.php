<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_Model_Theme extends FrontZend_Module_Model_Abstract
{
    protected $_configData = array();
    
    public function loadConfigData()
    {
        if (!$this->_configData && $this->env && $this->theme) {
            $configPath = APPLICATION_PATH 
                        . "/layouts/{$this->env}/{$this->theme}/theme.ini";
            if (file_exists($configPath)) {
                $config = new Zend_Config_Ini($configPath);
                $this->_configData = $config->toArray();
            }
        }
    }
    
    public function __get($columnName)
    {
        try {
            return  parent::__get($columnName);
        } catch(Exception $e) {
            $this->loadConfigData();
            if (isset($this->_configData[$columnName])) {
                return $this->_configData[$columnName];
            }
            return $e;
        }
    }

    /**
     * Retorna a lista de páginas deste tema
     *
     * @return array
     */
    public function getPages()
    {
        return FrontZend_Container::get('LayoutBlock')->findAll(array(
            'where' => array('id_layout_theme = ?' => $this->getId()),
            'order' => array('name', 'version')
        ));
    }

    public function __toString()
    {
        $this->loadConfigData();
        return "{$this->name} v{$this->version}";
    }

    static public function getInputFilter()
    {
        return array(            
            'id_layout_theme' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'theme' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    'Alnum',
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

            'env' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    'Alnum',
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 15,
                        ),
                    ),
                ),
            ),

        );
    }
    
}