<?php

require_once 'Zend/View/Helper/Abstract.php';

class Twitter_Bootstrap_View_Helper_Alerts extends Zend_View_Helper_Abstract
{
    private static $_types = array(
        'ALERT'     => 'alert',
        'SUCCESS'   => 'alert alert-success',
        'INFO'      => 'alert alert-info',
        'ERROR'     => 'alert alert-error',
        'DANGER'    => 'alert alert-danger'
    );

    public function alerts($closeButton=true, $uniqueMessages=true,
        $id='alerts')
    {
        $helper = Zend_Controller_Action_HelperBroker::getStaticHelper('Alerts');
        if (!$helper) {
            return '';
        }

        $messages = $helper->getMessages();

        if ($uniqueMessages) {
            $this->uniqueMessages($messages);
        }

        if (count($messages) < 1) {
            return '';
        }
        
        $xhtml = '<ul class="unstyled"';

        if ($id) {
            $xhtml .= ' id="' . $this->view->escape($id) . '"';
        }

        $xhtml .= '>';

        foreach($messages as $message) {
            $class = self::$_types[$message['type']];
            $text  = $message['escape']
                ? $this->view->escape($this->view->translate($message['text']))
                : $this->view->translate($message['text']);
            $xhtml .= '<li class="' . $class . '">';

            if ($closeButton) {
                $xhtml .= '<a class="close" data-dismiss="alert" href="#">&times;</a>';
            }

            $xhtml .= $text . '</li>' . PHP_EOL;
        }
        $xhtml .= '</ul>';
        return $xhtml;
    }

    public function uniqueMessages(array &$messages)
    {
        $stringMessages = array();
        if ($messages) {
            foreach($messages as $key => $message) {
                $stringMessages[$key] = $message['type'] 
                                      . ' - '
                                      . $message['text'];
            }
            $keepKeys = array_keys(array_unique($stringMessages));
            $allKeys = array_keys($messages);
            $delKeys = array_diff($allKeys, $keepKeys);

            foreach($delKeys as $key) {
                unset($messages[$key]);
            }
        }
    }

}
