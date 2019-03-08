<?php

/**
 * @package    vim
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


$VIMEO_OBJ = new vimeocms_class();
$VIDEOTHEK_OBJ = new videothek_class();

include (CMS_ROOT . 'admin/inc/module.class.php');
$MODULE_OBJ = new module_class(MODULE_ROOT . 'vim/');
$MODULE_OBJ->TCR->interpreter();

$VIMEO_OBJ->TCR->interpreter();
$VIDEOTHEK_OBJ->TCR->interpreter();

if ($_POST['aktion'] == "a_save_config") {
    $CONFIG_OBJ->save($_POST['FORM']);
    $TCMASTER->hard_exit();
}


$ADMINOBJ->set_top_menu(array(
    "Video Index" => "epage=" . $_GET['epage'] . '&section=videomanager&cmd=videolist',
    "Video Suche" => "epage=" . $_GET['epage'] . '&section=search',
    "Vimeo Abgleich" => "epage=" . $_GET['epage'] . '&section=vimeosync&cmd=load_via_videos',
    "{LA_MODCONFIGURATION}" => "section=conf&epage=" . $_GET['epage'],
    "Style&Files" => "section=modstylefiles&epage=" . $_GET['epage'],
    ));

if ($_REQUEST['section'] == 'conf') {
    $VIDEOTHEK_OBJ->VIM['CONFTAB'] = $CONFIG_OBJ->buildTable(41, 41);
}

if ($_REQUEST['section'] == 'vimeosync') {
    $VIDEOTHEK_OBJ->build_selectbox_arr();
}

if ($_REQUEST['section'] == 'search') {
    $VIDEOTHEK_OBJ->load_iso_table();
    $VIDEOTHEK_OBJ->load_all_ytcats();
    $VIDEOTHEK_OBJ->add_tree_selectboxes($_POST['CIDS'], 'query');
}

if ($_REQUEST['section'] == 'cats') {
    if ($_GET['aktion'] == 'showall' || $_GET['aktion'] == '') {
        $VIDEOTHEK_OBJ->buildATree(0, $_GET['starttree']);
        $VIDEOTHEK_OBJ->load_cat_table($_GET['starttree']);
    }
    if ($_GET['aktion'] == 'catedit') {
        $VIDEOTHEK_OBJ->load_cat($_GET['id']);
        $VIDEOTHEK_OBJ->build_tree_selectbox($VIDEOTHEK_OBJ->CATOBJ['ytc_parent'], $VIDEOTHEK_OBJ->CATOBJ['id']);
        if ($VIDEOTHEK_OBJ->VIM['fault_form'] == TRUE) {
            $VIDEOTHEK_OBJ->VIM['CATOBJ'] = array_merge($_POST['FORM'], $VIDEOTHEK_OBJ->VIM['CATOBJ']);
        }
    }
    if ($_GET['cmd'] == 'catadd') {
        $VIDEOTHEK_OBJ->load_cat(0);
        $VIDEOTHEK_OBJ->build_tree_selectbox(0, 0);
        if ($VIDEOTHEK_OBJ->VIM['fault_form'] == TRUE) {
            $VIDEOTHEK_OBJ->VIM['CATOBJ'] = $_POST['FORM'];
        }
    }

}

$ADMINOBJ->inc_tpl('video');
$VIDEOTHEK_OBJ->parse_to_smarty();

?>