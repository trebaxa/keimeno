<?php

/**
 * @package    forum
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$FORUM_OBJ = new forum_class();

$FORUM = new forum_admin_class();
$FORUM->TCR->interpreter();

include (CMS_ROOT . 'admin/inc/module.class.php');
$MODULE_OBJ = new module_class(MODULE_ROOT . 'forum/');
$MODULE_OBJ->TCR->interpreter();


$ADMINOBJ->set_top_menu(array(
    "Foren" => "aktion=fgroups&epage=" . $_GET['epage'],
    "Style&Files" => "section=modstylefiles&epage=" . $_GET['epage'],
    "{LA_MODCONFIGURATION}" => "section=conf&cmd=conf&epage=" . $_GET['epage']));


$FORUM->parse_to_smarty();
$ADMINOBJ->inc_tpl('forum.admin');

?>