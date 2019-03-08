<?php

/**
 * @package    Keimeno
 * @author Harald Petrich::mylivechat
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-06-11
 */
 

$MYLIVECHAT = new mylivechat_admin_class();
$MYLIVECHAT->TCR->interpreter();
$MYLIVECHAT->parse_to_smarty();
$MYLIVECHAT->add_tpl($content,'mylivechat');
?>