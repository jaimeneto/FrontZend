<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/Abstract.php';

class Content_View_Helper_BlockContentContents extends Zend_View_Helper_Abstract
{
    public function blockContentContents(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        /* OPTIONS:
         * title, types, limit, form_filter,
         * relationship, search_result, template
         */

        // Get values sent by POST to define order and filters
        $request = Zend_Controller_Front::getInstance()->getRequest()->getParams();

        $order = isset($request['order']) && $request['order']
            ? $request['order']
            : $block->getOption('order', 'dt_published desc');

        $filters = isset($request['filters']) && $request['filters']
            ? $request['filters']
            : array();

        // Define content types to filter
        $types = $block->getOption('types');
        
        if (!$types) {
            return;
        }
        
        foreach($types as &$type) {
            // Add quotes to content type name
            $type = Zend_Db_Table::getDefaultAdapter()->quote($type);
        }

        // Define options to filter the results by content type
        $options = array(
            'where' => array(
                'id_content_type IN (' . implode(',', $types) . ')'
            ),
            'order' => $order
        );

        // Se estiver ativa a opção para exibir conteúdo relacionado
        $relationship = $block->getOption('relationship');
        if ($relationship) {
            $relationshipsIds = array();
            $relationships = $content->getRelationships();
            if ($relationships) {
                foreach($relationships as $rel) {
                    if ($rel->rel_type == $relationship) {
                        $relationshipsIds[] = $rel->getRelatedContent($content->id)->id;
                    }
                }
                $options['where'][] = $relationshipsIds
                    ? 'id_content in(' . implode(',', $relationshipsIds) . ')'
                    : '1=0';
            }
        }

        // Define how many items to load
        $limit = $block->getOption('limit');
        if ($limit) {
            $options['limit'] = $limit;
            $options['page'] = $this->view->pageNumber;
        }

        $options['where']['status = ?'] = Content_Model_Content::STATUS_ACTIVE;
        $options['where']['dt_published <= ?'] = Zend_Date::now()->get('yyyy-MM-dd HH:mm:ss');

        // Find the contents
        $contents = FrontZend_Container::get('Content')->findAll($options);

        // Verify if block is configured to show a form filter
        $formFilter = '';
        if ($block->getOption('form_filter')) {
            // create the form for filtering
            $formFilter = new Content_Form_FilterBlockContents(array(
                'types' => $block->getOption('types')
            ));
            $formFilter->setAction($this->view->url() . '#content_list');
            $formFilter->populate($request);
        }

        // Define vars to be used on template
        $vars = array(
            'block'      => $block,
            'content'    => $content,
            'user'       => $user,
            'contents'   => $contents,
            'formFilter' => $formFilter,
        );

        try {
            $template = $block->getOption('template');
            $script = "blocks/content-contents/{$template}";
            $xhtml = $this->view->partial($script, $vars);
        } catch(Zend_View_Exception $e) {
            // If defined template is not found, calls the default one
            $script = 'blocks/content-contents/default.phtml';
            $xhtml = $this->view->partial($script, $vars);
        }

        return $xhtml;
    }
}