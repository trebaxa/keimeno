<?php

/**
 * @package    B8
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */

defined( 'IN_SIDE' ) or die( 'Access denied.' );

$B8 = new b8_class();
$B8->TCR->interpreter();
$B8->parse_to_smarty();

?>