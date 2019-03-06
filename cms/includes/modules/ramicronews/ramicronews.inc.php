<?php

/**
 * @package    ramicronews
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$RAMICRONEWS = new ramicronews_class();
$RAMICRONEWS->TCR->interpreter();
$RAMICRONEWS->parse_to_smarty();

?>