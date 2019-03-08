<?php

/**
 * @package    statistic
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


$STAT = new statistic_class();
$STAT->TCR->interpreter();

$STAT->parse_to_smarty();
$ADMINOBJ->inc_tpl('statistic');

?>