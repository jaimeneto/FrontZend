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
        $a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞ$ßàáâãäåæ@çèéêë&ìíîïðñòóôõöøùúûü' 
           . 'ýýþÿŔŕ°ºª¹²³,.;:\|/"^~*%# ()[]{}><=!?`´‘’¨¬§' . "'";
        $b = 'aaaaaaaceeeeiiiidnoooooouuuuybssaaaaaaaaceeeeeiiiidnoooooouuuu' 
           . 'yybyRrooa123--------------------------------' . '-';
        
        $string = utf8_decode($string);
        $string = strtr($string, utf8_decode($a), $b);
        $string = strtolower($string);
        $string = preg_replace('/--+/', '-', $string);
        $string = trim($string, '-');
        return utf8_encode($string);
    }
}
