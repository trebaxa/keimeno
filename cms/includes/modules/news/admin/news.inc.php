<?php

/**
 * @package    news
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$LNGOBJ->init_uselang();

$NEWSA_OBJ = new news_admin_class();
$NEWSA_OBJ->TCR->interpreter();

$NEWS_OBJ = new news_class();


$menu = array(
    "{LBLA_SHOWALL}" => "epage=" . $_GET['epage'] . "&cmd=list",
    "{LBL_NEWSGROUPS}" => "epage=" . $_GET['epage'] . "&cmd=newsgroups",
    "{LA_MODCONFIGURATION}" => "section=conf&cmd=conf");
$ADMINOBJ->set_top_menu($menu);


$NEWSA_OBJ->gen_selbox_groups();

if ($_REQUEST['gid'] > 0) {
    $NEWS_OBJ->load_group($_REQUEST['gid']);
}

if ($_GET['setkid'] > 0) {
    $NEWS_OBJ->set_kid($_GET['setkid'], $_GET['id']);
}

if ($_GET['cmd'] != "newsgroups") {
    keimeno_class::add_tpl($ADMINOBJ->content, 'news.table.admin');
}
if ($_GET['cmd'] == "newsgroups") {
    keimeno_class::add_tpl($ADMINOBJ->content, 'news.groups');
}
$NEWSA_OBJ->parse_to_smarty();

?>