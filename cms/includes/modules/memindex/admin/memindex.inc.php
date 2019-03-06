<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$MEMINDEX = new memindex_admin_class();
$MEMINDEX->TCR->interpreter();

$ADMINOBJ->inc_tpl('memindex');

$MEMINDEX->parse_to_smarty();
