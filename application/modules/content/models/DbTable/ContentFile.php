<?php

class Content_Model_DbTable_ContentFile 
    extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'content_file';
    protected $_primary      = 'id_content_file';
    protected $_modelClass   = 'Content_Model_ContentFile';
    protected $_enablePrefix = true;

    protected $_referenceMap = array(
        'File' => array(
            'columns' => 'id_file',
            'refTableClass' => 'Content_Model_DbTable_File',
            'refColumns' => 'id_file'
        ),
        'Content' => array(
            'columns' => 'id_content',
            'refTableClass' => 'Content_Model_DbTable_Content',
            'refColumns' => 'id_content'
        )
    );

    /**
     * Busca uma lista de Content_Model_ContentMetafield de um conteúdo.
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
     * Busca uma lista de Content_Model_ContentFile de um arquivo.
     * O parâmetro pode ser um objeto Content_Model_File ou o id do arquivo
     *
     * @param mixed $file
     * @return array
     */
    public function fetchByFile($file)
    {
        $idFile = $file instanceof Content_Model_File
            ? $file->getId()
            : $file;

        $options = array('id_file = ?' => $idFile);

        return parent::fetchAll($options);
    }

}
