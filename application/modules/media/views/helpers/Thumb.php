<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Helper que gera um thumbnail de um imagem.
 *
 * @author Jaime Neto
 */
class Media_View_Helper_Thumb extends Zend_View_Helper_Abstract
{
    public function thumb(Media_Model_File $image, $width=null, $height=null,
        $fill=false, $forceNew=false)
    {
        $imagePath = $image->getRealPath();

        if (!$width) $width = null;
        if (!$height) $height = null;
        
        if (!file_exists($imagePath) || (!$width && !$height)) {
            return;
        }

        $thumbRealPath = $image->getThumbRealPath($width, $height, $fill);

        if ($forceNew && file_exists($thumbRealPath)) {
            $this->_deleteThumb($thumbRealPath);
        }

        if (!file_exists($thumbRealPath)) {
            $this->_createThumb($imagePath, $thumbRealPath, $width, $height, $fill);
        }

        $thumbPath = $image->getThumbPath($width, $height, $fill);
        return $this->view->baseUrl($thumbPath);
    }

    private function _deleteThumb($thumbRealPath)
    {
        $thumb = new Pic();
        $thumb->open($thumbRealPath);
        $thumb->delete();
    }

    private function _createThumb($imagePath, $thumbRealPath, $width=null, $height=null, $fill=false)
    {
        $thumb = new Pic();
        $thumb->open($imagePath);
        $thumb->resize(array(
            'width'    => $width,
            'height'   => $height,
            'overflow' => 'visible'
        ));

        if ($fill) {
            $this->_fillThumb($thumb, $imagePath, $thumbRealPath, $width, $height, $fill);
        } else {
            $thumb->save($thumbRealPath);
            $thumb->clear();
        }
    }

    private function _fillThumb($thumb, $imagePath, $thumbRealPath, $width=null, $height=null, $fill=null)
    {
        $layer = new Pic();
        $layer->open($imagePath);
        $layer->photo(array(
            'width'  => $width,
            'height' => $height
        ));

        $top = $height > $thumb->img['height']
            ? ($height - $thumb->img['height']) / 2
            : 0;

        $left = $width > $thumb->img['width']
            ? ($width - $thumb->img['width']) / 2
            : 0;

        $thumb->geometric('rectangle', array(
            'height'     => $width,
            'width'      => $height,
            'top'        => '0',
            'left'       => '0',
            'background' => is_string($fill) && !is_numeric($fill) 
                                ? $fill : '#FFFFFF',
            'opacity'    => '100'
        ));
        $thumb->layer($layer->img, array(
            'left'    => $left,
            'top'     => $top,
            'opacity' => '100'
        ));
        $thumb->save($thumbRealPath);
        $thumb->clear();
        $layer->clear();
    }

}
