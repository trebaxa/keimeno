<?php

/**
 * @package    menus
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$MENUS = new menus_admin_class();
$MENUS->TCR->interpreter();
$MENUS->parse_to_smarty();
$MENUS->add_tpl($content, 'menus');
?>