<?php

/**
 * @package    rediconn
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */
 
$REDICONN = new rediconn_admin_class();
$REDICONN->TCR->interpreter();
$REDICONN->parse_to_smarty();
$REDICONN->add_tpl($content, 'rediconn');

?>