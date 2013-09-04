<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Acl_AuthController extends Zend_Controller_Action
{
    public function indexAction() 
    {
        $this->_forward('login');
    }
    
    public function loginAction() 
    {
        $url = $this->_getParam('url');

        // se já estiver logado redireciona para a página inicial
        if(Acl_Model_Auth::isLoggedIn()){
            $this->getHelper('Redirector')->gotoUrlAndExit('');
        }
        
        $request = $this->getRequest();
        $loginForm = new Acl_Form_Login();
        $loginForm->setAction($this->view->url(array(), 'login'));
        
        if($request->isPost()){
            
            if (!Zend_Session::isStarted()) {
                Zend_Session::start();
            }
            $theme = Acl_Model_Auth::getTheme();
            $namespace = new Zend_Session_Namespace('FrontZend_' . $theme);
            
            if($loginForm->isValid($request->getPost())){
                
                $authAdapter = $this->getAuthAdapter();
                
                // pega o usuário e senha enviado via form
                $username = $loginForm->getValue('username');
                $password = $loginForm->getValue('password');
                
                // passa para o adapter os parâmetros a serem validados
                $authAdapter
                    ->setIdentity($username)
                    ->setCredential($password)
                    ->setCredentialTreatment('MD5(?)');
                
                $auth = Zend_Auth::getInstance();
                $result = $auth->authenticate($authAdapter);
                
                // se o usuário for válido
                if($result->isValid()){
                    
                    // armazena todas as informações do usuário com 
                    // exceção da senha
                    $userInfo = $authAdapter->getResultRowObject(null, 
                                                                 'password');
                    $userInfo->theme = $theme;
                    
                    // prepara armazenamento das informações da sessão
                    $authStorage = $auth->getStorage();
                    $authStorage->write($userInfo);
                    
                    if ($url) {
                        if (strstr($url, '|')) {
                            $url = str_replace('|', '/', $url);
                        }
                    }
                    
                    $this->getHelper('Redirector')->gotoUrlAndExit($url);
                    $namespace->showCaptcha = false;
                }
                else {
                    $this->getHelper('alerts')
                         ->addError('Usuário ou senha incorretos');
                    $namespace->showCaptcha = true;
                    $loginForm->initCaptcha();
                }
            }
        }
        $this->view->loginForm = $loginForm;
        
        $this->renderScript('acl-login.phtml');
    }
    
    public function logoutAction()
    {
        // limpa a sessão e redireciona para a página inicial da gerência
        Zend_Auth::getInstance()->clearIdentity();
        Zend_Session::destroy();
        $this->getHelper('Redirector')->gotoUrlAndExit($url);
    }
    
    /**
     * Pega o adaptador de autenticação a partir de uma tabela do banco
     * 
     * @return Zend_Auth_Adapter_DbTable 
     */
    protected function getAuthAdapter()
    {
        $dbAdapter = Zend_Db_Table::getDefaultAdapter();
        $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);

        $authAdapter->setTableName('acl_user')
                    ->setIdentityColumn('username')
                    ->setCredentialColumn('password');
                    
        return $authAdapter;
    }


}

