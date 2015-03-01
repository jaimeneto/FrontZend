<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @subpackage DbTable
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Model_DbTable_File extends FrontZend_Module_Model_DbTable_Abstract
{
    protected $_name         = 'file';
    protected $_primary      = 'id_file';
    protected $_modelClass   = 'Media_Model_File';
    protected $_enablePrefix = true;

    protected $_dependentTables = array(
        'Content_Model_DbTable_ContentFile',
    );

    protected $_referenceMap = array(
        'User' => array(
            'columns'       => 'id_user',
            'refTableClass' => 'Acl_Model_DbTable_User',
            'refColumns'    => 'id_user'
        ),
        'Original' => array(
            'columns'       => 'id_original',
            'refTableClass' => 'Media_Model_DbTable_File',
            'refColumns'    => 'id_file'
        )
    );

    /**
     * Filtra apenas imagens de uma pasta específica e retorna os objetos
     * de imagem com várias informações extra
     *
     * @param string $path
     * @param array $options
     * @return StdClass
     */
    public function fetchFromPath($path, array $options=null)
    {
        $path = str_replace(DIRECTORY_SEPARATOR, '/', $path);
        $pathSlash = (rtrim($path, '/')) . '/';
        $options['where']['path LIKE ?'] = "{$path}%";
        $options['where'][] = "LOCATE('/', SUBSTR(path,LENGTH('{$pathSlash}')+1)) = 0";

        return $this->findAll($options);
    }

    /**
     * Retorna imagens relacionadas
     *
     * @param integer $idFile
     * @param integer $ignoreId
     * @return array
     */
    public function fetchRelatedFiles($idFile, $ignoreId=null)
    {
        $where = $idFile == $ignoreId
               ? array('id_original = ?' => $idFile)
               : array('id_file = ? OR id_original = ?' => $idFile);
        if ($ignoreId) {
            $where['id_file != ?'] = $ignoreId;
        }
        $options = array('where' => $where);
        return $this->findAll($options);
    }
    
    public function deleteById($id)
    {
        $file = $this->findById($id);
        
        if ($file) {
            $filename = $file->getRealPath();
            if (is_file($filename)&& !unlink($filename)) {
                throw new FrontZend_Exception(
                    'Erro ao tentar excluir o arquivo'
                );
            }
        }
        
        return parent::deleteById($id);
    }
    
    /*************************** Directory methods ***************************/

    public function addDir($dir, $path=null)
    {
        $filterSlug = new FrontZend_Filter_Slug();
        $dir = $filterSlug->filter($dir);

        if ($path) {
            $dir = $path . DIRECTORY_SEPARATOR . $dir;
        }

        $pathname = PUBLIC_PATH
                . DIRECTORY_SEPARATOR . Media_Model_File::getBasePath()
                . DIRECTORY_SEPARATOR . $dir;

        if (file_exists($pathname)) {
            throw new FrontZend_Exception('Uma pasta com este nome já existe');
        }
        
        return mkdir($pathname, 0777, true);
    }

    /**
     * Excluir pasta
     *
     * @return bool
     */
    public function removeDir($dir)
    {
        // critérios para a exclusão dos arquivos localizadas no diretório
        $options = array(
            'where' => array(
                "path LIKE '{$dir}%'"
            )
        );

        $results = $this->findAll($options);
        if ($results) {
            foreach($results as $file){
                $this->deleteById($file->id_file);
            }
        }

        // caminho da pasta selecionada
        $pathname = PUBLIC_PATH
                . DIRECTORY_SEPARATOR . Media_Model_File::getBasePath()
                . DIRECTORY_SEPARATOR . $dir;

        if (is_dir($pathname)) {
            $objects = scandir($pathname);
            foreach ($objects as $object) {
                if ($object != '.' && $object != '..') {
                    if (filetype("{$pathname}/{$object}") == 'dir') {
                        $this->removeDir("{$pathname}/{$object}");
                    } else {
                        unlink($pathname . "/" . $object);
                    }
                }
            }
            reset($objects);

            // excluir a pasta
            return rmdir($pathname);
        }
    }

    /**
     * Renomear pasta de arquivos
     *
     * @return bool
     */
    public function renameDir($curpath, $newname)
    {
        $filterSlug = new FrontZend_Filter_Slug();
        $newpath = strstr($newname, '/') || strstr($newname, DIRECTORY_SEPARATOR)
            ? $newname
            : substr($curpath, 0, strrpos($curpath, DIRECTORY_SEPARATOR)+1)
                 . $filterSlug->filter($newname);

        $newfullpath = Media_Model_File::getFullPath($newpath);
        if (is_dir($newfullpath)) {
            throw new FrontZend_Exception('Já existe uma pasta com este nome');
        }

        $curfullpath = Media_Model_File::getFullPath($curpath);

        if (is_dir($curfullpath)) {
            if (rename($curfullpath, $newfullpath)) {
                try {
                    $this->_renamePath($curpath, $newpath);
                    return true;
                } catch(Exception $e) {
                    rename($newfullpath, $curfullpath);
                    throw $e;
                }

            };
        }
        return false;
    }

    /**
    * Renomear caminho do arquivo no banco de dados quando a pasta for renomeada
    *
    * @param  String $curpath caminho da pasta atual
    *         String $newpath novo caminho da pasta atribuida
    */
    protected function _renamePath($curpath, $newpath)
    {
        $curpath = str_replace(DIRECTORY_SEPARATOR, '/', $curpath);
        $newpath = str_replace(DIRECTORY_SEPARATOR, '/', $newpath);

        $adapter = $this->getAdapter();

        // Inicia as trasações do banco de dados
        $adapter->beginTransaction();
        try {
            $files = $this->findAll(array(
                'where' => array("path LIKE '{$curpath}%'")
            ));

            if($files->count() > 0) {
                // renomeia com o $newpath as ocorreções encontradas
                foreach ($files as $file){
                    $file->path = str_replace($curpath, $newpath, $file->path);
                    FrontZend_Container::get('File')->save($file);
                }
            }

            $contents = FrontZend_Container::get('Content')->findAll(array(
                'where' => array("text LIKE '%{$curpath}%'")
            ));

            if ($contents->count() > 0) {
                foreach($contents as $content) {
                    // renomeia com o $newpath as ocorreções encontradas
                    $content->text = str_replace($curpath, $newpath,
                        $content->text);
                    FrontZend_Container::get('Content')->save($content);
                }
            }

            // Efetiva todas as transações do banco de dados
            $adapter->commit();
        } catch(Exception $e) {
            // Desfaz todas as alterações do banco de dados
            $adapter->rollBack();
            throw $e;
        }
    }

}

