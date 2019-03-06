<?php


# Scripting by Trebaxa Company(R) 2010   									  *

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/dashboard.class.php');

$DASH = new dashboard_class();
$DASH->TCR->interpreter();
$DASH->initdash();

$ADMINOBJ->inc_tpl('welcome.admin');

?>