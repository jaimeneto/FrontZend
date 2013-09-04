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
require_once 'Zend/View/Helper/FormElement.php';


class Content_View_Helper_FormFiles extends Zend_View_Helper_FormElement
{
    public function formFiles($name, $value = null, $attribs = null,
        $options = null, $listsep = '')
    {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable

        $multiple = isset($attribs['multiple'])
                  ? (bool) $attribs['multiple'] : false;
        unset($attribs['multiple']);
        
        $sortable = isset($attribs['sortable'])
                  ? (bool) $attribs['sortable'] : false;
        unset($attribs['sortable']);
        
        // retrieve attributes for labels (prefixed with 'label_' or 'label')
        $label_attribs = array();
        foreach ($attribs as $key => $val) {
            $tmp    = false;
            $keyLen = strlen($key);
            if ((6 < $keyLen) && (substr($key, 0, 6) == 'label_')) {
                $tmp = substr($key, 6);
            } elseif ((5 < $keyLen) && (substr($key, 0, 5) == 'label')) {
                $tmp = substr($key, 5);
            }

            if ($tmp) {
                // make sure first char is lowercase
                $tmp[0] = strtolower($tmp[0]);
                $label_attribs[$tmp] = $val;
                unset($attribs[$key]);
            }
        }

        $labelPlacement = 'append';
        foreach ($label_attribs as $key => $val) {
            switch (strtolower($key)) {
                case 'placement':
                    unset($label_attribs[$key]);
                    $val = strtolower($val);
                    if (in_array($val, array('prepend', 'append'))) {
                        $labelPlacement = $val;
                    }
                    break;
            }
        }

        // the radio button values and labels
        $options = (array) $options;

        // build the element
        $xhtml = '';
        
        $list  = array();

        // should the name affect an array collection?
        $name = $this->view->escape($name);

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        $files = array();
        if ($value) {
            $files = FrontZend_Container::get('File')->findAll(array(
                'where' => array('id_file IN (' . implode(',', $value) . ')')
            ));
        }

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) 
            && !$this->view->doctype()->isXhtml())
        {
            $endTag= '>';
        }

        $xhtml .= '<input id="select_'
                . str_replace('[', '_', trim($name, '[]')) . '"'
                . ' class="btn btn-select-image"'
                . ' type="button" value="Escolher imagem..." ' 
                . $endTag
                . '<ul class="thumbnails'
                    . ($multiple ? '' : ' single') 
                    . ($multiple && $sortable ? ' sortable-list' : '')
                    . '">'
                    . '<li class="image-uploader"></li>'
        ;

        if ($files) {
            $itemOrder = array_flip($value);
            foreach($files as $file) {
                $src = $this->view->baseUrl($file->getPath());
                // generate ID
                $optId = $id . '-' . $file->getId();

                $item = '<li id="files_' . $optId . '">'
                      . $this->view->formHidden($name, $file->getId(),
                                                array('id' => $optId))

                      . ($multiple && $sortable
                          ? '<i class="icon-move"></i>'
                          : '')

                      . '<a id="remove_' . $optId. '" '
                      . 'class="pull-right file-remove" '
                      . 'href="#files_' . $optId . '" title="Remover">'
                      . '<i class="icon-remove"></i>'
                      . '</a>'

                      . '<div id="' . $optId. '_preview" '
                      . 'class="thumbnail file-preview"'
                      . $this->_htmlAttribs($attribs)
                      . '>'
                      . '<img src="' . $src . '" '
                      . 'alt="' . ($escape ? $this->view->escape($file->info)
                                           : $file->info) . '" '
                      . $endTag
                      . '</div>'
                      . '</li>';

                // add to the array of radio buttons
                $list[$itemOrder[$file->id]] = $item;
            }
            ksort($list);
        }

        $listsep = '';
        $xhtml .= implode($listsep, $list);
        $xhtml .= '</ul>';
        
        return $xhtml;
    }
}
