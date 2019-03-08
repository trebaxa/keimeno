<?php

/**
 * @package    B8
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.1
 */
 
$B8 = new b8_admin_class();
$B8->TCR->interpreter();
$B8->parse_to_smarty();
$B8->add_tpl($content,'b8');
?>