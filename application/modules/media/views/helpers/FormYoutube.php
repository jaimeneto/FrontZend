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


class Media_View_Helper_FormYoutube extends Zend_View_Helper_FormElement
{
    public function formYoutube($name, $value = null, $attribs = null,
        $options = null, $listsep = "<br />\n")
    {
        $info = $this->_getInfo($name, $value, $attribs, $options, $listsep);
        extract($info); // name, value, attribs, options, listsep, disable

        // the radio button values and labels
        $options = (array) $options;

        // build the element
        $xhtml = '';
        
        // should the name affect an array collection?
        $name = $this->view->escape($name);
        $id = $this->view->escape($id);

        // ensure value is an array to allow matching multiple times
        $value = (array) $value;

        // XHTML or HTML end tag?
        $endTag = ' />';
        if (($this->view instanceof Zend_View_Abstract) && 
                !$this->view->doctype()->isXhtml()) {
            $endTag= '>';
        }

        $xhtml .= $this->view->formButton('select_' . $id,
                    'Buscar video no youtube...',
                    array('class' => 'btn btn-youtube-search'))
                . '<div class="row" id="' . $id . '_preview">';
        
        // Set up the filter - Alnum + hyphen + underscore
        require_once 'Zend/Filter/PregReplace.php';
        $pattern = @preg_match('/\pL/u', 'a') 
            ? '/[^\p{L}\p{N}\-\_]/u'    // Unicode
            : '/[^a-zA-Z0-9\-\_]/';     // No Unicode
        $filter = new Zend_Filter_PregReplace($pattern, "");
        
        $list = array();

        // add radio buttons to the list.
        foreach ($value as $val) if ($val) {

            $item = '<div id="files_' . $id . '-' . $val . '" class="col-xs-6">'
                  . $this->view->formHidden($name, $val)
                  . '<div id="' . $id. '_preview" class="youtube-preview"'
                  . $this->_htmlAttribs($attribs)
                  . '>'
                  . '<a id="remove_' . $id . '" class="file-remove pull-right"'
                  . 'href="#files_' . $id . '-' . $val . '" title="Remover">'
                  . '<span class="glyphicon glyphicon-remove"></span>'
                  .'</a>'
                  . $this->view->youtubeVideo($val, 360, 270)
                  . '</div>'
                  . '</div>';

            // add to the array of radio buttons
            $list[] = $item;
        }
        
        // done!
        $xhtml .= implode($listsep, $list);
        $xhtml .= '</div>';
        
        return $xhtml;
    }
}
