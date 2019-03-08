<?php


# Scripting by Trebaxa Company(R) 2010   									  *

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/dashboard.class.php');

$DASH = new dashboard_class();
$DASH->TCR->interpreter();
$DASH->initdash();

$ADMINOBJ->inc_tpl('welcome.admin');

?>