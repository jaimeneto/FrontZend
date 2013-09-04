<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_Model
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Media_Model_Youtube
{
    const ID_LENGTH = 11;
    
    /**
     * Encontra o id de video do youtube de uma string.
     * A string pode ser uma url do video ou o próprio código
     *
     * @param string $url
     * @return string Id do youtube
     */
    static public function getIdFromUrl($url)
    {
        $youtubeId = null;
        if (strlen($url) == self::ID_LENGTH) {
            $youtubeId = $url;
        } else {
            $idStarts = strpos($url, "?v=");
            if ($idStarts === false) {
                $idStarts = strpos($url, "&v=");
            }
            if ($idStarts === false) {
                $idStarts = strpos($url, "/v/");
            }
            if ($idStarts) {
                $idStarts += 3;
                $youtubeId = substr($url, $idStarts, self::ID_LENGTH);
            }
        }

        if ($youtubeId === null || strlen($youtubeId) != self::ID_LENGTH) {
            throw new Exception('Vídeo do youtube inválido');
        }

        return $youtubeId;
    }
    
    static public function search($term=null, $count=null, $order='viewCount', $index=1)
    {
        $yt = new Zend_Gdata_YouTube();
        $query = $yt->newVideoQuery();
        $query->videoQuery = $term;
        $query->startIndex = $index;
        $query->maxResults = $count;
        $query->orderBy    = $order;
        
        return $yt->getVideoFeed($query);
    }
    
}