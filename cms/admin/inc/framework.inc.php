<?php


# Scripting by Trebaxa Company(R) 2013						*

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */



include (CMS_ROOT . 'admin/inc/framework.class.php');

$FW = new framework_class();
$FW->TCR->interpreter();
$FW->parse_to_smarty();

$ADMINOBJ->inc_tpl('framework');

?>