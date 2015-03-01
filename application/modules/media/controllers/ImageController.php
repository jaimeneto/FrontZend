<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Controller
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_ImageController extends Zend_Controller_Action
{
    public function init()
    {
        $module = $this->_request->getModuleName();
        $controller = $this->_request->getControllerName();
        $action = $this->_request->getActionName();

        $this->_helper->acl("{$module}_{$controller}_{$action}");
    }
    
    public function indexAction()
    {
        $this->_forward('manage');
    }    

    public function manageAction()
    {
        $path = str_replace('|', '/', $this->_getParam('path', 'images'));

        $basepath = Media_Model_File::getFullPath();
        $directoryTree = current($this->_directoryTree($basepath, $path));
        $dirs = new Zend_Navigation($directoryTree['pages']);

        $files = FrontZend_Container::get('File')->fetchFromPath($path);

        $form = new Media_Form_Directory();
        $form->setAction($this->view->adminBaseUrl('media/image/create-dir'));
        $form->populate(array('path' => $path));
        
        $this->view->dirs = $dirs;
        $this->view->files = $files;
        $this->view->path = $path;
        $this->view->form = $form;
    }   

    public function editAction()
    {
        $id = $this->_getParam('id');

        if (!$id) {
            $this->getHelper('alerts')->addError('Id inválido');
            $this->getHelper('Redirector')->gotoUrlAndExit(
                    ADMIN_ROUTE . "/media/image/manage/target/{$target}");
        }

        $file = FrontZend_Container::get('File')->findById($id);

        $basePath = '';
        if ($file) {
            $basePath = explode('/', $file->path);
            array_pop($basePath);
            $basePath = '/path/' . implode('|', $basePath);
        }
        
        if ($this->_getParam('cancel')) {
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE 
                         . "/media/image/manage{$basePath}");
        }
        
        if (!$file) {
            $this->getHelper('alerts')->addError('Imagem inválido');
            $this->getHelper('Redirector')
                 ->gotoUrlAndExit(ADMIN_ROUTE . '/media/image');
        }

        $form = new Media_Form_Image(array('edit' => true));
        $form->setAction($this->view->url());
        $form->populate($file->toArray());

        if ($this->_request->isPost()) {
            $newFileName = Zend_Date::now()->get('yyyyMMddHHmmss');
            $path = $file->getRealPath();
            $file->setFilename($newFileName);
            if (rename(str_replace('.', '_tmp.', $path), $file->getRealPath())){
                try {
                    if ($this->_request->getPost('save_new')) {
                        $now = Zend_Date::now()->get('yyyy-MM-dd HH:mm:ss');
                        $id_user = Zend_Auth::getInstance()->getIdentity()
                                                           ->id_user;
                        $newFile = FrontZend_Container::get('File')->createRow(
                            array(
                                'path'          => $file->path,
                                'type'          => 'img',
                                'file'          => $newFileName,
                                'original_name' => $file->original_name,
                                'credits'       => $file->credits,
                                'info'          => $file->info,
                                'keywords'      => $file->keywords,
                                'id_original'   => $file->id_original 
                                                        ? $file->id_original 
                                                        : $file->id_file,
                                'dt_created'    => $now,
                                'dt_updated'    => $now,
                                'id_user'       => $id_user
                            ));
                        $newId = FrontZend_Container::get('File')
                                ->save($newFile);
                        
                        $this->getHelper('alerts')
                                ->addSuccess('Nova imagem criada com sucesso');
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . '/media/image/edit/id/' . $newId);
                    }
                    
                    FrontZend_Container::get('File')->save($file);
                    unlink($path);
                    $this->getHelper('alerts')
                            ->addSuccess('Imagem alterada com sucesso');
                    
                    if ($this->_request->getPost('save')) {
                        $this->getHelper('Redirector')->gotoUrlAndExit(
                            ADMIN_ROUTE . "/media/image/manage{$basePath}");
                    }
                } catch(Exception $e) {
                    $this->getHelper('alerts')
                            ->addError('Erro ao tentar salvar image');
                    rename($file->getRealPath(), $path);
                }
            } else {
                $this->getHelper('alerts')
                        ->addError('Erro ao tentar renomear o arquivo');
            }
        }
        
        $this->view->form = $form;
        $this->view->file = $file;
    }
    
    public function createDirAction()
    {
        $post = $this->getRequest()->getPost();
        
        $form = new Media_Form_Directory();
        if ($form->isValid($post)) {
            try {
                $form->persistData();
                $this->getHelper('alerts')
                        ->addSuccess('Pasta criada com sucesso');
            } catch(Exception $e) {
                $this->getHelper('alerts')
                        ->addError('Erro ao tentar criar pasta');
                $this->getHelper('alerts')
                        ->addError($e->getMessage());
            }
        }
        $basePath = '/path/' . str_replace('/', '|', $post['path']);
        $this->getHelper('Redirector')->gotoUrlAndExit(
                ADMIN_ROUTE . "/media/image/manage{$basePath}");
    }
    
    public function removeDirAction()
    {
        $path = $this->_getParam('path');
        
        $dir = str_replace('|', '/', $path);
        
        if (FrontZend_Container::get('File')->removeDir($dir)) {
            $this->getHelper('alerts')
                        ->addSuccess('Pasta excluída com sucesso');
        }
        
        $redirectPath = substr($path, 0, strrpos($path, '|'));        
        $this->getHelper('Redirector')->gotoUrlAndExit(
                ADMIN_ROUTE . "/media/image/manage/path/{$redirectPath}");
    }
    
    private function _directoryTree($path, $currentPath=null)
    {
        $di = new DirectoryIterator($path);
        $dirs = array();
        foreach($di as $dir) {
            if($dir->isDir() && !$dir->isDot() 
                    && substr($dir->getFilename(),0,1) != '.') {
                $dirPath = str_replace(
                    Media_Model_File::getFullPath() . DIRECTORY_SEPARATOR, '',
                    $dir->getPathname()
                );

                $uri = $this->view->url(array(
                    'action' => 'manage',
                    'path'   => str_replace(DIRECTORY_SEPARATOR, '|', $dirPath),
                    'target' => $this->view->target
                ));

                $dirs[$dir->getFilename()] = array(
                    'label'  => $dir->getFilename(),
                    'uri'    => $uri,
                    'active' => $dirPath == 
                        str_replace('/', DIRECTORY_SEPARATOR, $currentPath),
                    'pages'  => 
                        $this->_directoryTree($dir->getPathname(), $currentPath)
                );
            }
        }
        ksort($dirs);
        return $dirs;
    }

    /////////////////////////////////// AJAX ///////////////////////////////////

    public function ajaxModifyAction()
    {
        $data = $this->_request->getPost();

        if (!isset($data['id_file']) || !$data['id_file']) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }

        $file = FrontZend_Container::get('File')->findById($data['id_file']);

        if (!$file) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Arquivo inválido'
            ));
        }

        $realPath = $file->getRealPath();
        $tmpRealPath = 
                substr($realPath, 0, -4) . '_tmp' . substr($realPath, -4);

        $debug = array();

        $image = new Pic();
        $image->open($realPath);
        
        if (isset($data['modify']['resize'])) {
            $args = $data['modify']['resize'];
            if ($args['width'] || $args['height']) {
                $option = $args['option'];
                unset($args['option']);

                foreach($args as $arg => $val) {
                    if (!$val) unset($args[$arg]);
                }

                switch($option) {
                    case 'auto':
                        $args['overflow'] = 'visible';
                        
                        if (!isset($args['width'])) {
                            $args['width'] = $image->img['width']
                                           * $args['height']
                                           / $image->img['height'];
                        }
                        if (!isset($args['height'])) {
                            $args['height'] = $image->img['height']
                                            * $args['width']
                                            / $image->img['width'];
                        }
                        
                        $image->photo($args);
                        break;
                    case 'distort':
                        $image->resize($args);
                        break;
                    case 'avatar':
                        $args['left'] = 'auto';
                        $image->thumbnail($args);
                        break;
                    case 'fill':
                        if ((!isset($args['width']) || !$args['width']) &&
                            (!isset($args['height']) || !$args['height'])) {
                            break;
                        }
                        if (!isset($args['width']) || !$args['width']) {
                            $args['width'] = $args['height'];
                        }
                        if (!isset($args['height']) || !$args['height']) {
                            $args['height'] = $args['width'];
                        }

                        $image->resize($args);

                        $layer = new Pic();
                        $layer->open($realPath);
                        $args['overflow'] = 'visible';
                        $layer->photo($args);

                        $top = $args['height'] > $layer->img['height']
                            ? ($args['height'] - $layer->img['height']) / 2
                            : 0;

                        $left = $args['width'] > $layer->img['width']
                            ? ($args['width'] - $layer->img['width']) / 2
                            : 0;

                        $image->geometric('rectangle', array(
                            'height'     => $args['width'],
                            'width'      => $args['height'],
                            'top'        => '0',
                            'left'       => '0',
                            'background' => '#FFF',
                            'opacity'    => '100'
                        ));
                        $image->layer($layer->img, array(
                            'left'    => $left,
                            'top'     => $top,
                            'opacity' => '100'
                        ));
                        break;
                }

                $image->save($tmpRealPath);
            }
            unset($data['modify']['resize']);
        }
        
        if (isset($data['modify'])) {
            foreach($data['modify'] as $method => $args) {
                if (is_array($args)) {
                    foreach($args as $a => $v) {
                        if ($v) {
                            if ($method == 'filter') {
                                if ($a == 'colorize') {
                                    $image->$method($a, $v['red'], $v['green'], 
                                                    $v['blue'], $v['alpha']);
                                } else {
                                    $image->$method($a, $v);
                                }
                            } else {
                                $image->$method($a);
                            }
                        }
                    }
                } else if ($method == 'write') {
                    if ($args && $data['credits']) {
                        $image->write($data['credits'], array(
                            'bottom' => '5px',
                            'right'  => '5px',
                            'font'   => 'fonts/arial.ttf',
                            'size'   => '11px',
                            'color'  => '#FFF'
                        ));
                    }
                } else {
                    $image->$method($args);
                }
            }
        }
        $image->save($tmpRealPath);
        $image->clear();

        $tmpPath = $file->getPath();
        $tmpPath = substr($tmpPath, 0, -4) . '_tmp' . substr($tmpPath, -4);
        
        $imageSize = getimagesize($tmpRealPath);
        
        $this->_helper->json(array(
            'status' => 1,
            'src'    => $this->view->baseUrl($tmpPath),
            'width'  => $imageSize[0],
            'height' => $imageSize[1]
        ));

    }
    
    /**
     * Upload de imagens utilizando Valums File Uploader
     *
     * @return Json
     */
    public function ajaxUploadAction()
    {
        $path = trim($this->_getParam('path', 'temp'), '/');
        $template = $this->_getParam('template', 'files-manager');
        $target = $this->_getParam('target', 'images');

        $allowedExtensions = array('jpg', 'gif', 'png');
        // max file size in bytes
        $sizeLimit = 10 * 1024 * 1024;

        require_once PUBLIC_PATH . '/lib/valums-file-uploader/server/php.php';
        $uploader = new qqFileUploader($allowedExtensions, $sizeLimit);

        if (!$path) {
            $this->_helper->json(array(
                'error' => 'O caminho para envio do arquivo não foi definido'
            ));
        };
        $uploadDir = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, 
                PUBLIC_PATH . "/files/{$path}/");

        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Call handleUpload() with the name of the folder,
        // relative to PHP's getcwd()
        $result = $uploader->handleUpload($uploadDir);

        if (isset($result['error'])) {
            $this->_helper->json($result);
        }

        $file = FrontZend_Container::get('File')->createRow();

        $originalName = $uploader->getUploadName();
        $uploadedFilePath = $uploadDir . DIRECTORY_SEPARATOR . $originalName;
        $extension = pathinfo($originalName, PATHINFO_EXTENSION);
        $filename = Zend_Date::now()->get('yyyyMMddHHmmss') . '.' . $extension;
        $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

        while(file_exists($filepath)) {
            $filename = Zend_Date::now()->get('yyyyMMddHHmmss');
            $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;
        }

        if (!rename($uploadedFilePath, $filepath)) {
            unlink($uploadedFilePath);
            $this->_helper->json(array(
                'error'   => 'Erro ao tentar renomear arquivo'
                           . ' - uploadDir: ' . $uploadDir
                           . ' - uploadedFilePath: ' . $uploadedFilePath
                           . " - filepath: {$filepath}"
            ));
        }

        try {
            $now = Zend_Date::now()->get('yyyy-MM-dd hh:mm:ss');
            $id_user = Zend_Auth::getInstance()->getIdentity()->id_user;
            $path = str_replace('\\', '/', "{$path}/{$filename}");
            $file->setFromArray(array(
                'path'          => $path,
                'type'          => 'img',
                'file'          => $filename,
                'original_name' => $originalName,
                'credits'       => '',
                'info'          => '',
                'keywords'      => '',
                'dt_created'    => $now,
                'dt_updated'    => $now,
                'id_user'       => $id_user
            ));
            $id_file = $file->save();

        } catch(Exception $e) {
            $this->_helper->json(array(
                'error'   => $e->getMessage()
            ));
        }

        $this->view->file     = $file;
        $this->view->target   = $target;

        $this->_helper->json(array(
            'success' => $result['success'],
            'id'      => $file->id_file,
            'content' => 
                $this->view->render("image/ajax-upload/{$template}.phtml")
        ));
    }

    public function ajaxManageAction()
    {
        $path = str_replace('|', '/', $this->_getParam('path', 'images'));

        $basepath = Media_Model_File::getFullPath();
        $directoryTree = current($this->_directoryTree($basepath, $path));
        $dirs = new Zend_Navigation($directoryTree['pages']);

        $files = FrontZend_Container::get('File')->fetchFromPath($path);

        $this->view->dirs     = $dirs;
        $this->view->files    = $files;

        $this->_helper->json(array(
            'status'  => 1,
            'content' => $this->view->render('image/ajax-manage.phtml')
        ));
    }

    public function ajaxRemoveAction()
    {
        $id = $this->_getParam('id');
        
        if (!$id) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Id inválido'
            ));
        }
        
        if (!FrontZend_Container::get('File')->deleteById($id)) {
            $this->_helper->json(array(
                'status' => 0,
                'msg'    => 'Não foi possível excluir a imagem'
            ));
        }
        $this->_helper->json(array(
            'status'  => 1
        ));
    }
    
}
