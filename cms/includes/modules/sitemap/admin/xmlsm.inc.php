<?php

/**
 * @package    sitemap
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$XMLSM = new xmlsm_class();
$XMLSM->init();
$XMLSM->TCR->interpreter();


#$ADMINOBJ->set_top_menu(array("XML Sitemap Generator" => "epage=" . $_GET['epage']));

$ADMINOBJ->inc_tpl('xmlsm');
$XMLSM->parse_to_smarty();

?>