<?php

/**
 * @package    ramicronews
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$RAMICRONEWS = new ramicronews_admin_class();
$RAMICRONEWS->TCR->interpreter();
$RAMICRONEWS->parse_to_smarty();
$RAMICRONEWS->add_tpl($content, 'ramicronews');

?>