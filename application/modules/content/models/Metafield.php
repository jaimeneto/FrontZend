<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Content_Model_Metafield extends FrontZend_Module_Model_Abstract
{
    static protected $_fieldtypes = array(
        'field'        => array(
            'text'     => 'Campo de texto',
            'select'   => 'Lista de opções',
            'rating'   => 'Avaliação',
            'youtube'  => 'Video do Youtube',
        ),
        'relationship' => array(
            'contents' => 'Conteúdo',
            'users'    => 'Usuários',
        ),
        'file' => array(
            'images' => 'Imagens',
//            'audio'  => 'Áudio',
//            'videos' => 'Vídeos',
//            'docs'   => 'Documentos',
        ),
    );

    static public function getFieldtypes($datatype=null, $type=null)
    {
        return $datatype
            ? ($type
                ? self::$_fieldtypes[$datatype][$type]
                : self::$_fieldtypes[$datatype])
            : self::$_fieldtypes;
    }

    public function getFieldtype()
    {
        $datatype = $this->datatype;
        $type = $this->getOption('type');
        return self::getFieldtypes($datatype, $type);
    }

    public function __set($columnName, $value)
    {
        // Caso o atributo seja 'options' e o valor seja um array, ele é
        // convertido para json
        if ($columnName == 'options' && is_array($value)) {
            $value = Zend_Json::encode($value);
        }

        return parent::__set($columnName, $value);
    }

    /**
     * Retorna as opções do metafield convertidas em array
     *
     * @return array
     */
    public function getOptions()
    {
        return Zend_Json::decode($this->options);
    }

    public function hasOption($option)
    {
        $options = $this->getOptions();
        return isset($options[$option]);
    }

    /**
     * Retorna o valor de uma opção
     *
     * @param string $option
     * @return mixed
     */
    public function getOption($option)
    {
        $options = $this->getOptions();
        return isset($options[$option]) ? $options[$option] : null;
    }

    /**
     *
     * @return Content_Model_ContentType
     */
    public function getType()
    {
        $idContentType = $this->id_content_type;
        if ($idContentType) {
            return FrontZend_Container::get('ContentType')->findById($idContentType);
        }
    }

    /**
     * @see Content_Model_Metafield::getType()
     */
    public function getContentType()
    {
        return $this->getType();
    }
    
    public function getInputFilter()
    {
        return array(            
            'id_metafield' => array(
                'filters' => array(
                    'Int'
                ),
            ),
            
            'id_content_type' => array(
                'required' => true,
//                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'content_type',
//                            'field' => 'id_content_type',
//                        ),
//                    ),
//                ),
            ),

            'datatype' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 15,
                        ),
                    ),
                ),
            ),
            
            'fieldname' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim',
                    'StripTags'
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 20,
                        ),
                    ),
                ),
            ),

            'options' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim'
                ),
            ),

            'order' => array(
                'filters' => array(
                    'Int'
                ),
            ),
        );
    }
    
}