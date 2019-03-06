<?php

/**
 * @package    Keimeno::mylivechat
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 * @since      2018-06-11
 */
 
defined( 'IN_SIDE' ) or die( 'Access denied.' );

$MYLIVECHAT = new mylivechat_class();
$MYLIVECHAT->TCR->interpreter();
$MYLIVECHAT->parse_to_smarty();
