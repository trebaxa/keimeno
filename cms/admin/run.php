<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */

#require ('../includes/hlock.class.php');
#hlock::run(dirname(__FILE__) . '/../');

if (isset($_POST['epage'])) {
    $_GET['epage'] = ($_POST['epage'] != "") ? $_POST['epage'] : $_GET['epage'];
}
$content = "";

if (isset($_GET['epage'])) {
    $epage = preg_replace("/[^0-9a-z._]/", "", strval($_GET['epage']));
}

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

if (isset($epage) && array_key_exists($epage, $EPAGES)) {
    if ($EPAGES[$epage]['epage_dir'] == "" && file_exists(MODULE_ROOT . $EPAGES[$epage]['id'] . '/admin/' . $epage . '.php')) {
        $EPAGES[$epage]['epage_dir'] = MODULE_ROOT;
    }
}


if (isset($epage)) {
    $smarty->assign('epage', $epage);
}
else {
    $smarty->assign('epage', "");
}

if (isset($epage) && array_key_exists($epage, $EPAGES)) {
    if ($EPAGES[$epage]['submodpath'] != "") {
        $mod_file = $EPAGES[$epage]['epage_dir'] . 'admin/' . $epage . '.php';
    }
    else {
        $mod_file = $EPAGES[$epage]['epage_dir'] . $EPAGES[$epage]['id'] . '/admin/' . $epage . '.php';
    }
}


if (strpos($epage, '://') !== FALSE || strpos($epage, '../') !== FALSE || $epage == "" || (!file_exists(CMS_ROOT . 'admin/inc/' . $epage .
    ".php") && !file_exists($mod_file))) {

    $smarty->assign('not_found_page', array('epage' => $epage, 'reason' => 'file not found'));
    $epage = "notfound";
}

if (!isset($_SESSION['RULE']['allowed_php'][md5($epage)]) && $_SESSION['mitarbeiter'] != 1 && $_SESSION['mitarbeiter'] != 100) {
    $smarty->assign('not_found_page', array('epage' => $epage, 'reason' => 'no access'));
    $epage = "notfound";
}

if (array_key_exists($epage, $EPAGES)) {
    include ($mod_file);
}
else {
    include (CMS_ROOT . 'admin/inc/' . $epage . '.php');
}

include (CMS_ROOT . 'admin/inc/footer.inc.php');
