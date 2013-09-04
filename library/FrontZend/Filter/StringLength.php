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

/**
 * Shortens the string down to maximum length defined in $this->_maxLength. If the 
 * string is longer the function finds the last occurance of a space and 
 * adds a suffix (three dots, by default) at the end to illustrate that it 
 * is more text. If the string is without spaces it stops at exacly max 
 * length, also adding the suffix. If the string is shorter than max length 
 * it returns the string as it is. This is useful for previewing long 
 * strings.
 */
class FrontZend_Filter_StringLength implements Zend_Filter_Interface
{
    /**
     * @var integer
     */
    protected $_maxLength = 30;
    
    /**
     * @var integer
     */
    protected $_offset = 0;
    
    /**
     * @var string
     */
    protected $_suffix = '...';
    
    
    /**
     * Constructor
     *
     * @param string|array $options Options to set
     */
    public function __construct($maxLength=null, $offset=null, $suffix=null)
    {
        if ($maxLength) {
            $this->setMaxLength($maxLength);
        }
        
        if ($offset) {
            $this->setOffset($offset);
        }
        
        if ($suffix) {
            $this->setSuffix($suffix);
        }
    }
    
    /**
     * Set the max length
     *
     * @param integer
     * @return FrontZend_Filter_StringLength
     */
    public function setMaxLength($maxLength)
    {
        $this->_maxLength = (int) $maxLength;
        return $this;
    }
    
    /**
     * Retrieve max length
     *
     * @return integer
     */
    public function getMaxLength()
    {
        return $this->_maxLength;
    }
    
    /**
     * Set the offset
     *
     * @param integer
     * @return FrontZend_Filter_StringLength
     */
    public function setOffset($offset)
    {
        $this->_offset = (int) $offset;
        return $this;
    }
    
    /**
     * Retrieve offset
     *
     * @return integer
     */
    public function getOffset()
    {
        return $this->_offset;
    }
    
    /**
     * Set the suffix
     *
     * @param string
     * @return FrontZend_Filter_StringLength
     */
    public function setSuffix($suffix)
    {
        $this->_suffix = (string) $suffix;
        return $this;
    }
    
    /**
     * Retrieve suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->_suffix;
    }
    
    /**
     * @param  string $value
     * @return string
     */
    public function filter($string)
    {
        if (strlen($string) > $this->_maxLength){
            $string = substr($string, $this->_offset, $this->_maxLength);
            $pos = strrpos($string, ' ');
            if($pos === false) {
                $string = substr($string, 0, $this->_maxLength) . $this->_suffix;
            }
            $string = substr($string, 0, $pos) . $this->_suffix;

            if ($this->_offset > 0) {
                $rpos = strpos($string, ' ');
                $string =  $this->_suffix . substr($string, $rpos+1);
            }
        }
        return $string;
    }
}
