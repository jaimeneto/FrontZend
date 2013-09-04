<?php

/**
 * @see Zend_View_Helper_Navigation_Menu
 */
require_once 'Zend/View/Helper/Navigation/Menu.php';

class Twitter_Bootstrap_View_Helper_Menu
    extends Zend_View_Helper_Navigation_Menu
{
    protected $_tagAttribs = array(
        'class',
        'title',
        'target',
        'accesskey',
        'rel',
        'rev'
    );

    public function renderNavbar(Zend_Navigation_Container $container = null,
        array $options = array())
    {
        if (null === $container) {
            $container = $this->getContainer();
        }

        return isset($options['vertical']) && $options['vertical']
            ? $this->_vertical($container, $options)
            : $this->_horizontal($container, $options);

    }

    private function _horizontal($container = null, $options = array())
    {
        $html = array('<ul class="nav">');

        $acl = Zend_Registry::isRegistered('Zend_Acl')
            ? Zend_Registry::get('Zend_Acl')
            : null;
        
        $role = Zend_Auth::getInstance()->hasIdentity()
            ? Zend_Auth::getInstance()->getIdentity()->id_role
            : 'guest';

        foreach ($container as $page) {

            // visibility of the page
            if (!$page->isVisible()
                || ($acl && !$acl->isAllowed($role, $page->getResource()))) {
                continue;
            }

            // dropdown
            $dropdown = !empty($page->pages);

            $attribs = array(
                'href="' . ($dropdown ? '#' : $page->getHref()) . '"'
            );
            foreach($this->_tagAttribs as $attr) {
                if ($attr == 'class') {
                    $classes=array();
                    if ($dropdown) {
                        $classes[] = 'dropdown-toggle';
                        $attribs[] = 'data-toggle="dropdown"';
                    }
                    if ($page->class) {
                        $classes[] = $page->class;
                    }
                    $attribs[] = $classes 
                               ? 'class="' . implode(' ', $classes) . '"'
                               : '';
                } else if ($page->$attr) {
                    $attribs[] = $attr . '="' . $page->$attr . '"';
                }
            }

            // header
            $html[] = '<li' . ($dropdown ? ' class="dropdown"' : '') . '>';
            $html[] = '<a ' . implode(' ', $attribs) . '>';
            $html[] = $page->getLabel();

            if ($dropdown) {
                $html[] = '<b class="caret"></b>';
            }

            $html[] = '</a>';

            if (!$dropdown) {
                $html[] = '</li>';
                continue;
            }

            $html[] = '<ul class="dropdown-menu">';

            foreach ($page->pages as $subpage) {
                // visibility of the sub-page
                if (!$subpage->isVisible()
                    || ($acl && !$acl->isAllowed($role, $subpage->getResource()))) {
                    continue;
                }
                if ($subpage->getLabel() == 'divider') {
                    $html[] = '<li class="divider"></li>';
                    continue;
                }
                $html[] = '<li' . ($subpage->isActive() ? ' class="active"' : '') . '>';
                $html[] = '<a href="' . $subpage->getHref() . '">';

                if ($subpage->get('icon')) {
                    $html[] = '<i class="icon-' . $subpage->get('icon') . '"></i>';
                }

                $html[] = $subpage->getLabel();
                $html[] = "</a>";
                $html[] = "</li>";
            }

            $html[] = "</ul>";
            $html[] = "</li>";
        }

        $html[] = '</ul>';

        $html = join(PHP_EOL, $html);

        return $html;
    }

    private function _vertical($container = null, $options = array())
    {
        $html = array('<ul class="nav nav-list">');

        $acl = Zend_Registry::isRegistered('Zend_Acl')
            ? Zend_Registry::get('Zend_Acl')
            : null;
        
        $role = Zend_Auth::getInstance()->hasIdentity()
            ? Zend_Auth::getInstance()->getIdentity()->id_role
            : 'guest';

        foreach ($container as $page) {
            // show only the current branch and the visible item
            if (!$page->isVisible()
                || ($acl && !$acl->isAllowed($role, $page->getResource()))
                || ($this->menu()->getOnlyActiveBranch() && !$page->isActive(true))) {
                continue;
            }

            // header
            $html[] = '<li class="nav-header">';
            $html[] = $page->getLabel();
            $html[] = "</li>";

            if (!empty($page->pages)) {
                foreach ($page->pages as $subpage) {
                    // visibility of the sub-page
                    if (!$subpage->isVisible()
                        || ($acl && !$acl->isAllowed($subpage->getResource()))) {
                        continue;
                    }
                if ($subpage->getLabel() == 'divider') {
                    $html[] = '<li class="divider"></li>';
                    continue;
                }
                    $html[] = '<li' . ($subpage->isActive() ? ' class="active"' : '') . '>';
                    $html[] = '<a href="' . $subpage->getHref() . '">';

                    if ($subpage->get('icon')) {
                        $html[] = '<i class="icon-' . $subpage->get('icon') . '"></i>';
                    }

                    $html[] = $subpage->getLabel();
                    $html[] = "</a>";
                    $html[] = "</li>";
                }
            }
        }

        $html[] = '</ul>';

        $html = join(PHP_EOL, $html);

        return $html;
    }

}
