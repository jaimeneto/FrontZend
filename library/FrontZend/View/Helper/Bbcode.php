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
require_once 'Zend/View/Helper/Abstract.php';


/**
 * Converts a bbcode string to html
 *
 * @category   FrontZend
 * @package    FrontZend_View
 * @subpackage Helper
 */
class FrontZend_View_Helper_Bbcode extends Zend_View_Helper_Abstract
{
    /**
     * Filters the provided string.
     *
     * @param  string $str
     * @return string
     * @throws Zend_View_Exception
     */
    public function bbcode($str, $escape=true)
    {
        // Convert all special HTML characters into entities to display literally
        if ($escape) {
            $str = $this->view->escape($str);
        }

        // The array of regex patterns to look for
        $format_search =  array(
            '#\[b\](.*?)\[/b\]#is', // Bold ([b]text[/b]
            '#\[i\](.*?)\[/i\]#is', // Italics ([i]text[/i]
            '#\[u\](.*?)\[/u\]#is', // Underline ([u]text[/u])
            '#\[s\](.*?)\[/s\]#is', // Strikethrough ([s]text[/s])
            '#\[quote\](.*?)\[/quote\]#is', // Quote ([quote]text[/quote])
            '#\[code\](.*?)\[/code\]#is', // Monospaced code [code]text[/code])
            '#\[size=([1-9]|1[0-9]|20)\](.*?)\[/size\]#is', // Font size 1-20px [size=20]text[/size])
            '#\[color=\#?([A-F0-9]{3}|[A-F0-9]{6})\](.*?)\[/color\]#is', // Font color ([color=#00F]text[/color])
            '#\[url=((?:ftp|https?)://.*?)\](.*?)\[/url\]#i', // Hyperlink with descriptive text ([url=http://url]text[/url])
            '#\[url\]((?:ftp|https?)://.*?)\[/url\]#i', // Hyperlink ([url]http://url[/url])
            '#\[img\](https?://.*?\.(?:jpg|jpeg|gif|png|bmp))\[/img\]#i', // Image ([img]http://url_to_image[/img])
            '#\[li\](.*?)\[/li\]#is', // list item ([li]text[/li]
            '#\[ul\](.*?)\[/ul\]#is', // Unordered list ([ul]text[/ul]
            '#\[ol\](.*?)\[/ol\]#is', // Ordered list ([ol]text[/ol]
            '#\[lt\](.*?)\[/lt\]#is',
        );

        // The matching array of strings to replace matches with
        $format_replace = array(
            '<strong>$1</strong>',
            '<em>$1</em>',
            '<span style="text-decoration: underline;">$1</span>',
            '<span style="text-decoration: line-through;">$1</span>',
            '<blockquote>$1</blockquote>',
            '<pre>$1</'.'pre>',
            '<span style="font-size: $1px;">$2</span>',
            '<span style="color: #$1;">$2</span>',
            '<a href="$1" target="_blank">$2</a>',
            '<a href="$1" target="_blank">$1</a>',
            '<img src="$1" alt="" />',
            '<li>$1</li>',
            '<ul>$1</ul>',
            '<ol>$1</ol>',
            '<span style="text-decoration:line-through">$1</span>'
        );

        // Perform the actual conversion
        $str = preg_replace($format_search, $format_replace, $str);

        // Convert line breaks in the <br /> tag
        $str = nl2br($str);
        
        return $str;
    }

}
