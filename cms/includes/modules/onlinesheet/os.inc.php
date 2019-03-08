<?php


# Scripting by Trebaxa Company(R) 2012						*

/**
 * @package    onlinesheet
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


defined('IN_SIDE') or die('Access denied.');

#$_GET['sheetid'] = ($_POST['sheetid'] > 0) ? intval($_POST['sheetid']) : intval($_GET['sheetid']);

$OSFIELD = new osfield_class();
$OSFIELD->langid = $GBL_LANGID;

/*
if ($_GET['sheetid'] == 0) {
$_GET['sheetid'] = $OSFIELD->get_sheet_ident_from_html($CORE->content);    
}


$OSFIELD->load_fields_table_from_sheet($_GET['sheetid']);
$OSFIELD->load_sheet($_GET['sheetid']);

$CORE->content = $OSFIELD->parse_to_html($CORE->content, $_POST['OSFORM'], $_GET['sheetid']);
*/
$OSFIELD->TCR->interpreterfe();
#$smarty->assign('sheet_obj', $OSFIELD->sheet_obj);


?>