<?php

/**
 * @package    tplvars
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined('IN_SIDE') or die('Access denied.');

$TPLVARS = new tplvars_class();
$TPLVARS->TCR->interpreter();
$TPLVARS->parse_to_smarty();

?>