<?php

/**
 * @package    ktracker
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

$KTRACKER = new ktracker_class();
$KTRACKER->TCR->interpreter();
$KTRACKER->parse_to_smarty();

?>