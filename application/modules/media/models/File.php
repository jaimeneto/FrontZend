<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Model_File extends FrontZend_Module_Model_Abstract
{
    static private $_basePath;
    static private $_thumbsPath;

    const TYPE_IMAGE = 'img';
    const TYPE_VIDEO = 'vid';
    const TYPE_DOCUMENT = 'doc';

    /**
     *
     * @return Zend_Date
     */
    public function getDateCreated()
    {
        return parent::getDate('dt_created');
    }

    /**
     *
     * @return Zend_Date
     */
    public function getDateUpdated()
    {
        return parent::getDate('dt_updated');
    }

    /**
     *
     * @return Acl_Model_User
     */
    public function getUser()
    {
        $idUser = $this->id_user;
        if ($idUser) {
            return FrontZend_Container::get('AclUser')->findById($idUser);
        }
    }

    public function getImageSize()
    {
        $imageSize = getimagesize($this->getRealPath());
        return (object) array('width' => $imageSize[0], 'height' => $imageSize[1]);
    }
    
    /**
     *
     * @param String $path 
     * @return void
     */
    static public function setBasePath($path)
    {
        self::$_basePath = $path;

        $fullPath = self::getFullPath();
        if(!is_dir($fullPath)) {
            mkdir($fullPath, 0777, true);
        }
    }

    /**
     *
     * @param  String $path
     * @return String
     */
    static public function getBasePath($path = null)
    {
        return is_string($path)
            ? self::$_basePath . '/' . $path
            : self::$_basePath;
    }

    /**
     *
     * @return String
     */
    static public function getFullPath($path = null)
    {
        return PUBLIC_PATH . DIRECTORY_SEPARATOR . self::getBasePath($path);
    }

    /**
     *
     * @param String $path
     * @return void
     */
    static public function setThumbsPath($path)
    {
        self::$_thumbsPath = $path;

        $fullThumbsPath = self::getFullThumbsPath();
        if(!is_dir($fullThumbsPath)) {
            mkdir($fullThumbsPath, 0777, true);
        }
    }

    /**
     *
     * @return String
     */
    static public function getThumbsPath($path = null)
    {
        return is_string($path)
            ? self::$_thumbsPath . '/' . $path
            : self::$_thumbsPath;
    }

    /**
     *
     * @return String
     */
    static public function getFullThumbsPath($path = null)
    {
        return PUBLIC_PATH . DIRECTORY_SEPARATOR . self::getThumbsPath($path);
    }

    public function getPath()
    {
        $path = $this->path;
        return self::getBasePath($path);
    }

    public function getRealPath()
    {
        $path = $this->path;
        return str_replace('/', DIRECTORY_SEPARATOR, self::getFullPath($path));
    }

    public function getThumbPath($width, $height, $fill=false)
    {
        $path = $this->path;
        $path = substr($path, 0, strrpos($path, '.'))
              . "_{$width}x{$height}"
              . ($fill ? 'f' : '')
              . substr($path, strrpos($path, '.'));
        return self::getThumbsPath($path);
    }

    public function getThumbRealPath($width=null, $height=null, $fill=false)
    {
        $path = $this->path;
        $path = substr($path, 0, strrpos($path, '.'))
              . "_{$width}x{$height}"
              . ($fill ? 'f' : '')
              . substr($path, strrpos($path, '.'));
        return str_replace('/', DIRECTORY_SEPARATOR, self::getFullThumbsPath($path));
    }

    static public function getInputFilter()
    {
        return array(            
            'id_file' => array(
                'filters' => array(
                    'Int'
                ),
            ),

            'path' => array(
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
                            'max'      => 255,
                        ),
                    ),
                ),
            ),

            'type' => array(),
            
            'credits' => array(
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
                            'max'      => 255,
                        ),
                    ),
                ),
            ),
            
            'info' => array(
                'required' => true,
                'filters' => array(
                    'StringTrim'
                ),
            ),
            
            'keywords' => array(
                'filters' => array(
                    'StringTrim',
                    'StripTags',
                ),
            ),
            
            'original_name' => array(
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
                            'max'      => 100,
                        ),
                    ),
                ),
            ),
            
            'id_user' => array(
                'required' => true,
                'filters' => array(
                    'Int'
                ),
                'validators' => array(
//                    array(
//                        'name'    => 'Db_RecordExists',
//                        'options' => array(
//                            'table' => 'acl_user',
//                            'field' => 'id_user',
//                        ),
//                    ),                    
                ),
            ),
            
            'dt_created' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),                    
                ),
            ),
            
            'dt_updated' => array(
                'validators' => array(
                    array(
                        'name'    => 'Date',
                        'options' => array(
                            'format' => 'yyyy-MM-dd HH:mm:ss',
                        ),
                    ),                    
                ),
            ),
            
        );
    }
    
}
