<?php

/**
 * @package    linkliste
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$LINKS_OBJ = new links_class();
$LINKS_OBJ->TCR->interpreter();


$menu = array(
    "{LBLA_SHOWALL}" => "",
    "Kategorien" => "aktion=groupman",
    "{LBL_CONFIG}" => "cmd=conf");
$ADMINOBJ->set_top_menu($menu);

$LINKS_OBJ->load_groups();
$LINKS_OBJ->load_all_toplevel();
$LINKS_OBJ->load_all_country();


if ($cmd == "") {
    $LINKS_OBJ->load_links($_REQUEST['cid'], $_REQUEST['wort']);
}

$LINKS_OBJ->parse_to_smarty();
$ADMINOBJ->inc_tpl('linklist');

?>