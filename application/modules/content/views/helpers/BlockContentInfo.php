<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_View_Helper_BlockContentInfo extends Zend_View_Helper_Abstract
{
    public function blockContentInfo(Layout_Model_Block $block,
        Content_Model_Content $content, $user = null)
    {
        $maindata = $block->getOption('maindata');
        $metadataFields = $block->getOption('metadata');
        $relationshipFields = $block->getOption('relationships');

        // TODO se não selecionar nenhuma maindata, não exibe nada => consertar

        $metadata = array();
        if ($maindata) {
            $contentMetafields = $content->getMetafields();
            foreach($contentMetafields as $contentMetafield) {
                $metafield = $contentMetafield->getMetafield();
                if (in_array($metafield->fieldname, $metadataFields)) {
                    $metadata[$metafield->fieldname] = $contentMetafield;
                }
            }
        }

        $relationships = array();
        if ($relationshipFields) {
            $contentRelationships = $content->getRelationships();
            $relationships = array();
            foreach($contentRelationships as $contentRelationship) {
                if (in_array($contentRelationship->rel_type, $relationshipFields)) {
                    $relationships[$contentRelationship->rel_type][] =
                        $contentRelationship;
                }
            }
        }

        $xhtml = $this->view->partial('blocks/content-info.phtml', array(
            'block'         => $block,
            'content'       => $content,
            'maindata'      => $maindata,
            'metadata'      => $metadata,
            'relationships' => $relationships,
        ));

        return $xhtml;
    }

}
