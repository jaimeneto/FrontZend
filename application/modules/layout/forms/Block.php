<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class Layout_Form_Block extends Twitter_Bootstrap_Form_Horizontal
{
    /**
     * @var Layout_Model_Block
     */
    protected $_block;

    /**
     *
     * @param mixed $block
     * @return Layout_Form_Block;
     */
    static public function factory($block)
    {
        if ($block instanceof Layout_Model_Block) {
            $blockName = $block->block;
        } else {
            $blockName = $block;
            $block = null;
        }

        if (!strstr($blockName, '-')) {
            return;
        }

        list($module, $name) = explode('-',$blockName);
        $className = ucfirst($module) . '_Form_Block_' . ucfirst($name);
        return new $className($block);
    }

    public function __construct(Layout_Model_Block $block = null, $options = null)
    {
        if ($block) {
            $this->setBlock($block);
        }
        
        parent::__construct($options);

        if ($this->_block) {
            $this->populate(array_merge(
                array('id_layout_block' => $this->_block->getId()),
                $this->_block->toArray(),
                $this->_block->getOptions()
            ));
        }
    }

    /**
     *
     * @param Layout_Model_Block $block
     * @return Layout_Form_AbstractBlock
     */
    public function setBlock(Layout_Model_Block $block)
    {
        $this->_block = $block;
        return $this;
    }

    /**
     *
     * @return Layout_Model_Block
     */
    public function getBlock()
    {
        return $this->_block;
    }

    public function init()
    {
        // id_layout_block
        $this->addElement('hidden', 'id_layout_block', array(
            'decorators' => array('ViewHelper')
        ));

        // id_parent
        $where = array(
            'block = ?' => strtolower(str_replace('_Form_Block_', '-', get_class($this))),
            'id_parent IS NULL'
        );
        if ($this->_block) {
            $where['id_layout_block != ?'] = $this->_block->id;
        }

        $blocks = FrontZend_Container::get('LayoutBlock')->findAll(array('where' => $where));
        $multiOptions = array('' => '');
        if ($blocks) {
            foreach($blocks as $block) {
                $multiOptions[$block->getPage()->page][$block->id] =
                    ($block->hasOption('title') && $block->getOption('title')
                        ? $block->getOption('title') : $block->block);
            }
        }

        $this->addElement('select', 'id_parent', array(
            'label'        => 'Herdar opções',
            'class'        => 'input-block-level',
            'multiOptions' => $multiOptions
        ));

        $this->_initElements();

        $this->addElement('multiCheckbox', 'visibility', array(
            'label'        => 'Visibilidade',
            'label_class'  => 'inline span1',
            'multiOptions' => array(
                'desktop' => 'Desktop',
                'tablet'  => 'Tablet',
                'phone'   => 'Celular'
            ),
            'value'        => array('desktop', 'tablet', 'phone')
        ));

        $elements = array_keys($this->getElements());

        $this->addDisplayGroup(
            $elements, 'form_elements', array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array(
                    'FormElements',
                    array('HtmlTag', array('tag' => 'div', 'class' => 'modal-body'))
                )
            )
        );

        $this->_initButtons();
    }

    abstract protected function _initElements();

    public function _initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'        => 'Salvar',
            'ignore'       => true,
            'buttonType'   => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'decorators'   => array('ViewHelper')
        ));

        $this->addElement('button', 'cancel', array(
            'label'        => 'Cancelar',
            'data-dismiss' => 'modal',
            'aria-hidden'  => 'true',
            'ignore'       => true,
            'decorators'   => array('ViewHelper')
        ));

        $this->addFormActions(array('save', 'cancel'));
    }

    public function persistData()
    {
        $values = $this->getValues();

        if ($values) {
            $this->_block->id_layout_block = $values['id_layout_block'];

            if ($values['id_parent']) {
                $this->_block->id_parent = $values['id_parent'];
            }
            unset($values['id_layout_block'], $values['id_parent']);

            $this->_block->setOptions($values);

            return FrontZend_Container::get('LayoutBlock')->save($this->_block);
        }
    }

}
