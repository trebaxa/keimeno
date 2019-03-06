<?php

/**
 * @package    onlinesheet
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$_GET['sheetid'] = ($_POST['sheetid'] > 0) ? intval($_POST['sheetid']) : intval($_GET['sheetid']);
$LNGOBJ->init_uselang();


$OSFIELDADMIN = new os_fields_admin_class();
$OSFIELDADMIN->langid = $_GET['uselang'];
$OSFIELDADMIN->TCR->interpreter();


$OSFIELD = new osfield_class();
$OSFIELD->langid = $_GET['uselang'];
$OSFIELD->TCR->interpreter();

$ulang_obj = $kdb->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE id=" .
    (int)$_GET['uselang']);


$menu = array("{LBL_OSARCIVES}" => "epage=" . $_GET['epage'] .
        "&aktion=archives", "{LBL_ONLINESHEETMANAGER}" => "epage=" . $_GET['epage'] .
        "&aktion=showsheets");
$ADMINOBJ->set_top_menu($menu);

$OSFIELD->load_all_sheets();
$OSFIELD->process_orders($_GET['order'], $_GET['dc']);

if ($_GET['sheetid'] > 0) {
    $OSFIELD->load_fields_table_from_sheet($_GET['sheetid']);
    $OSFIELD->load_sheet($_GET['sheetid']);
}

$smarty->assign('FIELDS', $OSFIELD->field_table);
$smarty->assign('ccount', $OSFIELD->ccount);
$smarty->assign('fieldtypes', $OSFIELD->fieldtypes);

$smarty->assign('SHEETS', $OSFIELD->sheet_table);
$smarty->assign('sheet_obj', $OSFIELD->sheet_obj);
$smarty->assign('scount', $OSFIELD->scount);
$smarty->assign('sheetid', $_GET['sheetid']);
$smarty->assign('uselang', $_GET['uselang']);
$smarty->assign('uselangselect', $LNGOBJ->build_lang_select('&sheetid=' . $_GET['sheetid']));


keimeno_class::add_tpl($ADMINOBJ->content, 'os.sheet');
$OSFIELDADMIN->parse_to_smarty();

?>