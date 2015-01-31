<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/** Zend_Controller_Action_Helper_Abstract */
require_once 'Zend/Controller/Action/Helper/Abstract.php';

/**
 * Verifica se o usuário tem permissão de acessar um recurso específico
 * e o redireciona para uma página de erro caso não tenha
 *
 * @author Jaime Neto <contato@jaimeneto.com>
 */
class Acl_Action_Helper_Acl extends Zend_Controller_Action_Helper_Abstract
{
    
    public function direct($resource = null, $onlyIfExists = true, $test = null)
    {
        $this->redirectIfNotAllowed($resource, $onlyIfExists, $test);
    }
    
    public function isAllowed($resource = null, $onlyIfExists=true)
    {
        $acl = Zend_Registry::get('Zend_Acl');

        if ($onlyIfExists && !$acl->has($resource)) {
            return true;
        }

        $role = Acl_Model_Auth::getRole();

        $theme = Acl_Model_Auth::getTheme();
        if (Zend_Auth::getInstance()->hasIdentity()
            && Zend_Auth::getInstance()->getIdentity()->theme != $theme) {
            return false;
        }

        if($role == 'master') {
            $debug = Zend_Controller_Front::getInstance()
                ->getPlugin('ZFDebug_Controller_Plugin_Debug');
            if ($debug) {
                $debug->getPlugin('log')
                        ->mark('Acl_Action_Helper_Acl::isAllowed(' .
                                $resource . ')', true);
            }
        }

        return $acl->isAllowed($role, $resource);
    }
    
    public function redirectIfNotAllowed($resource = null, $onlyIfExists=null,
        $test=null)
    {
        if (!$this->isAllowed($resource, $onlyIfExists) && !$test) {
            if ($this->getActionController()->getRequest()->isXmlHttpRequest()) {
                $msg = 'Permissão negada';
                if (!Zend_Auth::getInstance()->hasIdentity()) {
                    $msg .= '. Efetue login';
                }
                $this->getActionController()->getHelper('json')->direct(array(
                    'status' => 0,
                    'msg'    => $msg
                ));
            } else {
                $url = 'error/permission';
                if (!Zend_Auth::getInstance()->hasIdentity()) {
                    $params = $this->getActionController()->getRequest()->getParams();
                    $baseUrl = Zend_Controller_Front::getInstance()->getBaseUrl();
                    $loginUrl = trim(str_replace(array($baseUrl, '/'), array('','|'),
                        $this->getActionController()->getHelper('Url')->url($params)),
                    '|');
                    
                    $url = "login/{$loginUrl}";
                }

                $this->getActionController()->getHelper('redirector')
                     ->goToUrlAndExit($url);
            }
        }
    }
    
}