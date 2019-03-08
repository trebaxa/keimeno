<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::safeupload
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2017-08-23
 */
 

$SAFEUPLOAD = new safeupload_admin_class();
$SAFEUPLOAD->TCR->interpreter();
$SAFEUPLOAD->parse_to_smarty();
$SAFEUPLOAD->add_tpl($content,'safeupload');
?>