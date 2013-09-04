<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_View_Helper_SyncLayoutFiles extends Zend_View_Helper_Abstract
{
    static $processed = false;

    /**
     *
     * @param Array|String $dirs
     * @return html
     */
    public function syncLayoutFiles($dirs, $layout=null, $env='frontend',
        $force=false)
    {
        if (!$layout) {
            $layout = Acl_Model_Auth::getTheme($env);
        }

        if (!is_array($dirs)) {
            $dirs = (array) $dirs;
        }

        // garante que vai ser executado apenas uma vez por requisição
        if (!$force) {
            if (self::$processed) return;
            self::$processed = true;
        }

        $layoutDir = APPLICATION_PATH . "/layouts/{$env}/{$layout}";
        $layoutCacheDir = PUBLIC_PATH . "/cache/layouts/{$env}/{$layout}";
        if (!is_dir($layoutDir)) return;

        foreach($dirs as $dir) {
            $dirToSync = "{$layoutDir}/{$dir}";
            if (is_dir($dirToSync)) {
                $destDir = "{$layoutCacheDir}/{$dir}";

                $this->_copyFilesFromDir($dirToSync, $dirToSync, $destDir);
            }
        }
    }

    private function _copyFilesFromDir($dir, $dirToSync, $destDir)
    {
        $iDir = new DirectoryIterator($dir);
        foreach ($iDir as $file) {
            if ($file->isDir() && !$file->isDot() && strpos($file->getFilename(), '.') !== 0) {
                $this->_copyFilesFromDir($file->getPathname(), $dirToSync, $destDir);
            } else if ($file->isFile()) {
                $cacheDir = ($dir == $dirToSync)
                    ? $destDir
                    : $destDir . str_replace($dirToSync, '', $dir);

                // Verifica se a pasta de cache de imagens existe e cria se não existir
                if (!is_dir($cacheDir)) mkdir($cacheDir, 0777, true);

                $cacheFile =  $cacheDir . '/' . $file->getFilename();
                // Se o arquivo de cache não existe, cria
                if (!file_exists($cacheFile)) {
                    copy($file->getPathname(), $cacheFile);
                    // Se existe, mas o original foi alterado, substitui
                } else if (filemtime($file->getPathname()) > filemtime($cacheFile)) {
                    unlink($cacheFile);
                    copy($file->getPathname(), $cacheFile);
                }
            }
        }
    }

}