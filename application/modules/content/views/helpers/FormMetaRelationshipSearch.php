<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Zend_View_Helper_FormMetaRelationshipSearch extends Twitter_Bootstrap_View_Helper_FormMultiCheckbox
{
    public function formMetaRelationshipSearch($name, $value = null, $attribs = null,
                                      $options = null, $listsep = '')
    {
        // retrieve attributes for text input (prefixed with 'text_' or 'text')
        $text_attribs = array(
            'class'        => 'input-block-level',
            'data-name'    => $name
        );
        foreach ($attribs as $key => $val) {
            $tmp    = false;
            $keyLen = strlen($key);
            if ((5 < $keyLen) && (substr($key, 0, 5) == 'text_')) {
                $tmp = substr($key, 5);
            } elseif ((4 < $keyLen) && (substr($key, 0, 4) == 'text')) {
                $tmp = substr($key, 4);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $text_attribs[$tmp] = $val;
                unset($attribs[$key]);
            }
        }
        $text_attribs['class'] .= ' content-search';
        if (isset($attribs['related'])) {
            $text_attribs['data-type'] = $attribs['related'];
            unset($attribs['related']);
        }
        if (isset($attribs['multiple']) && $attribs['multiple']) {
            $text_attribs['data-multiple'] = 1;
            unset($attribs['multiple']);
        }

        $pattern = @preg_match('/\pL/u', 'a')
            ? '/[^\p{L}\p{N}\-\_]/u'    // Unicode
            : '/[^a-zA-Z0-9\-\_]/';     // No Unicode
        $filter = new Zend_Filter_PregReplace($pattern, "");
        $textId = $filter->filter($name);

        if ($options) {
            foreach($options as $key => &$option) {
                $option = $this->view->escape($option);
                if ($attribs['type'] == 'users') {
                    continue;
                }
                
                $option .= ' <a href="'
                        . $this->view->baseUrl("content/content/edit/id/{$key}")
                        . '" target="_blank" title="editar conteúdo">'
                        . '<i class="icon-edit"></i></a> '
                        . '<a href="'
                        . $this->view->baseUrl("content/content/view/id/{$key}")
                        . '" target="_blank" title="Acessar página do conteúdo">'
                        . '<i class="icon-globe"></i></a>';
            }
        }

        $xhtml = '<div class="control-group">'
                   . '<div class="input-prepend">'
                       . '<span class="add-on"><i class="icon-search"></i></span>'
                       . $this->view->formText("{$textId}_search", '', $text_attribs)
                   . '</div>'
               . '</div>'
               . $this->formMultiCheckbox($name, $value, $attribs, $options, $listsep);

        return $xhtml;
    }
}
