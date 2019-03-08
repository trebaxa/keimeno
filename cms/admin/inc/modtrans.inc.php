<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


$ALANG_OBJ = new adminlang_class();


if ($_GET['mod'] == 'unset') {
    unset($_SESSION['at_mod']);
    unset($_GET['mod']);
}
if (!empty($_GET['mod']))
    $_SESSION['at_mod'] = $_GET['mod'];
if (empty($_GET['mod']) && !empty($_SESSION['at_mod']))
    $_GET['mod'] = $_SESSION['at_mod'];


$M = new modules_class();
$M->load_admin_translation($GBL_LANGID, $LANGS, $MODULE);

$lng_path = $M->ADMIN_MOD_TRANSPAGES[$_GET['mod']]['path'];


if (!empty($lng_path)) {
    $ALANG_OBJ->lng_path = $lng_path;
    $INTERPRETER = $ALANG_OBJ;
    include (CMS_ROOT . 'admin/inc/interpreter.inc.php');
}
$ALANG_OBJ->TCR->interpreter();

ksort($M->ADMIN_MOD_TRANSPAGES);
$smarty->assign('mod_list', $M->ADMIN_MOD_TRANSPAGES);
unset($M);
$ALANG_OBJ->parse_to_smarty();
$ADMINOBJ->inc_tpl('adminlang');
