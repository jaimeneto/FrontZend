<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Zend_View_Helper_FormMetaRelationshipSearch 
        extends Bootstrap_View_Helper_FormMultiCheckbox
{
    public function formMetaRelationshipSearch($name, $value = null, 
            $attribs = null, $options = null, $listsep = '')
    {
        // retrieve attributes for text input (prefixed with 'text_' or 'text')
        $text_attribs = array(
            'class'        => 'form-control',
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
        if (isset($attribs['type'])) {
            $text_attribs['data-type'] = $attribs['type'];
            unset($attribs['type']);
        }
        if (isset($attribs['related'])) {
            $text_attribs['data-related'] = 
                    implode(',', (array) $attribs['related']);
            unset($attribs['related']);
        }
        if (isset($attribs['multiple']) && $attribs['multiple']) {
            $text_attribs['data-multiple'] = 1;
            unset($attribs['multiple']);
        }

        $pattern = @preg_match('/\pL/u', 'a')
            ? '/[^\p{L}\p{N}\-\_]/u'    // Unicode
            : '/[^a-zA-Z0-9\-\_]/';     // No Unicode
        $filter = new Zend_Filter_PregReplace($pattern, '');
        $textId = $filter->filter($name);

        if ($options) {
            foreach($options as $key => &$option) {
                $option = $this->view->escape($option);
                if ($text_attribs['data-type'] == 'contents') {
                    $option .= ' <a href="'
                        . $this->view->baseUrl("content/content/edit/id/{$key}")
                        . '" target="_blank" title="editar conteúdo">'
                        . '<span class="glyphicon glyphicon-edit"></span></a> '
                        . '<a href="'
                        . $this->view->baseUrl("content/content/view/id/{$key}")
                        . '" target="_blank" title="Acessar página do conteúdo">'
                        . '<span class="glyphicon glyphicon-globe"></span></a>';
                }
            }
        }

        $xhtml = '<div class="input-group">'
               . '<span class="input-group-addon">' 
               . '<span class="glyphicon glyphicon-search"></span></span>'
               . $this->view->formText("{$textId}_search", '', $text_attribs)
               . '</div>'
               . $this->formMultiCheckbox($name, $value, $attribs, $options, 
                       $listsep);

        return $xhtml;
    }
}
