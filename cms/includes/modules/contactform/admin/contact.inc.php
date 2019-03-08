<?php

/**
 * @package    contractform
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


$CONTACT = new contact_admin_class();
$CONTACT->TCR->interpreter();
/*
$menu = array(
"{LA_MODCONFIGURATION}" => "section=conf&cmd=conf");

$ADMINOBJ->set_top_menu($menu);
*/
$CONTACT->parse_to_smarty();
$CONTACT->add_tpl($content, 'contact');
?>