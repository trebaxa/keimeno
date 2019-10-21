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
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

$INSTAGRAM = new instagram_class();
$INSTAGRAM->TCR->interpreter();
$INSTAGRAM->parse_to_smarty();
