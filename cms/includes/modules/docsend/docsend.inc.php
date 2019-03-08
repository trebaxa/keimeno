<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::docsend
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-24
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

$DOCSEND = new docsend_class();
$DOCSEND->TCR->interpreter();
$DOCSEND->parse_to_smarty();
