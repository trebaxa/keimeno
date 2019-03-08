<?php

/**
 * @package    menus
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$MENUS = new menus_class();
$MENUS->TCR->interpreter();
$MENUS->parse_to_smarty();

?>