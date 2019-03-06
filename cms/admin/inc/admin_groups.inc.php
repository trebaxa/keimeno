<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/admingroup.class.php');

if ($_REQUEST['direc'] == 'A') {
    $_REQUEST['direc'] = 'D';
}
else {
    $_REQUEST['direc'] = 'A';
}

$AG = new admingroup_class();
$AG->add_object($PERM);
$AG->TCR->interpreter();


$menu = array("{LBLA_ADMINGROUPS_MANAGER}" => "", # "{LBL_ROLES}" => "aktion=roles",
        );

#$ADMINOBJ->set_top_menu($menu);

$AG->load_groups();

if ($_GET['aktion'] == "roles") {
    $AG->load_role();
}

$ADMINOBJ->inc_tpl("admingroups.admin");
$AG->parse_to_smarty();

?>