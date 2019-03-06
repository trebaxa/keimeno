<?php

/**
 * @package    reflist
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


defined('IN_SIDE') or die('Access denied.');

$REFLIST = new reflist_class();
$REFLIST->TCR->interpreter();
$REFLIST->load_reflinks();
$REFLIST->parse_to_smarty();

?>