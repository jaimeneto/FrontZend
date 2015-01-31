<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_Model_Auth extends Zend_Auth
{
    public static function isLoggedIn()
    {
        return Zend_Auth::getInstance()->hasIdentity() 
                && isset(Zend_Auth::getInstance()->getIdentity()->theme) 
                && Zend_Auth::getInstance()->getIdentity()->theme 
                    == self::getTheme();
    }

    public static function getRole()
    {
        return Zend_Auth::getInstance()->hasIdentity() 
            && isset(Zend_Auth::getInstance()->getIdentity()->id_role)
                ? Zend_Auth::getInstance()->getIdentity()->id_role
                : 'guest';
    }

    public static function is($role)
    {
        return self::getRole() == $role;
    }

    public static function getTheme()
    {
        return Zend_Auth::getInstance()->hasIdentity() &&
            isset(Zend_Auth::getInstance()->getIdentity()->theme) &&
            Zend_Auth::getInstance()->getIdentity()->theme
                ? Zend_Auth::getInstance()->getIdentity()->theme
                : FrontZend_Container::get('LayoutTheme')->findActive()->theme;
    }

}