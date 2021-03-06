<?

/**
 * Smarty plugin
 * @package Smarty
 * @subpackage plugins
 */


/**
 * Smarty in_array modifier plugin by trebaxa
 *
 * Type: modifier<br>
 * Name: in_array<br>
 * Purpose: check if value is in array
 * @author Collin Lee <clee at sugarcrm com>
 * @param mixed
 * @param mixed
 * @return boolean
 */
function smarty_modifier_inarray($needle = null, $haystack = null) {
    $haystack = (array)$haystack;
    return in_array($needle, $haystack);
}

?>