<?php

/**
 * @package    jtagcloud
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$JTAGCLOUD = new jtagcloud_class();
$JTAGCLOUD->TCR->interpreter();
$JTAGCLOUD->parse_to_smarty();

?>