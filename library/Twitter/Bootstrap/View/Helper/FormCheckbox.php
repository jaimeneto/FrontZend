<?php
/**
 * View helper definition
 *
 * @category Forms
 * @package Twitter_Bootstrap_View
 * @subpackage Helper
 * @author Christian Soronellas <csoronellas@emagister.com>
 */

/**
 * Helper to generate a "checkbox" element with the Twitter's Bootstrap UI.
 *
 * @category Forms
 * @package Twitter_Bootstrap_View
 * @subpackage Helper
 * @author Jaime Neto <contato@jaimeneto.com>
 */
class Twitter_Bootstrap_View_Helper_FormCheckbox extends Zend_View_Helper_FormCheckbox
{

    public function formCheckbox($name, $value = null, $attribs = null, array $checkedOptions = null)
    {
        $inputLabel = isset($attribs['input_label'])
                ? $attribs['input_label']
                : null;

        if (is_array($attribs)) {
            unset($attribs['input_label']);
        }

        $xhtml = parent::formCheckbox($name, $value, $attribs, $checkedOptions);

        if (!isset($attribs['escape']) || $attribs['escape']) {
            $inputLabel = $this->view->escape($inputLabel);
        }

        // Wraps the checkbox with its own label (not the decorator one),
        // if the attrib input_label is added
        if ($inputLabel) {
            $xhtml = '<label class="checkbox"'
                   . ' for="' . $this->view->escape($name) . '">'
                   . $xhtml
                   . $inputLabel
                   . '</label>';
        }

        return $xhtml;
    }
}
