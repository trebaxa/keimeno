<?php


/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT.'admin/inc/pnf.class.php');
$PNF = new pnf_class();
$PNF->TCR->interpreter();
$PNF->init();
$PNF->parse_to_smarty();

$ADMINOBJ->inc_tpl('pnf');
?>