<?php

/**
 * @package    downloadcenter
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */



$DOCCENTER = new doccenter_class();
$DOCCENTER->TCR->interpreter();


clearstatcache();

if ($_GET['reset'] == "1") {
    unset($_SESSION['froot_dc']);
    unset($_SESSION['working_root_dc']);
}

if ($_GET['working'] == "webspace") {
    $_SESSION['working_root_dc'] = FILE_ROOT . DOWNCENTER;
    unset($_SESSION['froot_dc']);
}
$_SESSION['working_root_dc'] = (strlen($_SESSION['working_root_dc']) == 0) ?
    FILE_ROOT . DOWNCENTER : $_SESSION['working_root_dc'];

$HACKING = false;

if (!is_dir(FILE_ROOT))
    mkdir(FILE_ROOT, 0755);
if (!is_dir(FILE_ROOT . 'file_server'))
    mkdir(FILE_ROOT . 'file_server', 0755);
if (!is_dir(FILE_ROOT . DOWNCENTER))
    mkdir(FILE_ROOT . DOWNCENTER, 0755);

$_SESSION['froot_dc'] = (strlen($_SESSION['froot_dc']) == 0) ? $_SESSION['working_root_dc'] :
    $_SESSION['froot_dc'];


#*********************************
# SECURE !!!
#*********************************
if (strstr($_GET['dir'], '..') || strstr($_GET['dir'], '/')) {
    $HACKING = true;
}
if (strstr($_GET['datei'], '..') || strstr($_GET['datei'], '/')) {
    $HACKING = true;
}
if (strlen($_SESSION['froot_dc']) < strlen($_SESSION['working_root_dc']) || !
    is_dir($_SESSION['froot_dc'])) {
    $HACKING = true;
}
if ($HACKING == true) {
    $_GET['aktion'] = "";
    $_POST['aktion'] = "";
    $_SESSION['froot_dc'] = $_SESSION['working_root_dc'];
}


#*********************************
# SECURE !!!
#*********************************
if (strlen($_SESSION['froot_dc']) < strlen($_SESSION['working_root_dc']) || !
    is_dir($_SESSION['froot_dc']))
    $_SESSION['froot_dc'] = $_SESSION['working_root_dc'];


$menu = array("{LBL_DC_EXPLORER}" => "working=cmsspace", "{LBL_DC_MANAGEDFILES}" =>
        "cmd=mfiles");
$ADMINOBJ->set_top_menu($menu);


$DOCCENTER->set_current_directory();
$DOCCENTER->set_path_from_cms();
$DOCCENTER->equal_dirs();
$DOCCENTER->show_directory();
$DOCCENTER->parse_to_smarty();
$DOCCENTER->add_tpl($content, 'downloadcenter.explorer');

?>