<?php

/**
 * @package    ktracker
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */


$KTRACKER = new ktracker_admin_class();
$KTRACKER->TCR->interpreter();
$KTRACKER->parse_to_smarty();
$KTRACKER->add_tpl($content, 'ktracker');
?>