<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

abstract class Layout_Form_Block extends Bootstrap_Form_Horizontal
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
        if (!$block->module) {
            $class = get_class($this);
            $block->module = strtolower(substr($class, 0, strpos($class, '_')));
        }
        
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

        $this->_addParentId();

        $this->_initElements();
        
        $this->_addCssClass();
        $this->_addCssId();
        $this->_addElementTemplate();
        $this->_addElementVisibility();
        
        $this->_addDisplayGroup();

        $this->_initButtons();
    }

    protected function _addDisplayGroup()
    {
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
    }
    
    protected function _addParentId()
    {
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
            'multiOptions' => $multiOptions
        ));
    }
    
    protected function _addCssClass()
    {
        // css_class
        $this->addElement('text', 'css_class', array(
            'label'      => 'Classe CSS',
            'maxlength'  => 60,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 60,
                    ),
                ),
            )
        ));
    }
    
    protected function _addCssId()
    {
        // css_id
        $this->addElement('text', 'css_id', array(
            'label'      => 'Id CSS',
            'maxlength'  => 15,
            'filters'    => array(
                'StripTags',
                'StringTrim'
            ),
            'validators' => array(
                array(
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        'min'      => 0,
                        'max'      => 15,
                    ),
                ),
            )
        ));
    }
    
    protected function _addElementTemplate($blockName=null)
    {
        if (!$blockName) {
            $blockName = $this->_block->block;
        }
        
        $multiOptions = array(
            '' => '',
        );
        $theme = Acl_Model_Auth::getTheme();
        $dirCustomTemplates = APPLICATION_PATH . '/layouts/frontend/' . $theme
            . '/scripts/blocks/' . $blockName;
        if (is_dir($dirCustomTemplates)) {
            $dir = new DirectoryIterator($dirCustomTemplates);
            foreach($dir as $file) {
                if($file->isFile()) {
                    $multiOptions[$theme][$file->getFilename()] 
                            = $file->getFilename();
                }
            }
        }

        $dirStandardTemplates = APPLICATION_PATH . '/modules/' 
            . $this->_block->module . '/views/blocks/' . $blockName;
        if (is_dir($dirStandardTemplates)) {
            $dir = new DirectoryIterator($dirStandardTemplates);
            foreach($dir as $file) {
                if($file->isFile() 
                    && !isset($multiOptions[$theme][$file->getFilename()])) {
                        $multiOptions['standard'][$file->getFilename()] 
                            = $file->getFilename();
                }
            }
        } else {
            $templateFile = $blockName . '.phtml';
            if (file_exists(APPLICATION_PATH . '/modules/' 
                    . $this->_block->module . '/views/blocks/' 
                    . $templateFile)) {
                $multiOptions['standard'][$templateFile] = $templateFile;
            }
        }
        
        if (isset($multiOptions[$theme]) || isset($multiOptions['standard'])) {
            $options = array(
                'label'        => 'Modelo',
                'multiOptions' => $multiOptions
            );
            if (!isset($multiOptions[$theme]) 
                    && count($multiOptions['standard']) == 1) {
                $options['value'] = key($multiOptions['standard']);
            }
            $this->addElement('select', 'template', $options);
        }
    }
    
    protected function _addElementVisibility()
    {
        $this->addElement('multiCheckbox', 'visibility', array(
            'label'        => 'Visibilidade',
            'inline'       => true,
            'multiOptions' => array(
                'xs' => 'Celular '
                      . '<a href="#" rel="tooltip" ' 
                      . 'title="&lt; 768px" data-placement="top">'
                      . '<span class="glyphicon glyphicon-info-sign"></span></a>',
                'sm' => 'Tablet '
                      . '<a href="#" rel="tooltip" ' 
                      . 'title="&GreaterEqual; 768px" data-placement="top">'
                      . '<span class="glyphicon glyphicon-info-sign"></span></a>',
                'md' => 'Desktop '
                      . '<a href="#" rel="tooltip" ' 
                      . 'title="&GreaterEqual; 992px" data-placement="top">'
                      . '<span class="glyphicon glyphicon-info-sign"></span></a>',
                'lg' => 'Desktop grande '
                      . '<a href="#" rel="tooltip" ' 
                      . 'title="&GreaterEqual; 1200px" data-placement="top">'
                      . '<span class="glyphicon glyphicon-info-sign"></span></a>',
            ),
            'value'        => array('xs', 'sm', 'md', 'lg'),
            'escape'       => false
        ));
    }
    
    abstract protected function _initElements();

    public function _initButtons()
    {
        $this->addElement('submit', 'save', array(
            'label'        => 'Salvar',
            'ignore'       => true,
            'buttonType'   => Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'size'         => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'decorators'   => array('ViewHelper')
        ));

        $this->addElement('button', 'cancel', array(
            'label'        => 'Cancelar',
            'data-dismiss' => 'modal',
            'aria-hidden'  => 'true',
            'buttonType'   => Bootstrap_Form_Element_Submit::BUTTON_DEFAULT,
            'size'         => Bootstrap_Form_Element_Submit::BUTTON_SIZE_LARGE,
            'ignore'       => true,
            'decorators'   => array('ViewHelper')
        ));

        $this->addDisplayGroup(array('save', 'cancel'), 'buttons', array(
            'decorators' => array(
                'FormElements', 
                array('HtmlTag', array(
                    'class' => 'modal-footer', 
                    'tag'   => 'div',
                ))
            ),
        ));
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
