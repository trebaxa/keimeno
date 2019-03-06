  <?php

/**
 * Smarty plugin
 * 
 * @package Smarty
 * @subpackage PluginsFilter
 */

/**
 * Smarty htmlspecialchars variablefilter plugin
 * 
 * @param string $source input string
 * @param object $ &$smarty Smarty object
 * @return string filtered output
 */
function smarty_modifier_onlyalpha($source) {
    $source = preg_replace("/[^0-9a-zA-Z]_-/", "", strip_tags(strval($source)));
    return $source;
}

?>