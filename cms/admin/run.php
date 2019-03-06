<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2019 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

#require ('../includes/hlock.class.php');
#hlock::run();

if (isset($_POST['epage'])) {
    $_GET['epage'] = ($_POST['epage'] != "") ? $_POST['epage'] : $_GET['epage'];
}
$content = "";

include (dirname(__FILE__) . '/inc/initadmin.inc.php');


foreach ($MODULE as $key => $mod) {
    if (!is_array($mod['epage'])) {
        if (!empty($mod['epage'])) {
            $EPAGES[$mod['epage']] = $mod;
        }
    }
    if (is_array($mod['epage']) && count($mod['epage']) > 0) {
        foreach ($mod['epage'] as $ep) {
            $EPAGES[$ep] = $mod;
        }
    }
}

if (isset($_GET['epage']) && array_key_exists($_GET['epage'], $EPAGES)) {
    if ($EPAGES[$_GET['epage']]['epage_dir'] == "" && file_exists(MODULE_ROOT . $EPAGES[$_GET['epage']]['id'] . '/admin/' . $_GET['epage'] . '.php')) {
        $EPAGES[$_GET['epage']]['epage_dir'] = MODULE_ROOT;
    }
}

if (isset($_GET['epage'])) {
    $smarty->assign('epage', $_GET['epage']);
}
else {
    $smarty->assign('epage', "");
}

if (isset($_GET['epage']) && array_key_exists($_GET['epage'], $EPAGES)) {
    if ($EPAGES[$_GET['epage']]['submodpath'] != "") {
        $mod_file = $EPAGES[$_GET['epage']]['epage_dir'] . 'admin/' . $_GET['epage'] . '.php';
    }
    else {
        $mod_file = $EPAGES[$_GET['epage']]['epage_dir'] . $EPAGES[$_GET['epage']]['id'] . '/admin/' . $_GET['epage'] . '.php';
    }
}


if (strpos($_GET['epage'], '://') !== FALSE || strpos($_GET['epage'], '../') !== FALSE || $_GET['epage'] == "" || (!file_exists(CMS_ROOT . 'admin/inc/' . $_GET['epage'] .
    ".php") && !file_exists($mod_file))) {

    $smarty->assign('not_found_page', array('epage' => $_GET['epage'], 'reason' => 'file not found'));
    $_GET['epage'] = "notfound";
}

if (!isset($_SESSION['RULE']['allowed_php'][md5($_GET['epage'])]) && $_SESSION['mitarbeiter'] != 1 && $_SESSION['mitarbeiter'] != 100) {
    $smarty->assign('not_found_page', array('epage' => $_GET['epage'], 'reason' => 'no access'));
    $_GET['epage'] = "notfound";
}

if (array_key_exists($_GET['epage'], $EPAGES)) {
    include ($mod_file);
}
else {
    include (CMS_ROOT . 'admin/inc/' . $_GET['epage'] . '.php');
}

include (CMS_ROOT . 'admin/inc/footer.inc.php');
