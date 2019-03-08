<?php

/**
 * @package    indexsearch
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$INDEXSEARCH = new indexsearch_admin_class();
$INDEXSEARCH->TCR->interpreter();


$menu = array(
    "Index" => "epage=" . $_GET['epage'] . "&cmd=load_index",
    "W&ouml;rter" => "epage=" . $_GET['epage'] . "&cmd=words",
    "Aufgaben" => "epage=" . $_GET['epage'] . "&cmd=showtasks",
    "Konfiguration" => "epage=" . $_GET['epage'] . "&cmd=conf");
$ADMINOBJ->set_top_menu($menu);

$INDEXSEARCH->parse_to_smarty();
$INDEXSEARCH->add_tpl($content, 'indexsearchadmin');

?>