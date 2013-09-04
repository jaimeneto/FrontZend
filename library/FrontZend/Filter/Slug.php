<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Filter
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Zend_Filter_Interface
 */
require_once 'Zend/Filter/Interface.php';

class FrontZend_Filter_Slug implements Zend_Filter_Interface
{
    /**
     * Defined by Zend_Filter_Interface
     *
     * Returns the string $value, converting characters to alias format
     *
     * @param  string $value
     * @return string
     */
    public function filter($string)
    {
        $filteredValue = self::_filter($string);
        return $filteredValue;
    }
    
    private static function _filter($string)
    {
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖŌØÙÚÛÜŪÝÞ$ß£àáâãäåæ@ç¢èéêë&ìíîïðñòóôõöōø'
           . 'ùúûüūýýþÿŔŕ°ºª¹²³¨¬§,.;:\|/"^~*%# ()[]{}=!?`´‘’><' . "'";

        $b = 'aaaaaaaceeeeiiiidnooooooouuuuuybsslaaaaaaaacceeeeeiiiidnooooooo'
           . 'uuuuuyybyRrooa123--------------------------------' . '-';

        $string = strtr($string, $a, $b);
        $string = strtolower($string);
        $string = preg_replace('/--+/', '-', $string);
        $string = trim($string, '-');
        return $string;
    }
}
