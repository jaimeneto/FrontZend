<?php

require_once 'Zend/Session.php';
require_once 'Zend/Controller/Action/Helper/Abstract.php';


/**
 * Classe utilizada para adicionar mensagens na sessao, sendo util na
 * implementacao do padrao PRG (Post/Get/Redirect).
 * 
 * @author Jaime Neto <contato@jaimeneto.com>
 */
class Twitter_Bootstrap_Controller_Action_Helper_Alerts
    extends Zend_Controller_Action_Helper_Abstract
    implements Countable
{
    /**
     * Constante utilizada para denotar uma mensagem de alerta
     */
    const ALERT  = 'ALERT';

    /**
     * Constante utilizada para denotar uma mensagem de perigo
     */
    const DANGER  = 'DANGER';

    /**
     * Constante utilizada para denotar uma mensagem de erro
     */
    const ERROR   = 'ERROR';

    /**
     * Constante utilizada para denotar uma mensagem informativa
     */
    const INFO    = 'INFO';

    /**
     * Constante utilizada para denotar uma mensagem de sucesso
     */
    const SUCCESS = 'SUCCESS';
    
    /**
     * Array com os tipos de mensagem permitidos
     * @var Array
     */
    private static $_types = array
    (
        self::ALERT,
        self::DANGER,
        self::ERROR,
        self::INFO,
        self::SUCCESS
    );

    /**
     * Namespace de sessao utilizado para as mensagens
     * @var Zend_Session_Namespace
     */
    protected $_namespace;

    /**
     * Inicializa o namespace de sessao
     */
    public function __construct()
    {
        $this->_namespace = new Zend_Session_Namespace($this->getName());
        if (!isset($this->_namespace->messages)) {
            $this->_namespace->messages = array();
        }
    }

    /**
     * Metodo comum para adicionar mensagens a sessao
     *
     * @param string $text  Mensagem
     * @param string $type  Tipo
     * @return void
     * @throws InvalidArgumentException Se a mensagem nao for uma string ou
     *      se o tipo de mensagem informado for invalido
     */
    public function addMessage($text, $type = self::ALERT, $escape=true)
    {
        if (!(is_string($text) && in_array($type, self::$_types))) {
            throw new InvalidArgumentException();
        }

        $message = array (
            'text'   => $text,
            'type'   => $type,
            'escape' => $escape
        );

        $this->_namespace->messages[] = $message;
        return $this;
    }

    /**
     * Adicionar uma mensagem de alerta
     *
     * @param string $msg   Mensagem
     * @return void
     */
    public function addAlert($msg)
    {
        $this->addMessage($msg, self::ALERT);
    }

    /**
     * Adicionar uma mensagem de perigo
     *
     * @param string $msg   Mensagem
     * @return void
     */
    public function addDanger($msg)
    {
        $this->addMessage($msg, self::DANGER);
    }

    /**
     * Adicionar uma mensagem de erro
     *
     * @param string $msg   Mensagem de erro
     * @return void
     */
    public function addError($msg)
    {
        $this->addMessage($msg, self::ERROR);
    }

    /**
     * Adicionar uma mensagem de informacao
     *
     * @param string $msg   Mensagem
     * @return void
     */
    public function addInfo($msg)
    {
        $this->addMessage($msg, self::INFO);
    }

    /**
     * Adicionar uma mensagem de sucesso
     *
     * @param string $msg   Mensagem
     * @return void
     */
    public function addSuccess($msg)
    {
        $this->addMessage($msg, self::SUCCESS);
    }

    /**
     * Retorna as mensagens atualmente adicionada a sessao
     *
     * @param string $type   Filtro de tipo
     * @return array
     */
    public function getMessages($type = null, $cleanMessages = true)
    {
        if ($type === null) {
            $messages = $this->_namespace->messages;
            if ($cleanMessages) $this->cleanMessages();
            return $messages;
        }

        if (!array_key_exists($type, self::$_types))
            throw new InvalidArgumentException();

        $messages = array();

        $n = count($this->_namespace->messages);
        for ($i = 0; $i < $n; $i++)
        {
            $message =& $this->_namespace->messages[$i];

            if ($message['type'] == $type) {
                $messages[] = $message;
                if ($cleanMessages) unset($this->_namespace->messages[$i]);
            }
        }

        return $messages;
    }
    
    /**
     * Apaga as mensagens atualmente adicionadas a sessao
     *
     * @return void
     */
    public function cleanMessages()
    {
        $this->_namespace->messages = array();
    }

    /**
     * Implements the Countable interface
     *
     * @return int  Quantidade de mensagens armazenadas na sessao
     */
    public function count()
    {
        return count($this->_namespace->messages);
    }

    /**
     * Strategy pattern: proxy to addMessage()
     */
    public function direct($text, $type = self::ALERT)
    {
        return $this->addMessage($text, $type);
    }
}
