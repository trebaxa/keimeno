<?php

/**
 * @package    sellform
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


include (CMS_ROOT . 'admin/inc/module.class.php');
$MODULE_OBJ = new module_class(MODULE_ROOT . 'sellform/');
$MODULE_OBJ->TCR->interpreter();

$SELLFORMS = new sellform_class();
$SELLFORMS->TCR->interpreter();

$menu = array(
    "{LBLA_SHOWALL}" => "",
    "Style&Files" => "section=modstylefiles&epage=" . $_GET['epage'],
    "{LA_MODCONFIGURATION}" => "cmd=conf&section=conf");
$ADMINOBJ->set_top_menu($menu);


$SELLFORMS->load_forms();
$SELLFORMS->load_zw();


$SELLFORMS->parse_to_smarty();
$ADMINOBJ->inc_tpl('sellformadmin');

?>