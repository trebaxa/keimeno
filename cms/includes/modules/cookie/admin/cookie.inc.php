<?php

/**
 * @package    cookie
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$COOKIE = new cookie_admin_class();
$COOKIE->TCR->interpreter();
$COOKIE->parse_to_smarty();
$COOKIE->add_tpl($content, 'cookie');
?>