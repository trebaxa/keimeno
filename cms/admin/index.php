<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2019 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


require ('../includes/hlock.class.php');
hlock::run(dirname(__FILE__) . '/../');

include (dirname(__FILE__) . '/inc/initadmin.inc.php');

$ADMINOBJ = new mainadmin_class();
$ADMINOBJ->force_admin_www();
$ADMINOBJ->TCR->interpreter();
$ADMINOBJ->init_login();
