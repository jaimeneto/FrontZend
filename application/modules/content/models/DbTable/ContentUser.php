<?php

class Content_Model_DbTable_ContentUser
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content_user';
    protected $_primary      = 'id_content_user';
    protected $_modelClass   = 'Content_Model_ContentUser';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'Content' => array(
            'columns' => 'id_content',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns' => 'id_content'
        ),
        'User' => array(
            'columns' => 'id_user',
            'refTableClass' => 'Acl_Model_DbTable_User',
            'refColumns' => 'id_user'
        )
    );

    /**
     * Busca uma lista de Content_Model_ContentUser de um conteúdo.
     * O parâmetro pode ser um objeto Content_Model_Content ou o id do conteúdo
     *
     * @param mixed $content
     * @return array
     */
    public function fetchByContent($content)
    {
        $idContent = $content instanceof Content_Model_Content
            ? $content->getId()
            : $content;

        $options = array('id_content = ?' => $idContent);

        return parent::fetchAll($options);
    }

    /**
     * Busca uma lista de Content_Model_ContentUser de um usuário.
     * O parâmetro pode ser um objeto Acl_Model_User ou o id do usuário
     *
     * @param mixed $user
     * @return array
     */
    public function fetchByUser($user)
    {
        $idUser = $user instanceof Acl_Model_User
            ? $user->getId()
            : $user;

        $options = array('id_user = ?' => $idUser);

        return parent::fetchAll($options);
    }

}
