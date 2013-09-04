<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_View_Helper_CssHeadLinks extends Zend_View_Helper_Abstract
{
    static $processed = false;

    /**
     *
     * @param Array|String $files
     * @param String $layout
     * @param boolean $minify
     * @param boolean $join
     * @return html
     */
    public function cssHeadLinks($files, $layout=null, $env='frontend',
        $minify=true, $join=true)
    {
        if (!$layout) {
            $layout = Acl_Model_Auth::getTheme($env);
        }

        if (!is_array($files)) {
            $files = (array) $files;
        }

        // garante que vai ser executado apenas uma vez por requisição
        if (self::$processed) return;
        self::$processed = true;

        // verifica o tempo de modificação dos arquivos
        $modTimes = array();
        $validLinks = array();

        $cssCachePath = PUBLIC_PATH . "/cache/layouts/{$env}/{$layout}/css";

        if (count($files) > 0 && !is_dir($cssCachePath)) {
            mkdir($cssCachePath, 0777, true);
        }

        foreach ($files as $i => $file) {
            if (!file_exists($file)) {
                pr($files,1);
                throw new Exception("O arquivo de estilo não existe: $file");
            }

            if (!is_readable($file)) {
                throw new Exception('O arquivo de estilo não pode ser lido: '
                                    . $file);
            }

            if ($join) {
                $modTimes[] = filemtime($file);
                $validFiles[] = $file;
                continue;
            }
            else {

                if (!strstr($file, APPLICATION_PATH)) {
                    $this->view->headLink()->appendStylesheet(
                        $this->view->baseUrl($file));
                    continue;
                }

                $destFile = md5($file) . '_' . filemtime($file)
                          . '_'. basename($file);
                $destPath = $cssCachePath . '/' . $destFile;

                if (!file_exists($destPath)) {
                    $pattern = $cssCachePath . '/' . md5($file) . '_*_'
                             . basename($file);
                    foreach (glob($pattern) as $cachedFile) {
                        if ($cachedFile != $destPath) unlink($cachedFile);
                    }
                    
                    $content = $this->_filter($file, $minify);
                    if (!file_put_contents($destPath, $content)) {
                        throw new Exception('Não foi possível copiar o arquivo:'
                                            . $file);
                    }
                }

                $this->view->headLink()->appendStylesheet($this->view->baseUrl(
                    "cache/layouts/{$env}/{$layout}/css/{$destFile}"));
                    
            }
        }

        if ($join) {
            $modTimeHash = md5(implode(',', $modTimes));
            $cssFile    = "{$layout}_{$modTimeHash}.css";

            if ($modTimes) {
                $cssDir  = PUBLIC_PATH . "/cache/layouts/{$env}/{$layout}/css";
                $cssPath = "$cssDir/$cssFile";

                // apaga os arquivos antigos
                foreach (glob("{$cssDir}/{$layout}_*.css") as $cachedFile) {
                    if ($cachedFile != $cssPath) unlink($cachedFile);
                }

                $content = '';
                $includedFiles = array();

                foreach ($validFiles as $file) {
                    $filepath = str_replace(APPLICATION_PATH,
                        'application', $file);
                    $content .= "\n\n/* {$filepath} */\n"
                              . $this->_filter($file, $minify);
                    $includedFiles[] = $filepath;
                }

                $content = "/*\nCSS Files included:\n"
                         . implode(PHP_EOL, $includedFiles) . "\n*/\n"
                         . $content;
                file_put_contents($cssPath, $content);
            }

            $this->view->headLink()->appendStylesheet($this->view->baseUrl(
                "cache/layouts/{$env}/{$layout}/css/{$cssFile}"));
        }

        return $this->view->headLink();
    }

    protected function _filter($filepath, $minify=true)
    {
        $css = file_get_contents($filepath);

        if (!strstr($filepath, APPLICATION_PATH)) {
            $relativeDir = '../../../../../' . dirname($filepath);
            $css = preg_replace('/url\(([\'"])?/i', "url($1$relativeDir/", $css);
        }

        if (!$minify) return $css;

        $css = trim($css);
        $css = str_replace("\r\n", "\n", $css);
        $search = array("/\/\*[\d\D]*?\*\/|\t+/", "/\s+/", "/\}\s+/");
        $replace = array(null, " ", "}\n");
        $css = preg_replace($search, $replace, $css);

        if (substr($filepath, -8) == '.min.css') return $css;

        $search = array("/\\;\s/", "/\s+\{\\s+/", "/\\:\s+\\#/", "/,\s+/i", "/\\:\s+\\\'/i", "/\\:\s+([0-9]+|[A-F]+)/i");
        $replace = array(";", "{", ":#", ",", ":\'", ":$1");
        $css = preg_replace($search, $replace, $css);
        $css = str_replace("\n", null, $css);
        return $css;
    }

}