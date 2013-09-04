<?php
/**
 * Decorator definition for the submit buttons
 *
 * @category Forms
 * @package Twitter_Bootstrap_Form
 * @subpackage Decorator
 * @author Christian Soronellas <csoronellas@emagister.com>
 */

/**
 * A decorator to render the submit form buttons
 *
 * @category Forms
 * @package Twitter_Bootstrap_Form
 * @subpackage Decorator
 * @author Christian Soronellas <csoronellas@emagister.com>
 */
class Twitter_Bootstrap_Form_Decorator_Actions extends Zend_Form_Decorator_Abstract
{
    /**
     * Character encoding to use when escaping attributes
     * @var string
     */
    protected $_encoding;

    /**
     * Convert options to tag attributes
     *
     * @return string
     */
    protected function _htmlAttribs()
    {
        $attribs = (array) $this->getOptions();
        
        $attribs['class'] = (isset($attribs['class']))
            ? $attribs['class'] . ' form-actions'
            : 'form-actions';

        $xhtml = '';
        $enc   = $this->_getEncoding();
        foreach ($attribs as $key => $val) {
            $key = htmlspecialchars($key, ENT_COMPAT, $enc);
            if (is_array($val)) {
                if (array_key_exists('callback', $val)
                    && is_callable($val['callback'])
                ) {
                    $val = call_user_func($val['callback'], $this);
                } else {
                    $val = implode(' ', $val);
                }
            }
            $val    = htmlspecialchars($val, ENT_COMPAT, $enc);
            $xhtml .= " $key=\"$val\"";
        }
        return $xhtml;
    }

    /**
     * Render all the buttons
     *
     * @return string
     */
    public function buildButtons()
    {
        $output = '';
        foreach ($this->getElement() as $element) {
            if ($element !== null && $element instanceof Zend_Form_Element) {
                $element->setDecorators(array(array('ViewHelper')));
                $output .= $element->render();
            }
        }

        return $output;
    }

    /**
     * Renders the content
     *
     * @param  string $content
     * @return string
     */
    public function render($content)
    {
        return '<div ' . $this->_htmlAttribs() . '>'
                    . $this->buildButtons() . '</div>';
    }

    /**
     * Get encoding for use with htmlspecialchars()
     *
     * @return string
     */
    protected function _getEncoding()
    {
        if (null !== $this->_encoding) {
            return $this->_encoding;
        }

        if (null === ($element = $this->getElement())) {
            $this->_encoding = 'UTF-8';
        } elseif (null === ($view = $element->getView())) {
            $this->_encoding = 'UTF-8';
        } elseif (!$view instanceof Zend_View_Abstract
            && !method_exists($view, 'getEncoding')
        ) {
            $this->_encoding = 'UTF-8';
        } else {
            $this->_encoding = $view->getEncoding();
        }
        return $this->_encoding;
    }
}
