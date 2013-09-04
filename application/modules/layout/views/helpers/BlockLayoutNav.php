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
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/Abstract.php';

class Content_View_Helper_BlockLayoutNav extends Zend_View_Helper_Abstract
{
    public function blockLayoutNav($block,
        Content_Model_Content $content = null, $user = null)
    {
        if (!$block instanceof Layout_Model_Block) {
            return;
        }

        // TODO Implementar o uso de cache para o navigation
        $navPages = FrontZend_Container::get('LayoutNav')->fetchAll(null,
                        array('id_parent', 'order ASC'));

        $navConfigs = $this->_getNavTree($navPages);
        $navigation = new Zend_Navigation($navConfigs);

        $role = Acl_Model_Auth::getRole();
        $this->view->navigation()->setRole($role);

        $container = $block->getOption('container');
        if ($container) {
            $navigation = $navigation->findOneById('layout_nav_' . $container);
        }

        // Define vars to be used on template
        $vars = array(
            'block'   => $block,
            'content' => $content,
            'user'    => $user,
            'nav'     => $navigation,
        );

        try {
            $template = $block instanceof Layout_Model_Block
                        ? $block->getOption('template')
                        : 'menu.phtml';
            $script = "blocks/layout-nav/{$template}";
            $xhtml = $this->view->partial($script, $vars);
        } catch(Zend_View_Exception $e) {
            // If defined template is not found, calls the default one
            $script = 'blocks/layout-nav/menu.phtml';
            $xhtml = $this->view->partial($script, $vars);
        }

        return $xhtml;
    }

    protected function _getNavTree($navPages, $parent=null)
    {
        $navConfigs = array();
        foreach($navPages as $navPage) {
            if ($navPage->id_parent == $parent) {
                $idNav = "layout_nav_{$navPage->id}";
                $navConfigs[$idNav] = array(
                    'id'       => $idNav,
                    'label'    => $navPage->label,
                    'uri'      => $navPage->uri,
                    'visible'  => (bool) $navPage->visible
                );
                if ($navPage->resource) {
                    $navConfigs[$idNav]['resource'] = $navPage->resource;
                }
                if ($navPage->attribs) {
                    $navPageAttribs = Zend_Json::decode($navPage->attribs);
                    foreach($navPageAttribs as $attr => $val) {
                        $navConfigs[$idNav][$attr] = $val;
                    }
                }
                $pages = $this->_getNavTree(clone $navPages, $navPage->id);
                if ($pages) {
                    $navConfigs[$idNav]['pages'] = $pages;
                }
            }
        }

        return $navConfigs;
    }
}
