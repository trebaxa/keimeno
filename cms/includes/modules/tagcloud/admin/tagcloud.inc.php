<?php

/**
 * @package    tagcloud
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$LNGOBJ->init_uselang();
$TAGCLOUDADMIN = new tagcloud_admin_class();
$TAGCLOUDADMIN->TCR->interpreter();
$TAGCLOUD = new tagcloud_class();
$TAGCLOUD->TCR->interpreter();


if ($_GET['aktion'] == 'a_deltag') {
    $TAGCLOUD->delete_tag($_GET['id']);
    $TAGCLOUD->msg('{LBL_DELETED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
    exit;
}

if ($_POST['aktion'] == 'a_save_config') {
    $CONFIG_OBJ->save($_POST['FORM']);
    $TCMASTER->hard_exit();
}

if ($_POST['aktion'] == 'asavetag') {
    $TAGCLOUD->save_single_tag($_POST['FORM'], $_POST['id']);
    $TAGCLOUD->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?id=' . $_POST['id'] . '&aktion=edit&epage=' . $_GET['epage']);
    exit;
}

if ($_POST['aktion'] == 'delrelation') {
    $TAGCLOUD->delete_realtions($_POST['REL']);
    $TAGCLOUD->msg('{LBL_DELETED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?id=' . $_POST['id'] . '&aktion=edit&epage=' . $_GET['epage']);
    exit;
}

if ($_POST['aktion'] == 'masstagdelete') {
    $TAGCLOUD->mass_delete_tag($_POST['tagids']);
    $TAGCLOUD->msg('{LBL_DELETED}');
    header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage']);
    exit;
}

if ($_POST['aktion'] == 'masstagapprove') {
    $TAGCLOUD->mass_approve_tag($_POST['tagids']);
    $TAGCLOUD->msg('{LBLA_SAVED}');
    header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage']);
    exit;
}

if ($_POST['aktion'] == 'masstagdisapprove') {
    $TAGCLOUD->mass_disapprove_tag($_POST['tagids']);
    $TAGCLOUD->msg('{LBLA_SAVED}');
    header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage']);
    exit;
}

$menu = array("Tags verwalten" => "epage=" . $_GET['epage'], "Konfiguration" => "epage=" . $_GET['epage'] . "&cmd=conf");
$ADMINOBJ->set_top_menu($menu);

$smarty->assign('langselect', $LNGOBJ->build_lang_select());

if ($_GET['aktion'] == 'edit') {
    $TAGCLOUD->load_single_tag($_GET['id'], 'tag_pid', TBL_CMS_TEMPLATES, 'id', 'description');
    $TAGCLOUD->build_tagcloud();
}


if ($_GET['aktion'] == '') {
    $TAGCLOUD->langid = $_GET['uselang'];
    $TAGCLOUD->load_tags('tag_pid', TBL_CMS_TEMPLATES, 'id', 'description', $_GET['tagorder']);
    $TAGCLOUD->build_tagcloud();
}
$TAGCLOUDADMIN->parse_to_smarty();
$ADMINOBJ->inc_tpl('tagcloud.manager.admin');

?>
