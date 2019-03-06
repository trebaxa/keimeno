<?php

/**
 * @package    feedbacks
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


$FEEDB = new feedbacks_admin_class();
$FEEDB->TCR->interpreter();
$FEEDB->load_items();

$menu = array("{LBLA_SHOWALL}" => "");
#$ADMINOBJ->set_top_menu($menu);

$FEEDB->add_tpl($content, 'feedbacks.admin');

$FEEDB->parse_to_smarty();
