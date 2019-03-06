<?php

/**
 * @package    rediconn
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */

defined( 'IN_SIDE' ) or die( 'Access denied.' );

$REDICONN = new rediconn_class();
$REDICONN->TCR->interpreter();
$REDICONN->parse_to_smarty();

?>