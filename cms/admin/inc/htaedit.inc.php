<?php


# Scripting by Trebaxa Company(R) 2013    					*

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/htaedit.class.php');
$HTAEDIT = new htaedit_class();
$HTAEDIT->TCR->interpreter();

$HTAEDIT->parse_to_smarty();
$ADMINOBJ->inc_tpl('htaedit');
?>