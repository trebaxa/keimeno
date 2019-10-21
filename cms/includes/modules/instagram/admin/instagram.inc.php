<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::instagram
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2019-10-18
 */
 

$INSTAGRAM = new instagram_admin_class();
$INSTAGRAM->TCR->interpreter();
$INSTAGRAM->parse_to_smarty();
$INSTAGRAM->add_tpl($content,'instagram');
?>