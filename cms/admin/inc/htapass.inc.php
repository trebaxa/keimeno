<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/htapass.class.php');
$HTA = new htapass_class(PATH_CMS, CMS_ROOT);

if ($_POST['aktion'] == 'reset') {
    $HTA->htreset();
    HEADER('location:login.html?msg=' . base64_encode('{LBL_DONE}'));
    exit;
}

if ($_POST['aktion'] == 'save') {
    if (!preg_match("/^[a-zA-Z0-9]+$/s", $_POST['htapassword'])) {
        $HTA->msge('Ungueltige Zeichen im Passwort.');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if (!preg_match("/^[a-zA-Z0-9]+$/s", $_POST['htauser'])) {
        $HTA->msge('Ungueltige Zeichen im Login Namen.');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if ($_POST['htapassword'] != $_POST['htapassword2']) {
        $HTA->msge('Passwort Wdh. falsch');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }
    if ($_POST['htapassword'] != "" && $_POST['htauser'] != "") {
        $HTA->save_file($_POST['htauser'], $_POST['htapassword']);
    }
    HEADER('location:login.html?msg=' . base64_encode('Password set'));
    exit;
}

$ADMINOBJ->content .= '<%include file="htpass.tpl"%>';
