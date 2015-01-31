<?php

/**
 * FrontZend CMS
 *
 * @category   Module
 * @package    Module_View
 * @subpackage Helper
 * @copyright  Copyright (c) 2013 (http://frontzend.jaimeneto.com)
 */

class Layout_View_Helper_ShowBlock extends Zend_View_Helper_Abstract
{
    public function showBlock($block, $content = null, $user = null)
    {
        if ($block->visible) {
            $filterDashToCamelCase = new Zend_Filter_Word_DashToCamelCase();
            $helperName = 'block' . ucfirst($filterDashToCamelCase->filter($block->block));

            $this->view->pageNumber = Zend_Controller_Front::getInstance()->getRequest()->getParam('p',1);

            // call the view helper if exists
            if ($this->_helperExists($helperName)) {
                $xhtml = $this->view->$helperName($block, $content, $user);

            // use the template directly if view helper does not exist
            } else {
                // define template, is case block has a specific one
                $template = $block->getOption('template');
                $script = $template && $template != "{$block->block}.phtml"
                    ? "blocks/{$block->block}/{$block->getOption('template')}"
                    : "blocks/{$block->block}.phtml";

                $xhtml = $this->view->partial($script, array(
                    'block'   => $block,
                    'content' => $content,
                    'user'    => $user
                ));
            }

            return $xhtml;
        }
    }

    /**
     * Verify if a view helper exists
     *
     * @param string $name
     * @return boolean
     */
    protected function _helperExists($name)
    {
        return (bool) $this->view->getPluginLoader('helper')->load($name, false);
    }

}
