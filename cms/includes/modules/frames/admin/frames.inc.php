<?php

/**
 * @package    frames
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$FRAMES = new frames_admin_class();
$FRAMES->TCR->interpreter();

$ADMINOBJ->set_top_menu(array(
    "Farbdefinitionen" => "section=start&epage=" . $_GET['epage'],
    "Rahmen" => "section=framedefs&cmd=loadframedefs&epage=" . $_GET['epage'],
    "{LA_MODCONFIGURATION}" => "section=conf&cmd=conf&epage=" . $_GET['epage']));


$FRAMES->parse_to_smarty();
$FRAMES->add_tpl($content, 'frames');

?>