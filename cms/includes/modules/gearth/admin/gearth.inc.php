<?php

/**
 * @package    gearth
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

$GEARTH = new gearth_admin_class();
$GEARTH->TCR->interpreter();
#$GEARTH->parse_to_smarty();
$GEARTH->add_tpl($content, 'gearth');

?>