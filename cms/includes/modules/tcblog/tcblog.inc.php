<?php

/**
 * @package    tcblog
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


defined('IN_SIDE') or die('Access denied.');

$TCBLOG_OBJ = new tcblog_class();
$TCBLOG_OBJ->fe_init();
$TCBLOG_OBJ->TCR->interpreter();
$TCBLOG_OBJ->parse_to_smarty_fe();
