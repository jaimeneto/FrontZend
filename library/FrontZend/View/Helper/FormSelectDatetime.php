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
 * Abstract class for extension
 */
require_once 'Zend/View/Helper/FormElement.php';


/**
 * Helper to generate a "datetime" element
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 */
class FrontZend_View_Helper_FormSelectDatetime 
        extends Zend_View_Helper_FormElement
{
    /**
     * Generates a 'date' element.
     *
     * @access public
     *
     * @param string|array $name If a string, the element name.  If an
     * array, all other parameters are ignored, and the array elements
     * are used in place of added parameters.
     *
     * @param mixed $value The element value.
     *
     * @param array $attribs Attributes for the element tag.
     *
     * @return string The element XHTML.
     */
    public function formSelectDatetime($name, $value = null, $attribs = null, 
            $listsep = null)
    {
        $info = $this->_getInfo($name, $value, $attribs);
        extract($info); // id, name, value, attribs, options, listsep, disable

        if(isset($attribs['format'])) {
            $format = $attribs['format'];
            unset($attribs['format']);
        }

        $values = array(
            'day'    => null,
            'month'  => null,
            'year'   => null,
            'hour'   => null,
            'minute' => null
        );
        if (isset($value) && isset($format) && $value && $format) {
            $date = new Zend_Date($value, $format);
            $values = array(
                'day'    => $date->get(Zend_Date::DAY),
                'month'  => $date->get(Zend_Date::MONTH),
                'year'   => $date->get(Zend_Date::YEAR),
                'hour'   => $date->get(Zend_Date::HOUR),
                'minute' => $date->get(Zend_Date::MINUTE)
            );
        }

        $selectAttribs = array('disable' => $disable);

        if ($listsep === null) {
            $listsep = ' / ' . PHP_EOL;
        }

        $days = array('' => 'Dia: ');
        for($d = 1; $d <= 31; $d++) {
            $day = ($d < 10) ? '0'.$d : $d;
            $days[$day] = $day;
        }

        $months = array('' => 'Mês: ');
        for($m = 1; $m<=12; $m++) {
            $date = new Zend_Date(array('month' => $m));
            $monthNumber = $date->get(Zend_Date::MONTH);
            $monthName = $date->get(Zend_Date::MONTH_NAME);
            $months[$monthNumber] = $monthName;
        }
        ksort($months);

        $now = Zend_Date::now();
        $maxYear = $now->get(Zend_Date::YEAR)+5;
        if(isset($attribs['max-year'])) {
            $maxYear = $attribs['max-year'];
            unset($attribs['max-year']);
        }

        $years = array('' => 'Ano: ');
        for($y = $maxYear; $y >= 1900; $y--) {
            $years[$y] = $y;
        }

        $hours = array('' => 'Hora: ');
        for($h = 0; $h <= 24; $h++) {
            $hour = ($h < 10) ? '0'.$h : $h;
            $hours[$hour] = $hour;
        }

        $minutes = array('' => 'Minuto: ');
        for($m = 0; $m <= 59; $m++) {
            $minute = ($m < 10) ? '0'.$m : $m;
            $minutes[$minute] = $minute;
        }

        unset($attribs['local']);
        
        if (isset($attribs['class'])) {
            $attribs['class'] = trim(str_replace('form-control', '', $attribs['class']));
            if(!$attribs['class']) {
                unset($attribs['class']);
            }
        }
        
        $xhtml = '<div '
               . $this->_htmlAttribs($attribs)
               . '>'
               . $this->view->formSelect($name.'[day]', $values['day'],
                    array_merge($selectAttribs, array('class' => 'form-control input-10')), $days)
               . $listsep
               . $this->view->formSelect($name.'[month]', $values['month'],
                    array_merge($selectAttribs, array('class' => 'form-control input-20')), $months)
               . $listsep
               . $this->view->formSelect($name.'[year]', $values['year'],
                    array_merge($selectAttribs, array('class' => 'form-control input-10')), $years);

        if (strstr($format, 'HH:mm')) {
            $xhtml .= '&nbsp;' . PHP_EOL .
                      $this->view->formSelect($name.'[hour]', $values['hour'],
                        array_merge($selectAttribs, array('class' => 'form-control input-10')), $hours)
                    . ' : ' . PHP_EOL . 
                      $this->view->formSelect($name.'[minute]', $values['minute'],
                         array_merge($selectAttribs, array('class' => 'form-control input-10')), $minutes);
        }

        $xhtml .= '</div>';

        return $xhtml;
    }
}
