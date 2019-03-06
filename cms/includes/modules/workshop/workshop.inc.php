<?php

/**
 * @package    workshop
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$WORKSHOP = new workshop_class();
$WORKSHOP->TCR->interpreter();
$WORKSHOP->parse_to_smarty();

?>