<?php


/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


include (CMS_ROOT . 'admin/inc/webex.class.php');

$WEBEX = new webex_class();

$folders = array("{LBLA_WEBSPACE}" => 'file_server', #  "{LBL_JSMODULE}" => 'js',
        #	"{LBLA_CMSSAPCE}"		=> 'www'
    );
$WEBEX->add_folders($folders);
$WEBEX->set_folder($_GET);

if ($_GET['aktion'] == 'analyze_dirs') {

    if ($_GET['dir'] == '') {
        $_GET['dir'] = 'fs';
    }
    if ($_GET['dir'] == 'fs') {
        $tree = $WEBEX->analyze_dirs(CMS_ROOT . 'file_server');
    }
    if ($_GET['dir'] == 'img') {
        $tree = $WEBEX->analyze_dirs(CMS_ROOT . 'images');
    }
    if ($_GET['dir'] == 'js') {
        $tree = $WEBEX->analyze_dirs(CMS_ROOT . 'js');
    }
    if ($_GET['dir'] == 'fd') {
        $tree = $WEBEX->analyze_dirs(CMS_ROOT . 'file_data');
    }
    if ($_GET['dir'] == 'db') {
        $tree = $WEBEX->analyze_dirs(CMS_ROOT . 'admin/db_backup');
    }

    $analyzer = array(
        'file_tree' => $tree,
        'total_fs' => $WEBEX->total_fs,
        'total_fs_dir' => $WEBEX->total_fs_dir,
        'count_dirs' => count($WEBEX->total_fs_dir),
        'count_files' => $WEBEX->total_file_count,
        'file_extentions' => $WEBEX->file_extentions);
    $smarty->assign('analyzer', $analyzer);
}

$ADMINOBJ->inc_tpl('webexplorer');
