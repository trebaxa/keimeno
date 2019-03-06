<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/emailsman.class.php');
$EM = new emailman_class();
$EM->TCR->interpreter();
$EM->load_mod_filter();
$EM->load_mails_tpls();

$ADMINOBJ->inc_tpl('emailsman');

$EM->parse_to_smarty();

?>