<?php

/**
 * @package    callback
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined('IN_SIDE') or die('Access denied.');

$CALLBACK = new callback_class();
$CALLBACK->TCR->interpreter();
$CALLBACK->parse_to_smarty();

?>