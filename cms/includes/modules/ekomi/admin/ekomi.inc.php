<?php



# This script is not freeware						     	*
/**
 * @package    ekomi
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$EKOMI = new ekomia_class();
$EKOMI->TCR->interpreter();

$menu = array(
    "eKomi Manager" => "section=start&cmd=load_latest",
    "eKomi Emails" => "section=ekomi_emails",
    "{LA_MODCONFIGURATION}" => "section=conf&cmd=conf");

$ADMINOBJ->set_top_menu($menu);

$EKOMI->parse_to_smarty();
$EKOMI->add_tpl($content, 'ekomi');
?>