<?php

require_once 'Zend/View/Helper/Abstract.php';

class Twitter_Bootstrap_View_Helper_Alert extends Zend_View_Helper_Abstract
{
    private static $_types = array(
        'ALERT'     => 'alert',
        'SUCCESS'   => 'alert alert-success',
        'INFO'      => 'alert alert-info',
        'ERROR'     => 'alert alert-error',
        'DANGER'    => 'alert alert-danger'
    );

    public function alert($text, $type = 'ALERT', $closeButton=true, 
        $escape=true, $tag='div')
    {
        $class = self::$_types[strtoupper($type)];
        $text  = $escape
            ? $this->view->escape($this->view->translate($text))
            : $this->view->translate($text);
        
        $xhtml = '<' . $tag . ' class="' . $class . '">';
        if ($closeButton) {
            $xhtml .= '<a class="close" data-dismiss="alert" href="#">&times;</a>';
        }
        $xhtml .= $text . '</' . $tag . '>' . PHP_EOL;
     
        return $xhtml;
    }

}
