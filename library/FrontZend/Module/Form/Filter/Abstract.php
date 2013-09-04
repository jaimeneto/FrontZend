<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Form_Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class FrontZend_Module_Form_Filter_Abstract
    extends Twitter_Bootstrap_Form_Vertical
{
    protected $_modelClass;
    protected $_cacheAlias;

    public function __construct($options = null)
    {
        if (!$this->_modelClass) {
            $this->_modelClass = str_replace('_Form_Filter_', '_Model_',
                get_class($this));
        }

        $this->setAttrib('id', strtolower(__CLASS__));
        $this->initElements();
        parent::__construct($options);
        $this->initButtons();

        $this->setMethod(self::METHOD_GET);
    }

    abstract public function initElements();
    
    abstract protected function _initFilters(array $filters);

    public function initButtons()
    {
        $this->addElement('submit', 'submit', array(
            'label'       => 'Filtrar',
            'class'       => 'btn-large',
            'ignore'      => true,
            'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY
        ));
    }

    public function search($page=null, $limit=null, $range=null)
    {
        $values = $this->getValues();

        $options = array(
            'page'  => $page,
            'limit' => $limit,
            'range' => $range
        );

        if (isset($values['filter'])) {
            $where = $this->_initFilters($values['filter']);
            if ($where) {
                $options['where'] = $where;
            }

            if (isset($values['filter']['order']) &&
                    $values['filter']['order']) {
                $options['order'] = $values['filter']['order'];
            }
        }

        $cacheAlias = $this->_cacheAlias
            ? $this->_cacheAlias
            : str_replace('_Model_', '', $this->_modelClass);
        
        return FrontZend_Container::get($cacheAlias)->findAll($options);
    }

}