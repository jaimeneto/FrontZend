<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @subpackage Meta
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Form_Meta_Relationship_List extends Zend_Form_Element_Multi
{
    // TODO Não está funcionando o validator
    protected $_registerInArrayValidator = false;

    public function __construct($spec, $options = null)
    {
        $this->helper = isset($options['multiple']) && $options['multiple']
            ? 'formMultiCheckbox' : 'formRadio';

        if ($options['type'] == 'users') {
            $options = $this->loadUsers($options);
        } else if ($options['type'] == 'contents') {
            $options = $this->loadContents($options);
        }
        unset($options['type'],
              $options['display'],
              $options['multiple'],
              $options['related']);

        if (!isset($options['separator'])) {
            $options['separator'] = '';
        }
        if (!isset($options['label_class'])) {
            $options['label_class'] = 'inline span2';
        }

        parent::__construct($spec, $options);
    }

    protected function loadContents($options)
    {
        $tbContent = new Content_Model_DbTable_Content();
        $where = array();
        if (isset($options['related'])) {
            $where['id_content_type = ?'] = $options['related'];
        }
        $options['multiOptions'] = $tbContent->fetchPairs(
            'title', $where, 'title');

        return $options;
    }

    protected function loadUsers($options)
    {
        $tbUser = new Acl_Model_DbTable_User();
        $where = array();
        if (isset($options['related'])) {
            $related = array();
            foreach($options['related'] as $rel) {
                $related[] = "'{$rel}'";
            }
            
            $where[] = 'id_role IN (' . implode(',',$related) . ')';
        }

        $options['multiOptions'] = $tbUser->fetchPairs(
            'display_name', $where, 'display_name');

        return $options;
    }

}
