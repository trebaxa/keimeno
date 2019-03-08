<?php


# Scripting by Trebaxa Company(R) 2012    					*

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/cust.groups.class.php');
$CUSTG = new custgroup_class();
$CUSTG->TCR->interpreter();

$menu = array(
    "{LBL_GROUPS}" => "cmd=all&section=start",
    "{LBL_COLLECTIONS}" => "cmd=coll&section=coll",
    );
$ADMINOBJ->set_top_menu($menu);

$CUSTG->parse_to_smarty();
$ADMINOBJ->inc_tpl('custgroup');

?>