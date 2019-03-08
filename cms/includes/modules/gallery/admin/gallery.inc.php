<?php




/**
 * @package    gallery
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


include_once ('../includes/tree.class.php');
$_GET['uselang'] = ($_GET['uselang'] == 0) ? 1 : (int)$_GET['uselang'];

$CMSGALLERY = new gal_class();
$CMSGALLERY->TCR->interpreter();

$GALLERY = new gallery_class();
$GALLERY->TCR->interpreter();




if ($_POST['aktion'] == "a_savecontent") {
    if ($_POST['FORM_CON_ID'] > 0)
        update_table(TBL_CMS_GLGRCON, 'id', $_POST['FORM_CON_ID'], $_POST['FORM_CON']);
    else
        insert_table(TBL_CMS_GLGRCON, $_POST['FORM_CON']);
    $CMSGALLERY->msgok("{LBLA_SAVED}[BR]");
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $_POST['FORM_CON']['lang_id'] .
        '&aktion=edit&epage=' . $_GET['epage'] . '&id=' . $_POST['FORM_CON']['g_id']);
    exit;
}


$_GET['gid'] = ($_POST['gid'] > 0) ? intval($_POST['gid']) : intval($_GET['gid']);


$menu = array(
    "{LBL_PICMANAGER}" =>
        'redirect=run.php?section=start&cmd=initpicman&epage=gallerypicmanager.inc&gid=' .
        $_GET['gid'] . '&msid=' . md5('{LBL_PICMANAGER}'),
    "{LBLA_GALLERYGROUPS}" => 'cmd=load_groups',
    # "Style&Files" => "section=modstylefiles&epage=" . $_GET['epage'],
    "Tools" => "redirect=run.php?epage=gallerypicmanager.inc&cmd=tools&section=tools",
    "{LA_MODCONFIGURATION}" =>
        "redirect=run.php?epage=gallerypicmanager.inc&cmd=conf&section=conf");
$ADMINOBJ->set_top_menu($menu);

$ADMINOBJ->inc_tpl('gallery.groups');
$GALLERY->parse_to_smarty();
?>