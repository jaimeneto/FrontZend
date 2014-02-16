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

class Content_View_Helper_BlockContentRelatedcontents
    extends Zend_View_Helper_Abstract
{
    public function blockContentRelatedcontents(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        /* OPTIONS:
         * title, limit, related, template, visibility
         */

        if(!$content) {
            return;
        }
        
        $related = $block->getOption('related');
        $metafield = FrontZend_Container::get('Metafield')->findById($related);
        
        if(!$metafield) {
            return;
        }
        
        $relationshipsIds = array();
        $relationships = FrontZend_Container::get('ContentRelationship')->fetchByContent(
            $content, $metafield->fieldname);

        $contents = array();
        if ($relationships->count() > 0) {
            foreach($relationships as $rel) {
                $relationshipsIds[] =
                    $rel->getRelatedContent($content->id)->id;
            }

            $options['where'][] =
                'id_content in(' . implode(',', $relationshipsIds) . ')';

            // Define how many items to load
            $limit = $block->getOption('limit');
            if ($limit) {
                $options['limit'] = $limit;
                $options['page'] = $this->view->pageNumber;
            }

            // Find the contents
            $contents = FrontZend_Container::get('Content')->findAll($options);
        }

        // Define vars to be used on template
        $vars = array(
            'block'      => $block,
            'content'    => $content,
            'user'       => $user,
            'contents'   => $contents,
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