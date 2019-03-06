<?php

/**
 * @package    faq
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


$FAQ = new faq_admin_class();
$FAQ->TCR->interpreter();
$FAQ->load_groups();
$FAQ->parse_to_smarty();
$FAQ->add_tpl($content, 'faq');

?>