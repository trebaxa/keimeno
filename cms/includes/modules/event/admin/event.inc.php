<?php

/**
 * @package    calendar
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$EVENT = new event_admin_class();
$EVENT->TCR->interpreter();


$EVENT_OBJ = new event_class($_GET['uselang'], $_SESSION['seldate']);


$ADMINOBJ->set_top_menu(array(
    "{LBL_TERMINE}" => "cmd=load_events&epage=" . $_GET['epage'],
    "{LBL_CALTHEME}" => "aktion=calgroups&epage=" . $_GET['epage'],
    "{LA_MODCONFIGURATION}" => "aktion=conf&epage=" . $_GET['epage']));


$ADMINOBJ->inc_tpl('calendar');
$EVENT->parse_to_smarty();
