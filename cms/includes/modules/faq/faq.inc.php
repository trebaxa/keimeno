<?php

/**
 * @package    faq
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */

defined('IN_SIDE') or die('Access denied.');

$FAQ = new faq_class();
$FAQ->TCR->interpreter();
$FAQ->parse_to_smarty();

?>