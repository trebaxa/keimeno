<?php
/**
* Smarty plugin
* 
* @package Smarty
* @subpackage PluginsFilter
*/

/**
* Smarty human_file_size variablefilter plugin
* 
* @param string $source input string
* @param object $ &$smarty Smarty object
* @return string filtered output
*/
function smarty_modifier_hfs($size){
	$filesizename = array(" Bytes", " KB", " MB", " GB", " TB", " PB", " EB", " ZB", " YB");
	if ($size>0) return round($size/pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
	else return '0 Bytes';
}

?>
