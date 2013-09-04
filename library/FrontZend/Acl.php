<?php

/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_Acl
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * Classe para definição de permissões de usuários
 *
 * @category   FrontZend
 * @package    FrontZend_Acl
 * @author Jaime Neto
 */
class FrontZend_Acl extends Zend_Acl
{
    public function getRoleParents($role, $onlyParents=false)
    {
        $parents = array();
        foreach($this->getRoles() as $inherit) {
            if($this->inheritsRole($role, $inherit, $onlyParents)) {
                $parents[] = $inherit;
            }
        }
        return $parents;
    }

    public function getResourceParents($resource, $onlyParents=false)
    {
        $parents = array();
        foreach($this->getResources() as $inherit) {
            if($this->inherits($resource, $inherit, $onlyParents)) {
                $parents[] = $inherit;
            }
        }
        return $parents;
    }

    public function getResourcesTree($resource=null)
    {
        $tree = array();
        $resources = $this->getResources();
        foreach($this->getResources() as $r) {
            $parents = $this->getResourceParents($r, true);
            if (!$resource && count($parents) == 0 || in_array($resource, $parents)) {
                $tree[$r] = $this->getResourcesTree($r);
            }
        }
        return $tree;
    }

}