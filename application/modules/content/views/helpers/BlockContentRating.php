<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_View_Helper_BlockContentRating extends Zend_View_Helper_Abstract
{
    public function blockContentRating(Layout_Model_Block $block,
        Content_Model_Content $content = null, $user = null)
    {
        if (!$content) {
            return;
        }   
        
        $field = $block->getOption('field');
        $rating = $content->getMetafield($field);

        if (!$rating) {
            return;
        }

        $metafield = $rating->getMetafield();

        // Tests if metafield type is 'rating' and if the value is valid
        if (($metafield->getOption('type') != 'rating') ||
            ($rating->value < 0 || $rating->value > 5)) {
            return;
        }

        $contentRating = $rating->value;

        // if is not allowed half star, round down the value
        if (!$metafield->getOption('half')) {
            $contentRating = floor($contentRating) . '.0';
        }


//        $list = $modelContents->getList($options->metafield, $content->id_content_type);
//
//        $userRating = null;
//        if ($user) {
//            $id_list_option = $modelContents->getUserListOption($user->id_user,
//                    $content->id_content, $options->metafield);
//            if ($id_list_option) {
//                $userRating = $modelContents->findListOption($id_list_option);
//            }
//        }

        // Use different template if allow half star or not
        $template = $metafield->getOption('half')
            ? 'blocks/content-rating-half.phtml'
            : 'blocks/content-rating.phtml';

        $multiOptions = $metafield->getOption('multiOptions');
        $multiOptions = $multiOptions[$metafield->getOption('half')];

        $xhtml = $this->view->partial($template, array(
                'block'        => $block,
                'content'      => $content,
                'metafield'    => $metafield,
                'contentRating'  => $contentRating,
                'multiOptions' => $multiOptions,
//                'userRating'  => $userRating,
//                'list'        => $list,
            ));

        return $xhtml;
    }

}