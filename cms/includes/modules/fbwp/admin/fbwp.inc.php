<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/module.class.php');
$MODULE_OBJ = new module_class(MODULE_ROOT . 'fbwp/');
$MODULE_OBJ->TCR->interpreter();

$FBWP = new fbwpadmin_class();
$FBWP->TCR->interpreter();

$FBWP->init();


$ADMINOBJ->set_top_menu(array(
    "FB Welcome Page" => "section=start&epage=" . $_GET['epage'],
    "{LA_MODCONFIGURATION}" => "section=conf&epage=" . $_GET['epage'],
    "Style&Files" => "section=modstylefiles&epage=" . $_GET['epage']));

$ADMINOBJ->inc_tpl('fbwpadmin');
$FBWP->parse_to_smarty();

?>