<?php

/**
 * @package    rediapi
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


$REDIAPI = new rediapi_admin_class();
$REDIAPI->TCR->interpreter();
$REDIAPI->load_apis();
$REDIAPI->parse_to_smarty();

$ADMINOBJ->inc_tpl('rediapi');

?>