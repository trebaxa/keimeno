<?php

/**
 * @package    inlay
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


unset($_SESSION['gbltemp']);
$_GET['uselang'] = ($_POST['uselang'] > 0) ? $_POST['uselang'] : $_GET['uselang'];
$INLAY_OBJ = new inlay_class();
$INLAY_OBJ->langid = $GBL_LANGID;
$INLAY_OBJ->DATA = $CMSDATA;
$INLAY_OBJ->init();
$INLAY_OBJ->set_lang_id($_REQUEST['uselang']);
$INLAY_OBJ->TCR->interpreter();
$INLAY_OBJ->load_conn_table($_REQUEST['id']);
$INLAY_OBJ->inlay_connect_init();


$menu = array("{LBLA_SHOWALL}" => "", );
$ADMINOBJ->set_top_menu($menu);

$INLAY_OBJ->load_inlays();
$INLAY_OBJ->TEMPL_OBJ->build_lang_select();
$INLAY_OBJ->parse_to_smarty();
$ADMINOBJ->inc_tpl('inlaymanager');

?>