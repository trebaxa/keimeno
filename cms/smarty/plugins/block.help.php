<?php

/**
 * Smarty plugin to format text as htmlspecialchars
 *
 * @package Smarty
 * @subpackage PluginsBlock
 */

/**
 * Smarty {help}{/help} block plugin
 *
 * Type:     block function<br>
 * Name:     help<br>
 * Purpose:  format text a certain way with preset styles
 *           or custom wrap/indent settings<br>
 *
 * @link http://www.smarty.net/manual/en/language.function.textformat.php {textformat}
 *       (Smarty online manual)
 * @param array                    $params   parameters
 * @param string                   $content  contents of the block
 * @param Smarty_Internal_Template $template template object
 * @param boolean                  &$repeat  repeat flag
 * @return string content re-formatted
 * @author Monte Ohrt <monte at ohrt dot com>
 */

function smarty_block_help($params, $content, $template, &$repeat) {
    if (is_null($content)) {
        return;
    }
    return trim(str_replace(array('    ',"\t"),'&nbsp;&nbsp;&nbsp;&nbsp;', nl2br(htmlspecialchars($content))));
}
