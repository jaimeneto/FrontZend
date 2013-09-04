<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Form
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Formulário de Login
 */
class Acl_Form_Login extends Twitter_Bootstrap_Form_Horizontal
{  
    public function init()
    {
        $this->setAttrib('id', strtolower(__CLASS__));
        $this->setMethod(self::METHOD_POST);
        
        $this->addElement('text', 'username', array(
            'label'    => 'Usuário',
            'required' => true
        ));

        $this->addElement('password', 'password', array(
            'label'    => 'Senha',
            'required' => true
        ));

        $this->initCaptcha();

        $this->addElement('submit', 'btn_login', array(
            'label'  => 'Entrar',
            'ignore' => true,
            'class'  => 'btn btn-primary btn-large'
        ));
    }
    
    public function initCaptcha()
    {
//        $recaptchaCfg = Zend_Registry::get('recaptcha');
//        if (isset($recaptchaCfg['captchaEnabled']) && $recaptchaCfg['captchaEnabled']) {
//
//            if (!Zend_Session::isStarted()) {
//                Zend_Session::start();
//            }
//            $darwinNamespace = new Zend_Session_Namespace('Darwin_' . SITE_THEME);
//            
//            if ($darwinNamespace->showCaptcha) {        
//                $captchaDir = PUBLIC_PATH . '/cache/captcha/';
//
//                if (!is_dir($captchaDir)) {
//                    mkdir($captchaDir);
//                }
//
//                $captcha = new Zend_Form_Element_Captcha('captcha', array(
//                    'label'   => 'Digite o código de segurança',  
//                    'captcha' => array(
//                        'captcha' => 'Image',  
//                        'wordLen' => 4,  
//                        'timeout' => 300,  
//                        'font'    => PUBLIC_PATH . '/_gerencia/font/StencilStd.otf',
//                        'imgDir'  => $captchaDir,  
//                        'imgUrl'  => SITE_URL . '/cache/captcha/',
//                    )
//                ));  
//
////                $recaptcha = new Zend_Service_ReCaptcha($recaptchaCfg['publickey'], $recaptchaCfg['privatekey']);
////                $captcha = new Zend_Form_Element_Captcha('captcha', array(
////                    'captcha'        => 'ReCaptcha',
////                    'captchaOptions' => array(
////                        'captcha' => 'ReCaptcha',
////                        'timeout' => 300,
////                        'theme'   => 'white',
////                        'lang'   => 'pt',
////                        'service' => $recaptcha	
////                    ))
////                );
//
//                $captcha->setOrder(2);
//                $this->addElement($captcha);
//            }
//        }    
    }
    
}