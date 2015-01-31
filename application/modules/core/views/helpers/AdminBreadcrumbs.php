<?php
/**
 * FrontZend CMS
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

/**
 * @see Bootstrap_View_Helper_Breadcrumbs
 */
require_once 'Bootstrap/View/Helper/Breadcrumbs.php';

/**
 * Helper for printing breadcrumbs
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 * @author Jaime Neto <contato@jaimeneto.com>
 */
class Core_View_Helper_AdminBreadcrumbs
    extends Bootstrap_View_Helper_Breadcrumbs
{

    public function adminBreadcrumbs(Zend_Navigation_Container $container = null)
    {
        if (null !== $container) {
            $this->setContainer($container);
        }

        return $this;
    }
    
    public function findActive(Zend_Navigation_Container $container,
                               $minDepth = null,
                               $maxDepth = -1)
    {
        $found  = null;
        $foundDepth = -1;
        
        foreach ($container->findById('home')->getPages() as $page) {
            foreach ($page->getPages() as $subpage) {
                if ($subpage->isActive()) {
                    $found = $subpage;
                }
            }
        }

        if ($found) {
            return array('page' => $found, 'depth' => $foundDepth);
        } else {
            return array();
        }
    }
    
    public function htmlify(Zend_Navigation_Page $page)
    {
        // get label and title for translating
        $label = $page->getLabel();
        $title = $page->getTitle();

        if ($this->getUseTranslator() && $t = $this->getTranslator()) {
            if (is_string($label) && !empty($label)) {
                $label = $t->translate($label);
            }
            if (is_string($title) && !empty($title)) {
                $title = $t->translate($title);
            }
        }

        // get attribs for anchor element
        $attribs = array_merge(
            array(
                'id'     => $page->getId(),
                'title'  => $title,
                'class'  => $page->getClass(),
                'href'   => $this->view->baseUrl($page->getHref()),
                'target' => $page->getTarget()
            ),
            $page->getCustomHtmlAttribs()
        );

        return '<a' . $this->_htmlAttribs($attribs) . '>'
             . $this->view->escape($label)
             . '</a>';
    }
    
}
