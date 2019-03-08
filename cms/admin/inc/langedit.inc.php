<?php


/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/langedit.class.php');
$LANGEDIT = new langedit_class();
$LANGEDIT->TCR->interpreter();
$LANGEDIT->load_table();

$menu = array();
$menu['{LBLA_EDITJOKER}'] = 'admin=' . $_REQUEST['admin'];
#$ADMINOBJ->set_top_menu($menu);

$LANGEDIT->parse_to_smarty();
keimeno_class::add_tpl($ADMINOBJ->content, 'langedit');

?>