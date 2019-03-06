<?php

/**
 * @package    flextemp
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$FLEXTEMP = new flextemp_admin_class();
$FLEXTEMP->TCR->interpreter();
$FLEXTEMP->parse_to_smarty();
$FLEXTEMP->add_tpl($content, 'flextemp');

?>