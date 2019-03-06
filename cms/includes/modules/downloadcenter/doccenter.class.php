<?php

/**
 * @package    downloadcenter
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


class doccenter_class extends keimeno_class {


    private $doc_center_var = array();
    private $equal_current_dir_working_dir = false;


    /**
     * doccenter_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

    }

    /**
     * doccenter_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->create_submit_btn();
        $this->smarty->assign('doc_center', $this->doc_center_var);
    }

    /**
     * doccenter_class::set_current_directory()
     * 
     * @return
     */
    function set_current_directory() {
        $this->doc_center_var['current_directory'] = $_SESSION['froot_dc'];
    }

    /**
     * doccenter_class::set_path_from_cms()
     * 
     * @return
     */
    function set_path_from_cms() {
        $this->doc_center_var['cms_path_to_current_directory'] = str_replace(FILE_ROOT, "", $_SESSION['froot_dc']);
    }

    /**
     * doccenter_class::create_submit_btn()
     * 
     * @return
     */
    function create_submit_btn() {
        $this->doc_center_var['submit_dir_create'] = kf::gen_admin_sub_btn('{LBL_CREATEDIR}');
        $this->doc_center_var['submit_go'] = kf::gen_admin_sub_btn('GO');
        $this->doc_center_var['submit_save'] = kf::gen_admin_sub_btn('{LBLA_SAVE}');
    }

    /**
     * doccenter_class::equal_dirs()
     * 
     * @return
     */
    function equal_dirs() {
        $this->doc_center_var['froot_greater'] = 0;

        if (strlen($_SESSION['froot_dc']) > strlen($_SESSION['working_root_dc']))
            $this->doc_center_var['froot_greater'] = 1;
        else
            $this->doc_center_var['froot_greater'] = 0;
    }


    /**
     * doccenter_class::show_directory()
     * 
     * @return
     */
    function show_directory() {
        $dh = opendir($_SESSION['froot_dc']);
        $file_array = array();
        $all_dirs_and_files = array();
        if ($dh) {
            while (false !== ($filename = readdir($dh))) {
                if ($filename != '.' && $filename != '..' && is_dir($_SESSION['froot_dc'] . $filename)) {
                    $file_array['dirname'] = $filename;
                    $file_array['perms'] = $this->file_perms($_SESSION['froot_dc'] . $filename, true);
                    $file_array['del_icon'] = kf::gen_del_icon_reload('', 'a_deldir', '{LBL_ALLFILESDELETED}', '&dir=' . $filename);
                    $all_dirs_and_files['all_dirs'][$filename] = $file_array;
                }
            }

        }
        closedir($dh);
        $file_list = array();
        $dh = opendir($_SESSION['froot_dc']);

        if ($dh) {
            while (false !== ($filename = readdir($dh))) {
                if ($filename != '.' && $filename != '..' && is_file($_SESSION['froot_dc'] . $filename)) {
                    $file_list[$filename] = $filename;
                }
            }
        }
        closedir($dh);

        $all_dirs_and_files['all_files'] = $this->get_files_from_dir();
        $this->doc_center_var['all_dirs_and_files'] = $all_dirs_and_files;
        return $this->doc_center_var;
    }


    /**
     * doccenter_class::get_files_from_dir()
     * 
     * @return
     */
    function get_files_from_dir() {
        $file_list = array();
        $dh = opendir($_SESSION['froot_dc']);
        if ($dh) {
            while (false !== ($filename = readdir($dh))) {
                if ($filename != '.' && $filename != '..' && is_file($_SESSION['froot_dc'] . $filename)) {
                    $ext = strtolower(strrchr($filename, '.'));
                    $file['picture'] = '';
                    $file['filename'] = $filename;
                    $file['file_size'] = human_file_size(filesize($_SESSION['froot_dc'] . $filename));
                    $file['permission'] = $this->file_perms($_SESSION['froot_dc'] . $filename, true);
                    $file['create_date'] = date("d.m.Y H:i:s", filemtime($_SESSION['froot_dc'] . $filename));
                    $file['del_icon'] = kf::gen_del_icon(md5($filename), false, 'a_delfile', '', '&datei=' . $filename);
                    $file['path'] = str_replace(FILE_ROOT, "", $_SESSION['froot_dc']) . $filename;
                    if (self::is_image($_SESSION['froot_dc'] . $filename)) {
                        $file['picture'] = '<img src="' . kf::thumb($_SESSION['froot_dc'] . $filename, 90, 60) . '">';
                    }
                    $file_list[] = $file;
                }
            }
        }
        return $file_list;
    }

    /**
     * doccenter_class::clean_db()
     * 
     * @return
     */
    function clean_db() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_DOWNCENTER . " ORDER BY file");
        while ($row = $this->db->fetch_array_names($result)) {
            if (!is_file(FILE_ROOT . $row['file'])) {
                $this->db->query("DELETE FROM " . TBL_CMS_DOWNCENTER . " WHERE id='" . $row['id'] . "' LIMIT 1");
                $this->db->query("DELETE FROM " . TBL_CMS_DC_LOG . " WHERE dcid=" . (int)$row['id']);
            }
        }
    }

    /**
     * doccenter_class::sync()
     * 
     * @param mixed $sess_dir
     * @return
     */
    function sync($sess_dir) {
        $dh = opendir($sess_dir);
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..' && is_file($sess_dir . $filename)) {
                unset($FILE_INFO);
                $FILE_INFO['file'] = str_replace(FILE_ROOT, '', $sess_dir . $filename);
                if (get_data_count(TBL_CMS_DOWNCENTER, 'id', "file='" . $FILE_INFO['file'] . "'") == 0) {
                    $FILE_INFO['size'] = filesize($sess_dir . $filename);
                    $FILE_INFO['last_upload'] = time();
                    insert_table(TBL_CMS_DOWNCENTER, $FILE_INFO);
                    $z++;
                }
            }
        }
        $this->clean_db();
    }

    /**
     * doccenter_class::dc_delete_file()
     * 
     * @param mixed $delfile
     * @param integer $id
     * @return
     */
    function dc_delete_file($delfile, $id = 0) {
        if (is_file($delfile)) {
            if (unlink($delfile)) {
                $FILE_INFO['file'] = str_replace(FILE_ROOT, '', $delfile);
                $FILE_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE file='" . $FILE_INFO['file'] . "' OR id=" . (int)$id . " LIMIT 1");
                if ($FILE_OBJ['icon'] != "" && file_exists(FILE_ROOT . $FILE_OBJ['icon']))
                    unlink(FILE_ROOT . $FILE_OBJ['icon']);
            }
        }
        $this->db->query("DELETE FROM " . TBL_CMS_DOWNCENTER . " WHERE id='" . (int)$FILE_OBJ['id'] . "' LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_DC_LOG . " WHERE dcid=" . (int)$FILE_OBJ['id']);
    }

    /**
     * doccenter_class::delDirWithSubDirs()
     * 
     * @param mixed $dir
     * @return
     */
    function delDirWithSubDirs($dir) {
        if (is_dir($dir)) {
            $dir = (substr($dir, -1) != "/") ? $dir . "/" : $dir;
            $openDir = opendir($dir);
            while ($file = readdir($openDir)) {
                if (!in_array($file, array(".", ".."))) {
                    if (!is_dir($dir . $file))
                        $this->dc_delete_file($dir . $file);
                    else
                        $this->delDirWithSubDirs($dir . $file);
                }
            }
            closedir($openDir);
            @rmdir($dir);
        }
    }

    /**
     * doccenter_class::file_perms()
     * 
     * @param mixed $file
     * @param bool $octal
     * @return
     */
    function file_perms($file, $octal = false) {
        if (!file_exists($file))
            return false;
        $perms = fileperms($file);
        $cut = $octal ? 2 : 3;
        return substr(decoct($perms), $cut);
    }

    /**
     * doccenter_class::save_file()
     * 
     * @param mixed $FILE_INFO
     * @param integer $id_value
     * @return
     */
    function save_file($FILE_INFO, $id_value = 0) {
        if ($_FILES['datei']['name'] != "") {
            $target_file = $_POST['ftarget'] . $this->format_file_name($_FILES['datei']['name']);
            if ($id_value > 0) {
                $F = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE id=" . (int)$id_value);
                $target_file = $_POST['ftarget'] . $this->unique_filename($_POST['ftarget'], basename($target_file));
            }
            move_uploaded_file($_FILES['datei']['tmp_name'], $target_file);
            chmod($target_file, 0755);
            $FILE_INFO['file'] = str_replace(FILE_ROOT, '', $target_file);
            $FILE_INFO['size'] = filesize($target_file);
            $FILE_INFO['last_upload'] = time();
            if ($id_value == 0) {
                insert_table(TBL_CMS_DOWNCENTER, $FILE_INFO);
            }
            else {
                update_table(TBL_CMS_DOWNCENTER, "id", $id_value, $FILE_INFO);
            }
        }
    }

    /**
     * doccenter_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_DOWNCENTER . " SET approval=" . $this->TCR->GET['value'] . " WHERE id=" . (int)$id . " LIMIT 1");
        $this->hard_exit();
    }

    /**
     * doccenter_class::cmd_a_delfile()
     * 
     * @return
     */
    function cmd_a_delfile() {
        $this->dc_delete_file($_SESSION['froot_dc'] . basename($_GET['datei']));
        $this->sync($_SESSION['froot_dc']);
        $this->ej();
    }

    /**
     * doccenter_class::cmd_delfile_by_id()
     * 
     * @return
     */
    function cmd_delfile_by_id() {
        $F = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE id=" . (int)$_GET['ident']);
        $this->dc_delete_file(FILE_ROOT . $F['file'], $_GET['ident']);
        $this->ej();
    }

    /**
     * doccenter_class::cmd_load_dc_chart()
     * 
     * @return
     */
    function cmd_load_dc_chart() {
        $days = array();
        $daysback = (int)$_GET['days'];
        $daysback = ($daysback == 0) ? 30 : $daysback;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_DC_LOG . " WHERE dcid=" . (int)$_GET['id'] . " ORDER BY dcdate DESC LIMIT " . $daysback);
        while ($row = $this->db->fetch_array_names($result)) {
            $days[$row['dcdate']] = array($row['dcdate'], $row['hits']);
        }

        $x = 0;
        foreach ($days as $date => $row) {
            $x++;
            $data[] = array($x, $row[1]);
            $ticks[] = array($x, date('d', strtotime($row[0])));
        }

        $xaxis = array(
            'ticks' => $ticks,
            'tickDecimals' => 0,
            'tickSize' => 1);
        $yaxis = array('zoomRange' => array(0.1, 10), 'panRange' => array(-10, 10));
        $options = array(
            'xaxis' => $xaxis,
            'grid' => array(
                'hoverable' => 'true',
                'clickable' => true,
                'aboveData' => 'true',
                'color' => '#3f3f3f',
                'autoHighlight' => 'true',
                'axisMargin' => '0',
                'borderWidth' => '0',
                'borderColor' => 'null',
                'minBorderMargin' => '5',
                'mouseActiveRadius' => '100'),
            'lines' => array(
                'show' => 'true',
                'fill' => 'true',
                'lineWidth' => '2'),
            'points' => array(
                'show' => 'true',
                'radius' => '4.5',
                'symbol' => 'circle'));
        $series_list[] = array(
            'label' => 'Downloads',
            'data' => $data,
            'lines' => array('fillColor' => 'rgba(150, 202, 89, 0.12)'),
            'points' => array('fillColor' => '#fff'),
            'color' => '#96CA59');
        echo json_encode(array('serielist' => $series_list, 'foptions' => $options));
        $this->hard_exit();
    }


    /**
     * doccenter_class::cmd_a_file_update()
     * 
     * @return
     */
    function cmd_a_file_update() {
        $FORM = $_POST['FORM'];
        if ($_FILES['datei_icon']['tmp_name'] != "") {
            if (!is_dir(FILE_ROOT . DOWNCENTER))
                mkdir(FILE_ROOT . DOWNCENTER, 0755);
            $target_file = FILE_ROOT . DOWNCENTER . $this->format_file_name('DC_' . $_FILES['datei_icon']['name']);
            if (!validate_upload_file($_FILES['datei_icon'])) {
                $this->msge($_SESSION['upload_msge']);
                header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=mfiles');
                exit;
            }
            move_uploaded_file($_FILES['datei_icon']['tmp_name'], $target_file);
            chmod($target_file, 0755);
            $FORM['icon'] = '/' . DOWNCENTER . $this->format_file_name('DC_' . $_FILES['datei_icon']['name']);
        }
        if ($_FILES['datei']['name'] != '') {
            if (!validate_upload_file($_FILES['datei'])) {
                $this->msge($_SESSION['upload_msge']);
                header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=mfiles');
                exit;
            }
        }

        $this->save_file($_POST['FORM'], $_POST['id']);
        $FORM['description'] = $_POST['FORM']['description'];
        $FORM['title'] = strip_tags($_POST['FORM']['title']);
        update_table(TBL_CMS_DOWNCENTER, 'id', $_POST['id'], $FORM);
        $this->msg('{LBLA_SAVED}');
        HEADER('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=mfiles');
        exit;
    }

    /**
     * doccenter_class::cmd_onceup()
     * 
     * @return
     */
    function cmd_onceup() {
        if (substr($_SESSION['froot_dc'], -1) == '/')
            $_SESSION['froot_dc'] = substr($_SESSION['froot_dc'], 0, strlen($_SESSION['froot_dc']) - 1);

        for ($i = strlen($_SESSION['froot_dc']); $i >= 0; $i--) {
            $flip .= $_SESSION['froot_dc'][$i];
        }
        for ($i = 0; $i < strlen($_SESSION['froot_dc']); $i++) {
            if ($flip[$i] == '/') {
                $found_slash = $i;
                break;
            }
        }
        $_SESSION['froot_dc'] = substr($_SESSION['froot_dc'], 0, strlen($_SESSION['froot_dc']) - $found_slash);
        $this->TCR->redirect('epage=' . $_GET['epage']);
    }

    /**
     * doccenter_class::cmd_start()
     * 
     * @return
     */
    function cmd_start() {
        $_SESSION['froot_dc'] = $_SESSION['working_root_dc'];
    }

    /**
     * doccenter_class::cmd_a_tracking()
     * 
     * @return
     */
    function cmd_a_tracking() {
        $tracking_array = array();
        $FILE_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE id='" . (int)($_GET['id']) . "' LIMIT 1");
        $FILE_OBJ['title'] = (trim($FILE_OBJ['title']) == "") ? $FILE_OBJ['file'] : $FILE_OBJ['title'];
        $this->doc_center_var['track_file'] = $FILE_OBJ;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_DC_LOG . " WHERE dcid=" . (int)$_GET['id'] . " ORDER BY dcdate DESC");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['dcdate'] = my_date('d.m.Y', $row['dcdate']);
            $row['hits'] = $row['hits'];
            $tracking_array[] = $row;
        }
        $this->doc_center_var['tracking'] = $tracking_array;
    }

    //upload a file
    /**
     * doccenter_class::cmd_a_fileupload()
     * 
     * @return
     */
    function cmd_a_fileupload() {
        if ($_POST['ftarget'] == "")
            return;

        if ($_FILES['datei']['name'] == '') {
            $this->msge("{ERR_NOFILE}");
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
            exit;
        }

        if (!validate_upload_file($_FILES['datei'])) {
            $this->msge($_SESSION['upload_msge']);
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=mfiles');
            exit;
        }
        $target_file = $this->format_file_name($_FILES['datei']['name']);
        $fname = str_replace(FILE_ROOT, '', $_POST['ftarget'] . $target_file);
        $id_value = (int)$this->get_value_from_table(TBL_CMS_DOWNCENTER, "id", "file='" . $fname . "'");
        $this->save_file($_POST['FORM'], $id_value);
        $this->msg($msgback . " {LBL_UPDATED}.");
        header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
        exit;
    }

    //delete Directory
    /**
     * doccenter_class::cmd_a_deldir()
     * 
     * @return
     */
    function cmd_a_deldir() {
        if ($_GET['dir'] == "")
            return;
        $deldir = $_SESSION['froot_dc'] . basename($_GET['dir']) . '/';
        if (strlen($deldir) > strlen($_SESSION['working_root_dc']) && is_dir($deldir))
            $this->delDirWithSubDirs($deldir);
        $this->sync($_SESSION['froot_dc']);
        $this->msg(basename($deldir) . ' {LBLA_DELETED}');
        HEADER('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
        exit;
    }

    //Delete more files
    /**
     * doccenter_class::cmd_a_massdel()
     * 
     * @return
     */
    function cmd_a_massdel() {
        if (count($_POST['files']) <= 0)
            return;

        foreach ($_POST['files'] as $key => $datei) {
            $delfile = $_SESSION['froot_dc'] . basename($datei);
            $this->dc_delete_file($delfile);
            $k++;
        }
        $this->sync($_SESSION['froot_dc']);
        $this->msg($k . ' {LBL_FILES} {LBLA_DELETED}');
        HEADER('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
        exit;
    }

    /**
     * doccenter_class::cmd_mfiles()
     * 
     * @return
     */
    function cmd_mfiles() {
        $mfiles_array = array();
        $result = $this->db->query("SELECT D.*,SUM(L.hits) AS TOTAL, D.id AS DID FROM " . TBL_CMS_DOWNCENTER . " D LEFT JOIN " . TBL_CMS_DC_LOG .
            " L ON (D.id = L.dcid) GROUP BY D.file ORDER BY D.morder");
        while ($row = $this->db->fetch_array_names($result)) {
            $file['title'] = $row['title'];
            $file['TOTAL'] = (int)$row['TOTAL'];
            $file['file'] = $row['file'];
            $file['id'] = $row['id'];
            $file['morder'] = $row['morder'];
            $file['approval_icon'] = kf::gen_approve_icon($row['id'], $row['approval']);
            $file['DID'] = $row['DID'];
            $file['download_link'] = basename($row['file']);
            $file['download_url'] = gen_download_url($row['id']);
            $file['del_img_tag_icon'] = kf::gen_edit_icon($row['id']);
            $file['stat_icon'] = kf::gen_chart_icon($row['id']);
            $file['del_img_adm_confirm'] = kf::gen_del_icon($row['DID'], false, 'delfile_by_id');
            $file['del_icon'] = kf::gen_edit_icon($row['id']);
            $mfiles_array[] = $file;
        }

        $this->doc_center_var['mfiles'] = $mfiles_array;
    }

    //create Directory
    /**
     * doccenter_class::cmd_a_cdir()
     * 
     * @return
     */
    function cmd_a_cdir() {
        if ($_POST['cdir'] == "") {
            $this->TCR->redirect('epage=' . $_REQUEST['epage']);
        }
        else {
            if (!is_dir($_SESSION['froot_dc'] . $_POST['cdir']))
                mkdir($_SESSION['froot_dc'] . $_POST['cdir'], 0755);
            $this->msg($_POST['cdir'] . " wurde angelegt.");
            header('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage']);
            exit;
        }
    }

    /**
     * doccenter_class::cmd_dc_down()
     * 
     * @return
     */
    function cmd_dc_down() {
        $this->direct_download(FILE_ROOT . $_GET['id']);
    }


    /**
     * doccenter_class::cmd_enter()
     * 
     * @return
     */
    function cmd_enter() {
        if ($_GET['dir'] == "")
            $this->TCR->redirect('epage=' . $_REQUEST['epage']);
        $_SESSION['froot_dc'] .= $_GET['dir'] . '/';
        $this->TCR->redirect('epage=' . $_REQUEST['epage']);
    }


    /**
     * doccenter_class::cmd_sync()
     * 
     * @return
     */
    function cmd_sync() {
        $tab = $this->sync($_SESSION['froot_dc']);
    }


    /**
     * doccenter_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave() {
        if (count($_POST['morder']) > 0) {

            $tosort = array();
            foreach ($_POST['morder'] as $id => $num) {
                $tosort[] = array('id' => $id, 'morder' => $num);
            }
            $tosort = $this->sort_multi_array($tosort, 'morder', SORT_ASC, SORT_NUMERIC);
            $k = 0;
            foreach ($tosort as $row) {
                $k += 10;
                update_table(TBL_CMS_DOWNCENTER, 'id', $row['id'], array('morder' => $k));
            }
        }
        $this->msg('{LBLA_SAVED}');
        HEADER('location: ' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=mfiles');
        exit;
    }


    /**
     * doccenter_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_DOWNCENTER . " WHERE id='" . $_GET['id'] . "' LIMIT 1");
        $FORM['ftarget'] = FILE_ROOT . str_replace(basename($FORM['file']), '', $FORM['file']);
        # $FORM['icon_gen_thumb_picture'] = (($FORM['icon'] != "") ? '<img src="' . kf::thumb($FORM['icon'], 100, 100) . '" >' : '');
        $FORM['file_gen_thumb_picture'] = ((self::is_image(FILE_ROOT . $FORM['file']) != "") ? '<img src="' . kf::thumb(FILE_ROOT . $FORM['file'], 100, 100) . '" >' :
            '');
        $FORM['editor'] = create_html_editor('FORM[description]', $FORM['description'], 300, 'Basic');
        $FORM['gen_submit_admin'] = kf::gen_admin_sub_btn('{LBLA_SAVE}');
        $this->doc_center_var['FORM'] = $FORM;
    }

}