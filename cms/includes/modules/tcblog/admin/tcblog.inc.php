<?php

/**
 * @package    tcblog
 *
 * @copyright  Copyright (C) 2006 - 2017 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


$TCBLOG = new tcblog_admin_class();
$TCBLOG->TCR->interpreter();

$menu = array(
    "Blog {LBL_ITEMS}" => "cmd=load_items&section=items",
    "Blogs" => "cmd=load_groups&section=groups",
    "{LA_MODCONFIGURATION}" => "cmd=conf&section=conf");
$ADMINOBJ->set_top_menu($menu);

$LNGOBJ->init_uselang();
$ADMINOBJ->inc_tpl('tcblog');
$TCBLOG->parse_to_smarty();
