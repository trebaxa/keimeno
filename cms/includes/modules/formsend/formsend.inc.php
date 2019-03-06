<?php

/**
 * @package    Keimeno::formsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-04-09
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

$FORMSEND = new formsend_class();
$FORMSEND->TCR->interpreter();
$FORMSEND->parse_to_smarty();
