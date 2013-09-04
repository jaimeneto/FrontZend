<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_View_Helper_JsHeadScripts extends Zend_View_Helper_Abstract
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
    public function jsHeadScripts($files, $layout=null, $env='frontend',
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

        $jsCachePath = PUBLIC_PATH . "/cache/layouts/{$env}/{$layout}/js";

        if (count($files) > 0 && !is_dir($jsCachePath)) {
            mkdir($jsCachePath, 0777, true);
        }

        foreach ($files as $i => $file) {
            if (!file_exists($file)) {
                throw new Exception("O arquivo de script não existe: $file");
            }

            if (!is_readable($file)) {
                throw new Exception('O arquivo de script não pode ser lido: '
                                    . $file);
            }

            if ($join) {
                $modTimes[] = filemtime($file);
                $validFiles[] = $file;
                continue;
            }
            else {

                if (!strstr($file, APPLICATION_PATH)) {
                    $this->view->headScript()->appendFile(
                        $this->view->baseUrl($file));
                    continue;
                }

                $destFile = md5($file) . '_' . filemtime($file)
                          . '_'. basename($file);
                $destPath = $jsCachePath . '/' . $destFile;

                if (!file_exists($destPath)) {
                    $pattern = $jsCachePath . '/' . md5($file) . '_*_'
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

                $this->view->headScript()->appendFile($this->view->baseUrl(
                    "cache/layouts/{$env}/{$layout}/js/{$destFile}"));
                    
            }
        }

        if ($join) {
            $modTimeHash = md5(implode(',', $modTimes));
            $jsFile    = "{$layout}_{$modTimeHash}.js";

            if ($modTimes) {
                $jsDir  = PUBLIC_PATH . "/cache/layouts/{$env}/{$layout}/js";
                $jsPath = "$jsDir/$jsFile";

                // apaga os arquivos antigos
                foreach (glob("{$jsDir}/{$layout}_*.js") as $cachedFile) {
                    if ($cachedFile != $jsPath) unlink($cachedFile);
                }

                $content = PHP_EOL
                         . 'var baseUrl="' . $this->view->baseUrl() . '",'
                         . 'adminBaseUrl="' . $this->view->adminBaseUrl() . '",'
                         . 'siteUrl="' . SITE_URL . '";';
                $includedFiles = array();

                foreach ($validFiles as $file) {
                    $filepath = str_replace(APPLICATION_PATH,
                        'application', $file);
                    $content .= "\n\n/* {$filepath} */\n"
                              . $this->_filter($file, $minify);
                    $includedFiles[] = $filepath;
                }

                $content = "/*\njs Files included:\n"
                         . implode(PHP_EOL, $includedFiles) . "\n*/\n"
                         . $content;
                file_put_contents($jsPath, $content);
            }

            $this->view->headScript()->appendFile($this->view->baseUrl(
                "cache/layouts/{$env}/{$layout}/js/{$jsFile}"));
        } else {
            $this->view->headScript()->prependScript(
                     'var baseUrl="' . $this->view->baseUrl() . '",'
                     . 'siteUrl="' . SITE_URL . '";' . PHP_EOL
            );
        }

        return $this->view->headScript();
    }

    protected function _filter($filepath, $minify=true)
    {
        $js = file_get_contents($filepath);        

        if ($minify && substr($filepath, -7) != '.min.js') {
            require_once 'JsMin.php';
            $js = JsMin::minify($js);
        }

        return $js;
    }

}