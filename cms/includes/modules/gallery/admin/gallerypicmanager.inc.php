<?php

/**
 * @package    gallery
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


include_once ('../includes/tree.class.php');
DEFINE('ISADMIN', 1);
DEFINE('UPL_ROOT', CMS_ROOT . 'admin/cache/import/');


$GAL_OBJ = new gal_class();
$GAL_OBJ->init_obj($GBL_LANGID, $user_object, (int)$_GET['gid']);


$GALLERY = new gallery_class();
$GALLERY->TCR->interpreter();


if ($_POST['FORM']['group_id'] > 0)
    $_GET['gid'] = $_POST['FORM']['group_id'];


if ($_POST['aktion'] == "a_sort") {
    $GALLERY->sortPictures($_POST['gid'], $_POST['column'], $_POST['direction']);
    $GALLERY->msg('{LBLA_SAVED}');
    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&gid=' . $_POST['gid']);
    exit;
}


if ($_REQUEST['aktion'] == 'gblresize') {
    foreach ($_REQUEST['ROPT'] as $key => $value) {
        $urladd .= 'ROPT[' . $key . ']=' . urlencode($value) . '&';
    }
    if ($_REQUEST['start'] == 1) {
        $_SESSION['total_size'] = $GALLERY->get_total_filesize();
    }
    $count = $GALLERY->resize_all_pictures($_REQUEST['start'], $_REQUEST['ROPT']);
    if ($count > 0) {
        $total = get_data_count(TBL_CMS_GALPICS, 'id', "1");
        $done = printMenge((100 / $total) * ($_REQUEST['start'] + 30));
        $done = ($done > 100) ? 100 : $done;
        header('Refresh: 0;  URL=' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=gblresize&start=' . ($_REQUEST['start'] + 30) . '&epage=' . $_REQUEST['epage'] .
            '&' . $urladd);
        $ADMINOBJ->content .= '<div class="page-header"><h1>in Bearbeitung</h1></div><h3>' . $done . '%</h3><br>bitte warten...';
        include (CMS_ROOT . 'admin/inc/footer.inc.php');
        exit;
    }
    else {
        $bytes_free = human_file_size($_SESSION['total_size'] - $GALLERY->get_total_filesize());
        unset($_SESSION['total_size']);
        $GALLERY->msg('{LBLA_SAVED} Eingespart:' . $bytes_free);
        header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=tools');
    }
    exit;
}

if ($_REQUEST['aktion'] == 'genpiccache') {
    if ($_REQUEST['start'] == 1) {
        if ($handle = opendir('../cache/')) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    @unlink('../cache/' . $file);
                    $k++;
                }
            }

        }
        closedir($handle);
    }
    $count = $GAL_OBJ->generate_allthumbs($_REQUEST['start']);
    if ($count > 0) {
        $total = get_data_count(TBL_CMS_GALPICS, 'id', "1");
        $done = printMenge((100 / $total) * ($_REQUEST['start'] + 30));
        $done = ($done > 100) ? 100 : $done;
        header('Refresh: 0;  URL=' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=genpiccache&start=' . ($_REQUEST['start'] + 30) . '&epage=' . $_REQUEST['epage']);
        $ADMINOBJ->content .= '<div class="page-header"><h1>in Bearbeitung</h1></div><h3>' . $done . '%</h3><br>bitte warten...
		<table class="table table-striped table-hover"><tr>
			<% foreach from=$adminpictab item=pic name=gloop %>
			<td><img src="<%$pic.img_src_1%>" ></td>
			 <% if ($smarty.foreach.gloop.iteration % 6 == 0 || $smarty.foreach.gloop.last==TRUE)%></tr><tr><%/if%>
		<%/foreach%>
		</tr></table>
		';
        include (CMS_ROOT . 'admin/inc/footer.inc.php');
    }
    else {
        $GALLERY->msg('{LBLA_SAVED}');
        header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=tools');
    }
    exit;
}


if ($_POST['aktion'] == "a_picval") {
    $VRES['count_nullfiles'] = $GALLERY->delete_files_size_null();
    $VRES['count_fnote'] = $GALLERY->deleteFromDBIfFileNotExists();
    $VRES['count_fs'] = $GALLERY->setAllFileSize();
    $VRES['count_notindb'] = $GALLERY->delete_files_not_in_db();
    $urladd = "";
    foreach ($VRES as $key => $value) {
        $urladd .= 'VRES[' . $key . ']=' . urlencode($value) . '&';
    }
    $GALLERY->msg('{LBLA_SAVED}');
    header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&' . $urladd . 'cmd=tools');
    exit;
}


$menu = array(
    "{LBL_PICMANAGER}" => 'section=start&cmd=initpicman' . (($_REQUEST['gid'] > 0) ? '&gid=' . $_REQUEST['gid'] : ''),
    "{LBLA_GALLERYGROUPS}" => "redirect=run.php?cmd=load_groups&epage=gallery.inc&section=groups&msid=" . md5('{LBLA_GALLERYGROUPS}'),
    "Tools" => "cmd=tools&section=tools",
    "{LA_MODCONFIGURATION}" => "cmd=conf&section=conf",
    );
$ADMINOBJ->set_top_menu($menu);


keimeno_class::add_tpl($ADMINOBJ->content, 'gallery');


$smarty->assign('csfilter', $_SESSION['cs_gal']);
$GALLERY->parse_to_smarty();

?>