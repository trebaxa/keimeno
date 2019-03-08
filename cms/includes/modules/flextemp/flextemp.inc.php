<?php

/**
 * @package    flextemp
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$FLEXTEMP = new flextemp_class();
$FLEXTEMP->TCR->interpreter();
$FLEXTEMP->parse_to_smarty();

?>