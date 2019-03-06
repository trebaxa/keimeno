<?php

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty {html_image} function plugin
 *
 * Type:     function<br>
 * Name:     html_image<br>
 * Date:     Feb 24, 2003<br>
 * Purpose:  format HTML tags for the image<br>
 * Input:<br>
 *         - file = file (and path) of image (required)
 *         - height = image height (optional, default actual height)
 *         - width = image width (optional, default actual width)
 *         - basedir = base directory for absolute paths, default
 *                     is environment variable DOCUMENT_ROOT
 *         - path_prefix = prefix for path output (optional, default empty)
 *
 * Examples: {html_image file="/images/masthead.gif"}
 * Output:   <img src="/images/masthead.gif" width=400 height=23>
 * @link http://smarty.php.net/manual/en/language.function.html.image.php {html_image}
 *      (Smarty online manual)
 * @author   Monte Ohrt <monte at ohrt dot com>
 * @author credits to Duda <duda@big.hu> - wrote first image function
 *           in repository, helped with lots of functionality
 * @version  1.0
 * @param array
 * @param Smarty
 * @return string
 * @uses smarty_function_escape_special_chars()
 */
function smarty_function_html_subbtn($params, &$smarty) {
    require_once (SMARTY_PLUGINS_DIR . 'shared.escape_special_chars.php');
    $type = "submit";
    foreach ($params as $_key => $_val) {
        switch ($_key) {
            case 'alt':
            case 'label':
            case 'class':
                if (!is_array($_val)) {
                    $$_key = smarty_function_escape_special_chars($_val);
                }
                else {
                    $smarty->trigger_error("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;

            case 'link':
            case 'href':
                $prefix = '<a title="' . $label . '" href="' . $_val . '">';
                $suffix = '</a>';
                $type = "button";
                break;
            default:
                if (!is_array($_val)) {
                    $extra .= ' ' . $_key . '="' . smarty_function_escape_special_chars($_val) . '"';
                }
                else {
                    $smarty->trigger_error("html_image: extra attribute '$_key' cannot be an array", E_USER_NOTICE);
                }
                break;
        }
    }

    return $prefix . '<input type="' . $type . '" name="' . $name . '" value="' . $label . '" class="' . $class . '" ' . $extra . ' />' . $suffix;
    # return $prefix . '<img src="'.$path_prefix.$file.'" alt="'.$alt.'" width="'.$width.'" height="'.$height.'"'.$extra.' />' . $suffix;
}

/* vim: set expandtab: */

?>
