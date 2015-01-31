<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Module
 * @subpackage Form_Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

require_once 'Bootstrap/Form/Vertical.php';

abstract class FrontZend_Module_Form_Filter_Abstract
    extends Bootstrap_Form_Horizontal
{
    protected $colLabel = 4;
    protected $colInput = 8;
    protected $colWrapper = 4;
    
    protected $_modelClass;
    protected $_cacheAlias;

    public function __construct($options = null)
    {
        if (!$this->_modelClass) {
            $this->_modelClass = str_replace('_Form_Filter_', '_Model_',
                get_class($this));
        }

        $this->setAttrib('id', strtolower(__CLASS__));
        
        parent::__construct($options);
        
        $this->initElements();
        $this->initButtons();
    }

    abstract public function initElements();
    
    abstract protected function _initFilters(array $filters);

    public function initButtons()
    {
        $this->addElement('submit', 'submit', array(
            'label'       => 'Filtrar',
            'ignore'      => true,
            'buttonType'  => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'icon'        => 'glyphicon glyphicon-search'
        ));
        
        $this->addDisplayGroup(array('submit'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'class' => 'col-sm-4', 
                    'tag'   => 'div'
                ))
            ),
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