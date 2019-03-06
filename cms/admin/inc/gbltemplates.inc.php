<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

include (CMS_ROOT . 'admin/inc/gbltemplates.class.php');
$GBLTPL_OBJ = new gbltpl_class($CMSDATA);

$GBLTPL_OBJ->set_lang_id($_GET['uselang']);
$GBLTPL_OBJ->TCR->interpreter();

if ($_REQUEST['mod'] != "" && $_REQUEST['mod'] != -1) {
    $mod = $_SESSION['gbltemplatemod'] = $_REQUEST['mod'];
}
if ($_REQUEST['mod'] == "") {
    $mod = $_SESSION['gbltemplatemod'];
}
if ($_REQUEST['mod'] == -1) {
    unset($_REQUEST['mod']);
    unset($_SESSION['gbltemplatemod']);
    unset($_GET['mod']);
    $mod = "";
}

$LNGOBJ->init_uselang();


/*
$menu = array("System Templates" => "mod=" . $mod);
$ADMINOBJ->set_top_menu($menu);
*/

$GBLTPL_OBJ->load_gbltemplates($mod);
$GBLTPL_OBJ->parse_to_smarty();
keimeno_class::add_tpl($ADMINOBJ->content, 'gbltemplates');

#$smarty->assign('langselect', $LNGOBJ->build_lang_select_smarty($_GET['uselang']));
$smarty->assign('gbltemplatemod', $mod);
/*
$M = new modules_class();

$M->load_admin_translation($GBL_LANGID, $LANGS, $MODULE);
foreach ($M->ADMIN_MOD_TRANSPAGES as $key => $mod)
    $M->ADMIN_MOD_TRANSPAGES[$key]['mod_name'] = kf::translate_admin($M->ADMIN_MOD_TRANSPAGES[$key]['mod_name']);

if (count($M->ADMIN_MOD_TRANSPAGES) > 0)
    $M->ADMIN_MOD_TRANSPAGES = sort_db_result($M->ADMIN_MOD_TRANSPAGES, 'mod_name', SORT_ASC, SORT_STRING);

$smarty->assign('mod_list', $M->ADMIN_MOD_TRANSPAGES);
*/
unset($M);

?>