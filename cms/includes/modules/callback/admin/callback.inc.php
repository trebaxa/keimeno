<?php

/**
 * @package    callback
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$CALLBACK = new callback_admin_class();
$CALLBACK->TCR->interpreter();
$CALLBACK->parse_to_smarty();
$CALLBACK->add_tpl($content, 'callback');
?>