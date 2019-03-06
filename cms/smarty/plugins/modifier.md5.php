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
function smarty_modifier_md5($source)
{
    return md5($source);
} 

?>
