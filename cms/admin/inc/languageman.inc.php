<?php




/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


if ($_GET['admin'] == 'yes')
    $_SESSION['LNGOPT']['sql_table'] = TBL_CMS_LANG_ADMIN;
if ($_GET['admin'] == 'no')
    $_SESSION['LNGOPT']['sql_table'] = TBL_CMS_LANG;
if ($_GET['admin'] != "")
    $_SESSION['LNGOPT']['type'] = $_GET['admin'];

if (strlen($_SESSION['LNGOPT']['sql_table']) == 0) {
    unset($_SESSION['LNGOPT']);
    header('location:welcome.html');
    exit;
}

$LNGOBJ->options = $_SESSION['LNGOPT'];
$LNGOBJ->TCR->interpreter();

$menu = array(
    "{LBL_LANGUAGES} FrontEnd" => "aktion=showall&admin=no",
    "{LBL_LANGUAGES} BackEnd" => "aktion=showall&admin=yes",
    );

$ADMINOBJ->set_top_menu($menu);

$LNGOBJ->load_langs();
$LNGOBJ->load_lang($_REQUEST['id']);
if (isset($_POST['FORM'])) {
    $LNGOBJ->lng_loaded = $_POST['FORM'];
}
$LNGOBJ->parse_to_smarty();

$ADMINOBJ->inc_tpl('langman.admin');

?>