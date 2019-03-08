<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


$FIREWALL->TCR->interpreter();

if ($_GET['aktion'] == "delall") {
    $FIREWALL->clear_log();
    $TCMASTER->msg("{LBL_DELETED}");
    HEADER('location:run.php?epage=' . $_GET['epage'] . '&aktion=details');
    exit;
}


if ($_POST['aktion'] == 'a_save_config') {
    $CONFIG_OBJ->save($_POST['FORM']);
    $TCMASTER->hard_exit();
}


$menu = array(
    "Firewall" => "epage=" . $_GET['epage'],
    "Blacklist" => "epage=" . $_GET['epage'] . "&aktion=details",
    "Konfiguration" => "epage=" . $_GET['epage'] . "&aktion=config");
$ADMINOBJ->set_top_menu($menu);

$FIREWALL->locate_ip_adress(REAL_IP);

if ($_GET['aktion'] == 'details') {
    $FIREWALL->load_log();
}

if ($_GET['aktion'] == "config") {
    $ADMINOBJ->content .= '<div class="page-header"><h1>Einstellungen</h1></div>';
    $ADMINOBJ->content .= $CONFIG_OBJ->buildTable(33, 33);
}

$ADMINOBJ->inc_tpl('firewall.admin');

?>