<?php

/**
 * @package    memindex
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


if (IN_SIDE != 1) {
    header('location:/index.html');
    exit;
}
exec_evt('OnCustomerIndexPage');

$MEMINDEX = new memindex_class();
$MEMINDEX->init_register();
$MEMINDEX->TCR->interpreterfe();
$MEMINDEX->init_index();
$MEMINDEX->customerlist();
$MEMINDEX->parse_to_smarty_fe();