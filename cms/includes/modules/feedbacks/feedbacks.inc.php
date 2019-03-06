<?php

/**
 * @package    feedbacks
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


defined('IN_SIDE') or die('Access denied.');

$FB = new feedbacks_class();
$FB->TCR->interpreterfe();
$FB->load_feedbacks();
