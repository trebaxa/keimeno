<?php

/**
 * @package    reflist
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.2
 */


$REFLIST = new reflist_admin_class();
$REFLIST->TCR->interpreter();
$REFLIST->load_reflinks();
$REFLIST->parse_to_smarty();
$REFLIST->add_tpl($content, 'reflist');

?>