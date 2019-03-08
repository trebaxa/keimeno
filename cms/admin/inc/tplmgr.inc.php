<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/topleveladmin.class.php');
$TOPLEVELADMIN = new topleveladmin_class();
$TOPLEVELADMIN->TCR->interpreter();
$TOPLEVELADMIN->parse_to_smarty();
$ADMINOBJ->content .= '<% include file="toplevel.editor.tpl" %>';

?>