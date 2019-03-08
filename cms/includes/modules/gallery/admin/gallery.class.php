<?php

/**
 * @package    gallery
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.3
 */


class gallery_class extends modules_class {

    var $std_lang = 1;
    var $gallery_obj = array();


    /**
     * gallery_class::__construct()
     * 
     * @return void
     */
    function __construct() {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
        $this->std_lang = $this->gbl_config['std_lang_id'];
        $this->CMSGALLERY = new gal_class();
    }

    /**
     * gallery_class::cmd_a_randpic()
     * 
     * @return void
     */
    function cmd_a_randpic() {
        $this->unset_title_image($_GET['id']);
        $this->CMSGALLERY->msg("{LBLA_SAVED}");
        $this->ej();
    }


    /**
     * gallery_class::cmd_clearcache()
     * 
     * @return
     */
    function cmd_clearcache() {
        $CR = new crj_class();
        $CR->cleanImageCache(0);
        unset($CR);
        $this->hard_exit();
    }

    /**
     * gallery_class::cmd_axdelpic()
     * 
     * @return
     */
    function cmd_axdelpic() {
        $this->deleteFotoById($_GET['id']);
        $this->hard_exit();
    }

    /**
     * gallery_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->GALADMIN['cs_gal'] = $_SESSION['cs_gal'];
        $this->GALADMIN['flickractive'] = class_exists('flickr_master_class') && $this->gblconfig->fli_token != "";
        $this->GALADMIN['gobtn'] = gen_submit_btn(' - GO - ');
        $this->GALADMIN['albumcount'] = get_data_count(TBL_CMS_GALGROUP, 'id', "1");
        $this->smarty->assign('GALADMIN', $this->GALADMIN);
    }

    /**
     * gallery_class::cmd_post_to_flickr()
     * 
     * @return
     */
    function cmd_post_to_flickr() {
        $flickr = new flickr_master_class();
        $FOTO = $_POST['FORM'];
        $FL = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $FOTO['picid']);
        $FOTO['file'] = CMS_ROOT . 'images/gallery/' . $FL['pic_name'];
        $flickr->flickr_upload($FOTO);
        $this->msg('{LA_SEND}');
        $this->echo_json_fb('flickrsend');
    }


    /**
     * gallery_class::cmd_save_groups()
     * 
     * @return
     */
    function cmd_save_groups() {
        foreach ($_POST['FORM'] as $key => $row) {
            $row['g_createdate'] = strtotime($row['g_createdate']);
            update_table(TBL_CMS_GALGROUP, 'id', $key, $row);
        }
        $this->rebuild_page_index();
        $data = $this->sort_multi_array((array )$_POST['ORDER'], 'g_order', SORT_ASC, SORT_NUMERIC);
        $k = 0;
        foreach ($data as $key => $row) {
            $k += 10;
            $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET g_order=" . $k . " WHERE id = " . $row['id']);
        }
        $this->echo_json_fb();
    }

    /**
     * gallery_class::cmd_change_watermark()
     * 
     * @return
     */
    function cmd_change_watermark() {
        $CR = new crj_class();
        $CR->cleanImageCache(0);
        unset($CR);
        move_uploaded_file($_FILES['datei']['tmp_name'], CMS_ROOT . 'images/watermark.png');
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * gallery_class::delete_gallery_group()
     * 
     * @param mixed $id
     * @return
     */
    function delete_gallery_group($id) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE group_id=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->deleteFotoById($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_GALGROUP . " WHERE id=" . $id . " LIMIT 1");
        $this->remove_from_page_index('gallery', $id, $this->gblconfig->gal_path);
    }

    /**
     * gallery_class::cmd_deletegroup()
     * 
     * @return
     */
    function cmd_deletegroup() {
        $id = (int)$_GET['ident'];
        if (get_data_count(TBL_CMS_GALGROUP, 'id', "parent=" . $id) > 0) {
            $this->msge('{LBLA_NOT_DELETED} {LBL_HASSUBCONTENT}');
            $this->ej();
        }
        $this->delete_gallery_group($id);
        $this->msg('{LBLA_DELETED}');
        $this->ej();
    }

    /**
     * gallery_class::cmd_delete_gallery_by_node()
     * 
     * @return
     */
    function cmd_delete_gallery_by_node() {
        $this->delete_gallery_group($_GET['id']);
        $this->hard_exit();
    }


    /**
     * gallery_class::delete_files_size_null()
     * 
     * @return
     */
    function delete_files_size_null() {
        $i = 0;
        $dh = opendir(PICS_GAL_ROOT_ADMIN);
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..') {
                if (filesize(PICS_GAL_ROOT_ADMIN . $filename) == 0 && file_exists(PICS_GAL_ROOT_ADMIN . $filename)) {
                    delete_file(PICS_GAL_ROOT_ADMIN . $filename);
                    $i++;
                }
            }
        }
        $this->LOGCLASS->addLog('DELETE', $i . ' "not in DB" fixed');
        return $i;
    }

    /**
     * gallery_class::delete_files_not_in_db()
     * 
     * @return
     */
    function delete_files_not_in_db() {
        $i = 0;
        $dh = opendir(PICS_GAL_ROOT_ADMIN);
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..') {
                if (get_data_count(TBL_CMS_GALPICS, 'id', "pic_name='" . $filename . "'") == 0) {
                    delete_file(PICS_GAL_ROOT_ADMIN . $filename);
                    $i++;
                }
            }
        }
        $this->LOGCLASS->addLog('DELETE', $i . ' "not in DB" fixed');
        return $i;
    }

    /**
     * gallery_class::deleteFromDBIfFileNotExists()
     * 
     * @return
     */
    function deleteFromDBIfFileNotExists() {
        $i = 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALPICS . " 	");
        while ($row = $this->db->fetch_array_names($result)) {
            $fname = PICS_GAL_ROOT_ADMIN . $row['pic_name'];
            if (!file_exists($fname)) {
                $this->deleteFotoSQL($row['id']);
                $i++;
            }
        }
        $this->LOGCLASS->addLog('DELETE', $i . ' "file not exists" fixed');
        return $i;
    }

    /**
     * gallery_class::setAllFileSize()
     * 
     * @return
     */
    function setAllFileSize() {
        $i = 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE pic_width=0");
        while ($row = $this->db->fetch_array_names($result)) {
            $FORM = array();
            if (file_exists(PICS_GAL_ROOT_ADMIN . $row['pic_name'])) {
                list($FORM['pic_width'], $FORM['pic_height']) = getimagesize(PICS_GAL_ROOT_ADMIN . $row['pic_name']);
                update_table(TBL_CMS_GALPICS, 'id', $row['id'], $FORM);
                $i++;
            }
        }
        $this->LOGCLASS->addLog('UPDATE', $i . ' fotos filesize set');
        return $i;
    }

    /**
     * gallery_class::format_pic_name()
     * 
     * @param mixed $str
     * @return
     */
    function format_pic_name($str) {
        $str = strtolower($str);
        $str = preg_replace('/\s+/', '', $str); //entfernt zeilenumbrueche, whitespace
        return preg_replace('/[^0-9a-z?-????\`\~\!\@\#\$\%\^\*\; \,\.\'\/\_\-]/i', '_', $str);
    }


    /**
     * gallery_class::insert_to_db()
     * 
     * @param mixed $f_name
     * @param mixed $FORM
     * @param integer $id
     * @return
     */
    function insert_to_db($f_name, $FORM, $id = 0) {
        chmod(PICS_GAL_ROOT_ADMIN . $f_name, 0755);
        $fileParts = pathinfo($f_name);
        $FORM['pic_size'] = filesize(PICS_GAL_ROOT_ADMIN . $f_name);
        if (!isset($FORM['post_time_int']))
            $FORM['post_time_int'] = time();
        $FORM['pic_ext'] = $fileParts['extension'];
        $FORM['pic_name'] = $f_name;
        $FORM['ip'] = REAL_IP;
        $FORM['pic_agree'] = $this->gbl_config['cs_autoalowed'];
        $FORM['approved'] = $this->gbl_config['cs_autoalowed'];
        $FORM['pic_haswm'] = 0;
        if ($id == 0) {
            $id = insert_table(TBL_CMS_GALPICS, $FORM);
        }
        else
            update_table(TBL_CMS_GALPICS, 'id', $id, $FORM);
        return array('id' => $id, 'FORM' => $FORM);
    }

    /**
     * gallery_class::manupulate_pic()
     * 
     * @param mixed $id
     * @param mixed $FORM
     * @return
     */
    function manupulate_pic($id, $FORM) {
        if ($this->gbl_config['gal_autoresize'] == 1) {
            $this->resize_single_picture($FORM, array('maxwidth' => $this->gbl_config['gal_maxorg_width'], 'maxheight' => $this->gbl_config['gal_maxorg_height']));
        }
    }

    /**
     * gallery_class::uploadPicAndSave()
     * 
     * @param mixed $FILES
     * @param mixed $id
     * @param mixed $FORM
     * @return
     */
    function uploadPicAndSave($FILES, $id, $FORM) {
        $id = intval($id);
        $msge = "";
        if ($FILES['attfile']['name'] != "") {
            if (!is_dir(PICS_GAL_ROOT_ADMIN))
                mkdir(PICS_GAL_ROOT_ADMIN, 0775);
            $f_name = $this->format_pic_name($FILES['attfile']['name']);

            $msge = $this->validate_file($_FILES, 'attfile');
            if ($msge == "") {
                if ($id > 0) {
                    $pic_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $id . " LIMIT 1");
                    delete_file(PICS_GAL_ROOT_ADMIN . $pic_obj['pic_name']);
                }

                $f_name = $this->unique_filename(PICS_GAL_ROOT_ADMIN, $f_name);

                if (move_uploaded_file($FILES['attfile']['tmp_name'], PICS_GAL_ROOT_ADMIN . $f_name)) {

                    $RET = $this->insert_to_db($f_name, $FORM, $id);
                    $id = $RET['id'];
                    $this->LOGCLASS->addLog('UPLOAD', 'foto ' . $f_name);
                    $this->manupulate_pic($id, $RET['FORM']);
                }
            }
        }
        return array('msge' => $msge, 'id' => $id);
    }


    /**
     * gallery_class::validate_file()
     * 
     * @param mixed $FILES
     * @return
     */
    function validate_file($FILES, $name) {
        $msge = "";
        if (!validate_upload_file($FILES[$name], true)) {
            $msge .= $_SESSION['upload_msge'];
        }
        switch ((int)$FILES[$name]['error']) {
            case UPLOAD_ERR_INI_SIZE;
                $msge .= 'Die hochgeladene Datei überschreitet die in der Anweisung upload_max_filesize in php.ini festgelegte Größe. ';
                break;
            case UPLOAD_ERR_NO_FILE;
                $msge .= 'Es wurde keine Datei hochgeladen. ';
                break;
            case UPLOAD_ERR_EXTENSION;
                $msge .= 'Unerlaubte Dateiendung. ';
                break;
            case UPLOAD_ERR_PARTIAL;
                $msge .= ' Die Datei wurde nur teilweise hochgeladen. ';
                break;
        }
        return $msge;
    }

    /**
     * gallery_class::cmd_dragdropfile_gallery()
     * 
     * @return
     */
    function cmd_dragdropfile_gallery() {
        $msge = $this->validate_file($_FILES, 'bilddatei');

        if ($msge != "") {
            echo json_encode(array('status' => 'failed', 'filename' => $_FILES['bilddatei']['name'] . $msge));
            $this->hard_exit();
        }

        $newfilename = $this->unique_filename(PICS_GAL_ROOT_ADMIN, $_FILES['bilddatei']['name']);
        if (move_uploaded_file($_FILES['bilddatei']['tmp_name'], PICS_GAL_ROOT_ADMIN . $newfilename)) {
            chmod(PICS_GAL_ROOT_ADMIN . $newfilename, 0755);
            list($pic_width, $pic_height) = getimagesize(PICS_GAL_ROOT_ADMIN . $newfilename);
            $arr = array(
                'group_id' => $_GET['gid'],
                'pic_agree' => 1,
                'pic_width' => $pic_width,
                'pic_height' => $pic_height,
                'fotoquelle' => $this->gbl_config['adr_firma'],
                'pic_title' => basename($_FILES['bilddatei']['name']));
            $arr = $this->real_escape($arr);
            $RET = $this->insert_to_db($newfilename, $arr);
            $id = $RET['id'];
            $this->LOGCLASS->addLog('UPLOAD', 'foto ' . $f_name);
            $this->manupulate_pic($id, $RET['FORM']);
            $this->sortPictures($_GET['gid'], 'morder', 'ASC');
        }
        echo json_encode(array('status' => 'ok', 'filename' => $_FILES['bilddatei']['name']));
        $this->hard_exit();
    }

    /**
     * gallery_class::save_group_content()
     * 
     * @param mixed $FORM_CON
     * @param mixed $FORM_CON_ID
     * @return
     */
    function save_group_content($FORM_CON, $FORM_CON_ID) {
        if ($FORM_CON_ID > 0)
            update_table(TBL_CMS_GLGRCON, 'id', $FORM_CON_ID, $FORM_CON);
        else
            $FORM_CON_ID = insert_table(TBL_CMS_GLGRCON, $FORM_CON);
        $this->add_group_pageindex($FORM_CON['g_title'], $FORM_CON['g_id'], $FORM_CON['lang_id']);
        $this->rebuild_page_index();
        return $FORM_CON_ID;
    }

    /**
     * gallery_class::deleteFotoSQL()
     * 
     * @param mixed $id
     * @return
     */
    function deleteFotoSQL($id) {
        $this->db->query("DELETE FROM " . TBL_CMS_GALPICS . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_GALCON . " WHERE pic_id=" . $id . " LIMIT 1");
        $this->remove_from_page_index('gallery', $id, $this->gblconfig->gal_path_image);
    }

    /**
     * gallery_class::deleteFotoById()
     * 
     * @param mixed $id
     * @return
     */
    function deleteFotoById($id) {
        $id = intval($id);
        $pic_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $id . " LIMIT 1");
        delete_file(PICS_GAL_ROOT_ADMIN . $pic_obj['pic_name']);
        $this->deleteFotoSQL($id);
        $this->LOGCLASS->addLog('DELETED', $pic_obj['pic_name'] . ' deleted');
        return true;
    }

    /**
     * gallery_class::human_file_size()
     * 
     * @param mixed $size
     * @return
     */
    function human_file_size($size) {
        $filesizename = array(
            " Bytes",
            " KB",
            " MB",
            " GB",
            " TB",
            " PB",
            " EB",
            " ZB",
            " YB");
        if ($size > 0)
            return round($size / pow(1024, ($i = floor(log($size, 1024)))), 2) . $filesizename[$i];
        else
            return 0 . 'Bytes';
    }

    /**
     * gallery_class::massDelete()
     * 
     * @param mixed $fotoids
     * @return
     */
    function massDelete($fotoids) {
        $k = 0;
        if (count($fotoids) > 0 && is_array($fotoids)) {
            foreach ($fotoids as $key => $id) {
                if ($this->deleteFotoById($id) === true)
                    $k++;
            }
        }
        $this->LOGCLASS->addLog('DELETE', $k . ' fotos');
        return $k;
    }

    /**
     * gallery_class::sortPictures()
     * 
     * @param mixed $gallery_id
     * @param mixed $column
     * @param mixed $direction
     * @return
     */
    function sortPictures($gallery_id, $column, $direction) {
        $i = 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE group_id=" . intval($gallery_id) . "	ORDER BY " . $column . " " . $direction);
        while ($row = $this->db->fetch_array_names($result)) {
            $i += 10;
            $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET morder=" . $i . " WHERE id=" . $row['id'] . " LIMIT 1");
        }
        $this->LOGCLASS->addLog('UPDATE', 'fotos sorted to ' . $column);
    }

    /**
     * gallery_class::saveSort()
     * 
     * @param mixed $morder
     * @return
     */
    function saveSort($morder) {
        $i = 0;
        if (count($morder) > 0 && is_array($morder)) {
            asort($morder);
            foreach ($morder as $key => $wert) {
                $i += 10;
                $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET morder=" . $i . " WHERE id=" . $key . " LIMIT 1");
            }
        }
    }

    /**
     * gallery_class::set_title_image()
     * 
     * @param mixed $galid
     * @param mixed $picid
     * @return
     */
    function set_title_image($galid, $picid) {
        $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET picid=" . (int)$picid . " WHERE id=" . (int)$galid . " LIMIT 1");
    }

    /**
     * gallery_class::unset_title_image()
     * 
     * @param mixed $galid
     * @return
     */
    function unset_title_image($galid) {
        $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET picid=0 WHERE id=" . (int)$galid . " LIMIT 1");
    }

    /**
     * gallery_class::cmd_approvepic()
     * 
     * @return
     */
    function cmd_approvepic() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->approvePic((int)$id, (int)$_GET['value']);
        $this->hard_exit();
    }

    /**
     * gallery_class::approvePic()
     * 
     * @param mixed $id
     * @param mixed $value
     * @return
     */
    function approvePic($id, $value) {
        $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET approved=" . $value . " WHERE id=" . intval($id) . " LIMIT 1");
    }

    /**
     * gallery_class::movePicsToCat()
     * 
     * @param mixed $fotoids
     * @param mixed $group_id
     * @return
     */
    function movePicsToCat($fotoids, $group_id) {
        $i = 0;
        if (count($fotoids) > 0 && is_array($fotoids)) {
            foreach ($fotoids as $key => $wert) {
                $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET group_id='" . $group_id . "' WHERE id='" . $wert . "'");
                $i++;
            }
        }
        $this->LOGCLASS->addLog('MOVE', $i . ' fotos to ' . $group_id);
    }

    /**
     * gallery_class::approveMass()
     * 
     * @param mixed $fotoids
     * @param mixed $aktion
     * @return
     */
    function approveMass($fotoids, $aktion) {
        $sql_or = "";
        if (count($fotoids) > 0 && is_array($fotoids)) {
            foreach ($fotoids as $key => $id) {
                $sql_or .= (($sql_or != "") ? ' OR ' : '') . 'id=' . $id;
            }
            if ($sql_or != "")
                $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET approved='" . (($aktion == 'a_app') ? 1 : 0) . "' WHERE " . $sql_or);
        }
    }

    /**
     * gallery_class::buildATree()
     * 
     * @param mixed $nodes
     * @param integer $node_id
     * @param bool $show_root
     * @return
     */
    function buildATree($nodes, $node_id = 0, $show_root = false) {
        $nodes->allowed_treeids = array();
        $nodes->create_result_and_array("SELECT id, parent, groupname,approval FROM " . TBL_CMS_GALGROUP . "  ORDER BY parent,groupname", 0, 0, -1);
        return $nodes->output_as_admin_gal_nav($node_id, true, $show_root);
    }

    /**
     * gallery_class::add_pic_count()
     * 
     * @param mixed $tree
     * @return
     */
    function add_pic_count(&$tree) {
        foreach ($tree as $key => $item) {
            $tree[$key]['piccount'] = get_data_count(TBL_CMS_GALPICS, "*", "group_id=" . $item['id']);
            if (is_array($item['children'])) {
                $tree[$key]['children'] = $this->add_pic_count($item['children']);
            }
        }
        return $tree;
    }

    /**
     * gallery_class::load_tree()
     * 
     * @return
     */
    function load_tree() {
        $this->NESTED_ARR->label_column = 'groupname';
        $this->NESTED_ARR->label_id = 'id';
        $this->NESTED_ARR->sign = '|_';
        $this->NESTED_ARR->label_parent = 'parent';
        $this->NESTED_ARR->create_result_and_array("SELECT * FROM " . TBL_CMS_GALGROUP . " WHERE 1 ORDER BY groupname", 0, 0, -1);
        $this->GALADMIN['tree_select'] = $this->NESTED_ARR->outputtree_select();
        $this->GALADMIN['tree'] = $this->add_pic_count($this->NESTED_ARR->menu_array);
        # echoarr($this->FMRVIDEO['tree_select'] );
    }

    /**
     * gallery_class::genEditImgTag()
     * 
     * @param mixed $id
     * @param string $toadd
     * @param string $a
     * @param string $idc
     * @return
     */
    function genEditImgTag($id, $toadd = '', $a = 'edit', $idc = 'id') {
        return '<a title="{LBLA_EDIT}" class="btn btn-default" href="' . $_SERVER['PHP_SELF'] . '?' . $idc . '=' . $id . '&cmd=' . $a . $toadd .
            '"><span class="glyphicon glyphicon-pencil" ><!----></span></a>';
    }

    /*  function gen_del_img_tagConfirm($id, $akt = 'a_del', $confirm = '{LBL_CONFIRM}', $toadd = '') {
    return '<a ' . gen_java_confirm($confirm) . ' title="{LBL_DELETE}" href="' . $_SERVER['PHP_SELF'] . '?id=' . $id . $toadd . '&aktion=' . $akt .
    '"><span class="glyphicon glyphicon-pencil" ><!----></span></a>';
    }
    */

    /*  function genApproveImgTag_old($id, $value, $add = '', $siteurl = '', $akt = 'a_approve') {
    if ($siteurl == "")
    $siteurl = $_SERVER['PHP_SELF'];
    if ($value == 1) {
    return "<a href=\"" . $siteurl . "?aktion=" . $akt . "&value=0&id=" . $id . $add . "\"><img title=\"{LBLA_APPROVED}\" src=\"./images/page_visible.png\" ></a>";
    }
    else {
    return "<a href=\"" . $siteurl . "?aktion=" . $akt . "&value=1&id=" . $id . $add . "\"><img title=\"{LBLA_NOTAPPROVED}\" src=\"./images/page_notvisible.png\" ></a>";
    }
    }*/

    /**
     * gallery_class::getApprovedPicCountOfGallery()
     * 
     * @param mixed $gallery_id
     * @return
     */
    function getApprovedPicCountOfGallery($gallery_id) {
        $result = $this->db->query("SELECT COUNT(A.id) FROM " . TBL_CMS_GALPICS . " A 	WHERE A.approved=1 AND A.group_id=" . intval($gallery_id));
        while ($row = $this->db->fetch_array($result)) {
            $count = $row[0];
        }
        return intval($count);
    }

    /**
     * gallery_class::getAllPicCountOfGallery()
     * 
     * @param mixed $gallery_id
     * @return
     */
    function getAllPicCountOfGallery($gallery_id) {
        $result = $this->db->query("SELECT COUNT(A.id) FROM " . TBL_CMS_GALPICS . " A 	WHERE A.group_id=" . intval($gallery_id));
        while ($row = $this->db->fetch_array($result)) {
            $count = $row[0];
        }
        return intval($count);
    }

    /**
     * gallery_class::getDisApprovedPicCountOfGallery()
     * 
     * @param mixed $gallery_id
     * @return
     */
    function getDisApprovedPicCountOfGallery($gallery_id) {
        $result = $this->db->query("SELECT COUNT(A.id) FROM " . TBL_CMS_GALPICS . " A 	WHERE A.approved=0 AND A.group_id=" . intval($gallery_id));
        while ($row = $this->db->fetch_array($result)) {
            $count = $row[0];
        }
        return intval($count);
    }

    /**
     * gallery_class::get_total_filesize()
     * 
     * @return
     */
    function get_total_filesize() {
        $result = $this->db->query("SELECT SUM(A.pic_size) FROM " . TBL_CMS_GALPICS . " A 	WHERE 1");
        while ($row = $this->db->fetch_array($result)) {
            $tfilesize = $row[0];
        }
        return $tfilesize;
    }

    /**
     * gallery_class::getGalleryFileSize()
     * 
     * @param mixed $gallery_id
     * @return
     */
    function getGalleryFileSize($gallery_id) {
        $result = $this->db->query("SELECT SUM(A.pic_size) FROM " . TBL_CMS_GALPICS . " A 	WHERE A.group_id=" . intval($gallery_id));
        while ($row = $this->db->fetch_array($result)) {
            $count = $row[0];
        }
        return $this->human_file_size($count);
    }

    /**
     * gallery_class::watermark()
     * 
     * @param mixed $id
     * @return
     */
    function watermark($id) {
        $id = (int)$id;
        $PIC = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE pic_haswm=0 AND id=" . $id);
        if (file_exists(PICS_GAL_ROOT_ADMIN . $PIC['pic_name'])) {
            clean_cache_like($PIC['pic_name']);
            if (ISADMIN == 1) {
                $this->GRAPHIC_FUNC->watermark(PICS_GAL_ROOT_ADMIN . $PIC['pic_name'], '../images/watermark.png', $this->gbl_config['gal_waterm_pos'], $this->gbl_config['gal_waterm_trans']);
            }
            else {
                $this->GRAPHIC_FUNC->watermark(PICS_GAL_ROOT_ADMIN . $PIC['pic_name'], './images/watermark.png', $this->gbl_config['gal_waterm_pos'], $this->gbl_config['gal_waterm_trans']);
            }
            $this->db->query("UPDATE " . TBL_CMS_GALPICS . " SET pic_haswm=1 WHERE id=" . $id);
        }
    }


    /**
     * gallery_class::resize_single_picture()
     * 
     * @param mixed $PIC_OBJ
     * @param mixed $ROPT
     * @return
     */
    function resize_single_picture($PIC_OBJ, $ROPT) {
        $target_file = PICS_GAL_ROOT_ADMIN . $PIC_OBJ['pic_name'];
        if (file_exists($target_file)) {
            list($owidth, $oheight, $type, $attr) = getimagesize($target_file);
            if ($owidth > (int)$ROPT['maxwidth'] || $oheight > (int)$ROPT['maxheight']) {
                $GRAF = new graphic_class();
                $GRAF->resize_picture_imageick(PICS_GAL_ROOT_ADMIN . $PIC_OBJ['pic_name'], PICS_GAL_ROOT_ADMIN . $PIC_OBJ['pic_name'], $ROPT['maxwidth'], $ROPT['maxheight']);
                $fs = filesize($target_file);
                if (file_exists($target_file) && $fs > 0) {
                    list($owidth, $oheight, $type, $attr) = getimagesize($target_file);
                    $PIC = array(
                        'pic_width' => (int)$owidth,
                        'pic_height' => (int)$oheight,
                        'pic_size' => (int)$fs);
                    update_table(TBL_CMS_GALPICS, 'id', $PIC_OBJ['PICID'], $PIC);
                }
            }
        }
    }

    /**
     * gallery_class::resize_all_pictures()
     * 
     * @param mixed $start
     * @param mixed $ROPT
     * @return
     */
    function resize_all_pictures($start, $ROPT) {
        $result = $this->db->query($sql = "SELECT *,id AS PICID
	FROM " . TBL_CMS_GALPICS . "
	WHERE 1
	ORDER BY id 
	LIMIT " . (int)$start . ",30");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->resize_single_picture($row, $ROPT);
        }
        return $this->db->num_rows($result);
    }

    /**
     * gallery_class::loadGallery()
     * 
     * @param mixed $gallery_id
     * @param integer $th_width
     * @param integer $th_height
     * @param bool $adminuse
     * @param string $th_type
     * @param integer $cs_fullsizew
     * @param integer $cs_fullsizeh
     * @param integer $start
     * @param integer $count
     * @return
     */
    function loadGallery($gallery_id, $th_width = 160, $th_height = 100, $adminuse = true, $th_type = 'resize', $cs_fullsizew = 800, $cs_fullsizeh = 600, $start = 0,
        $count = 0) {
        $gallery = array();
        $k = 0;
        $_SESSION['cs_gal']['approved'] = intval($_SESSION['cs_gal']['approved']);
        if ($adminuse === false) {
            $_SESSION['cs_gal']['approved'] = 1;
        }
        $cs_fullsizew = intval($cs_fullsizew);
        $th_height = intval($th_height);
        $cs_fullsizeh = intval($cs_fullsizeh);
        $th_width = intval($th_width);
        if ($gallery_id > 0) {
            if ($adminuse == true) {
                if ((int)$_SESSION['cs_gal']['approved'] < 2) {
                    $sa = " AND A.approved=" . (int)$_SESSION['cs_gal']['approved'];
                }
                if ((int)$_SESSION['cs_gal']['approved'] == 2) {
                    $sa = "";
                }
            }
            else {
                $sa = " AND A.approved=1 ";
            }

            $sql = "SELECT A.*,A.id AS PICID,C.*,C.id AS GID,GC.pic_content,GC.pic_title AS PICTITLE
		FROM " . TBL_CMS_GALGROUP . " C, 
		" . TBL_CMS_GALPICS . " A LEFT JOIN " . TBL_CMS_GALCON . " GC ON (GC.pic_id=A.id AND GC.lang_id=" . $this->std_lang . ")
		WHERE C.id=A.group_id 		
		" . $sa . " 		
		AND C.id=" . $gallery_id . " 
		GROUP BY A.id 
		ORDER BY A.morder ASC 
		" . (($count > 0) ? " LIMIT " . intval($start) . "," . intval($count) : '') . " ";
            $result = $this->db->query($sql);
            while ($row = $this->db->fetch_array_names($result)) {
                if (!file_exists(PICS_GAL_ROOT_ADMIN . $row['pic_name']))
                    $row['pic_name'] = 'gal_defekt.jpg';
                if (file_exists(PICS_GAL_ROOT_ADMIN . $row['pic_name']))
                    list($width_foto_px, $height_foto_px) = getimagesize(PICS_GAL_ROOT_ADMIN . $row['pic_name']);
                $sm_arr = array(
                    'pic_name' => $row['pic_name'],
                    'pic_kid' => $row['pic_kid'],
                    'width_foto_px' => $width_foto_px,
                    'height_foto_px' => $height_foto_px,
                    'counter' => $z,
                    'filesize' => $this->human_file_size($row['pic_size']),
                    'edit_link' => $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&section=edit&cmd=edit&gid=' . $row['GID'] . '&id=' . $row['PICID'],
                    'icon_edit' => $this->genEditImgTag($row['PICID'], '&section=edit&epage=' . $_GET['epage'] . '&gid=' . $row['GID']),
                    'icon_approve' => kf::gen_approve_icon($row['PICID'], $row['approved'], 'approvepic'),
                    # $this->genApproveImgTag($row['PICID'], $row['approved'], '&epage=' . $_GET['epage'] . '&gid=' . $row['GID'], '', 'a_approvepic'),
                    'imginfo' => $row,
                    'img_descshort' => $description,
                    'img_fullsize' => SSL_PATH_SYSTEM . PATH_SHOP . PICS_GAL_ROOT_ADMIN . $row['pic_name'],
                    'img_id' => $row['PICID'],
                    'img_descplain' => htmlspecialchars(strip_tags($row['pic_content'])),
                    'img_groupident' => str_replace(array(
                        "\r",
                        "\n",
                        "\t",
                        " "), "", strip_tags($row['groupname'])),
                    'img_description' => str_replace(array(
                        "\r",
                        "\n",
                        "\t"), "", htmlspecialchars($row['pic_content'])),
                    'img_descriptionplain' => strip_tags(str_replace(array(
                        "\r",
                        "\n",
                        "\t"), "", ($row['pic_content']))),
                    'img_copyright' => htmlspecialchars($row['fotoquelle']),
                    'img_title' => htmlspecialchars($row['PICTITLE']),
                    'img_posttime' => date('d.m.Y', $row['post_time_int']),
                    'img_posttimeclock' => date('H:i', $row['post_time_int']),
                    );
                if ($adminuse === true) {
                    $sm_arr['thumbnail'] = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT_ADMIN . $row['pic_name'], $th_width, $th_height, 'admin/' . CACHE, true, $th_type);
                    $sm_arr['preview'] = './' . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT_ADMIN . $row['pic_name'], 300, 200, 'admin/' . CACHE, true, 'resize');
                }
                else {
                    $sm_arr['thumbnail'] = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT_ADMIN . $row['pic_name'], $th_width, $th_height, './' . CACHE, true, $th_type);
                    $sm_arr['img_redfullsize'] = PATH_CMS . CACHE . $this->GRAPHIC_FUNC->makeThumb(PICS_GAL_ROOT_ADMIN . $row['pic_name'], $cs_fullsizew, $cs_fullsizeh, './' .
                        CACHE, true, 'resize');
                }
                $gallery[$row['PICID']] = $sm_arr;
                $total_size += $row['pic_size'];
                $groupname = $row['groupname'];
                $k++;
            }
        }

        $this->pic_count_all = $this->getAllPicCountOfGallery($gallery_id);
        $this->pic_count_appall = $this->getApprovedPicCountOfGallery($gallery_id);
        $this->pic_count_disappall = $this->getDisApprovedPicCountOfGallery($gallery_id);

        if ($_SESSION['cs_gal']['approved'] == 0) {
            $use_count = $this->pic_count_disappall;
        }
        else
            if ($_SESSION['cs_gal']['approved'] == 1) {
                $use_count = $this->pic_count_appall;
            }
            else {
                $use_count = $this->pic_count_all;
            }

            $this->gallery_obj = array(
                'gallery' => $gallery,
                'groupname' => $groupname,
                'totalsize' => $total_size,
                'piccount' => $k,
                'pic_count_gallery' => $this->pic_count_all,
                'piccount_all' => $use_count,
                'piccount_disapproved' => $this->pic_count_disappall,
                'piccount_approved' => $this->pic_count_appall,
                'totalsizekb' => $this->getGalleryFileSize($gallery_id),
                'gblsizekb' => $this->human_file_size($this->get_total_filesize()));

        return $this->gallery_obj;
    }


    /**
     * gallery_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('gallery');
        $this->GALADMIN['conf'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * gallery_class::cmd_tools()
     * 
     * @return
     */
    function cmd_tools() {
        if (is_array($_GET['VRES']))
            foreach ($_GET['VRES'] as $key => $value)
                $_GET['VRES'][$key] = (int)$_GET['VRES'][$key];
        $this->smarty->assign('VRES', $_GET['VRES']);
        $this->smarty->assign('totalsize', human_file_size($this->get_total_filesize()));
        $this->smarty->assign('watermark_exists', file_exists('../images/watermark.png'));
    }


    /**
     * gallery_class::add_users()
     * 
     * @param mixed $pic_arr
     * @return
     */
    function add_users(&$pic_arr) {
        $U = new member_class();
        if (is_array($pic_arr)) {
            foreach ($pic_arr as $picid => $row) {
                $sql .= (($sql != "") ? " OR " : "") . " kid=" . $row['pic_kid'];
            }
            if ($sql != "") {
                $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUST . "  WHERE " . $sql . "");
                while ($row = $this->db->fetch_array_names($result)) {
                    $customers[$row['kid']] = $row;
                }

                foreach ($pic_arr as $picid => $row) {
                    $pic_arr[$picid]['customer'] = $U->setOptions($customers[$row['pic_kid']], false);
                }

            }
        }
        unset($U);
    }

    /**
     * gallery_class::cmd_initpicman()
     * 
     * @return
     */
    function cmd_initpicman() {
        $gid = (int)$_REQUEST['gid'];
        if ($gid == 0) {
            $FIRST_GAL = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALGROUP . " ORDER BY groupname LIMIT 1");
            $gid = $FIRST_GAL['id'];
        }
        $this->load_tree();
        $this->GALADMIN['gid'] = $gid;

    }

    /**
     * gallery_class::cmd_load_pics()
     * 
     * @return
     */
    function cmd_load_pics() {
        $_SESSION['cs_gal']['approved'] = 2;
        if (isset($_GET['cs_filter']))
            $_SESSION['cs_gal']['approved'] = intval($_GET['cs_filter']);
        $this->GALADMIN['gid'] = (int)$_REQUEST['gid'];
        $this->load_tree();
        $galist = $this->loadGallery($_GET['gid'], 200, 130, true, 'crop', 0, 0, intval($_GET['start']), 999);
        $this->add_users($galist['gallery']);
        $this->GALADMIN['galtab'] = $galist['gallery'];
        #  $this->smarty->assign('paging', $this->genPaging($_GET['start'], 35, $_GET['gid'], $this->gallery_obj['pic_count_gallery']));
        $tmp = array_shift($galist['gallery']);
        #  'selectbtn' => gen_submit_btn('{LBL_BTN_REFRESH}'),
        $POBJ = array('galinfo' => $galist, 'album_picid' => $tmp['imginfo']['picid']);
        $this->smarty->assign('POBJ', $POBJ);
        $this->parse_to_smarty();
        kf::echo_template('gallery.fotolist');
    }

    /**
     * gallery_class::cmd_a_movecat()
     * 
     * @return
     */
    function cmd_a_movecat() {
        $this->movePicsToCat($_POST['metaids'], $_POST['FORM']['group_id']);
        $this->echo_json_fb('reloadfotos', $_POST['gid']);
    }

    /**
     * gallery_class::cmd_a_disapp()
     * 
     * @return
     */
    function cmd_a_disapp() {
        $this->approveMass($_POST['metaids'], $_POST['cmd']);
        $this->echo_json_fb('reloadfotos', $_POST['gid']);
    }

    /**
     * gallery_class::cmd_a_app()
     * 
     * @return
     */
    function cmd_a_app() {
        $this->approveMass($_POST['metaids'], $_POST['cmd']);
        $this->echo_json_fb('reloadfotos', $_POST['gid']);
    }

    /**
     * gallery_class::cmd_a_deletem()
     * 
     * @return
     */
    function cmd_a_deletem() {
        $this->massDelete($_POST['metaids']);
        $this->echo_json_fb('reloadfotos', $_POST['gid']);
    }

    /**
     * gallery_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave() {
        $this->saveSort($_POST['morder']);
        $this->set_title_image($_POST['gid'], $_POST['albumtitlepicid']);
        $this->echo_json_fb('reloadfotos', $_POST['gid']);
    }

    /**
     * gallery_class::build_lang_select()
     * 
     * @return
     */
    function build_lang_select() {
        global $LNGOBJ;
        $_GET['uselang'] = ($_GET['uselang'] == 0) ? $this->gbl_config['std_lang_id']:
        $_GET['uselang'];
        $ulang_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE id=" . (int)$_GET['uselang']);
        $ulang_obj['flag'] = kf::gen_thumbnail('/images/' . $ulang_obj['bild'], 30, 30);
        $_SESSION['CNT_TABBEDLANG'] = $LNGOBJ->build_lang_select();
        $this->smarty->assign('ulang_obj', $ulang_obj);
    }

    /**
     * gallery_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $LNGOBJ;
        $this->build_lang_select();
        $this->load_tree();
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id='" . intval($_GET['id']) . "' LIMIT 1");
        $FORM['post_time_int'] = date('d.m.Y', $FORM['post_time_int']);
        $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALCON . " WHERE lang_id=" . intval($_GET['uselang']) . " AND pic_id='" . intval($_GET['id']) .
            "' LIMIT 1");
        list($width_foto_px, $height_foto_px) = getimagesize(PICS_GAL_ROOT_ADMIN . $FORM['pic_name']);
        $FORM['width_foto_px'] = $width_foto_px;
        $FORM['height_foto_px'] = $height_foto_px;
        $EDITOR = array(
            'FORM' => $FORM,
            'FORM_CON' => $FORM_CON,
            'img_src' => kf::gen_thumbnail('/images/gallery/' . $FORM['pic_name'], 150, 100),
            'img_hover' => kf::gen_thumbnail('/images/gallery/' . $FORM['pic_name'], 600, 480),
            'box_header' => $FORM['pic_title'],
            'pic_id' => intval($_GET['id']),
            'langselect' => $LNGOBJ->build_lang_select());

        $EDITOR['albumcount'] = get_data_count(TBL_CMS_GALGROUP, 'id', "1");
        $galist = $this->loadGallery($FORM['group_id'], 90, 90, true, 'crop', 0, 0, 0, 100);
        $this->smarty->assign('picquickjump', $galist['gallery']);


        $EDITOR['fck'] = create_html_editor('FORM_CON[pic_content]', $FORM_CON['pic_content'], 300, 'Basic2');
        $EDITOR['uselang'] = $_GET['uselang'];
        $EDITOR['id'] = $_GET['id'];
        $EDITOR['gid'] = $FORM['group_id'];
        $this->smarty->assign('EDITOR', $EDITOR);
    }


    /**
     * gallery_class::cmd_save_group_content()
     * 
     * @return
     */
    function cmd_save_group_content() {
        $this->save_group_content($_POST['FORM_CON'], $_POST['FORM_CON_ID']);
        $this->ej();
    }

    /**
     * gallery_class::save_image_content()
     * 
     * @param mixed $FORM_CON
     * @param mixed $FORM_CON_ID
     * @return
     */
    function save_image_content($FORM_CON, $FORM_CON_ID) {
        if ($FORM_CON_ID > 0)
            update_table(TBL_CMS_GALCON, 'id', $FORM_CON_ID, $FORM_CON);
        else
            $FORM_CON_ID = insert_table(TBL_CMS_GALCON, $FORM_CON);
        $this->add_image_pageindex($FORM_CON['pic_title'], $FORM_CON['pic_id'], $FORM_CON['lang_id']);
        return $FORM_CON_ID;
    }

    /**
     * gallery_class::cmd_updatepic()
     * 
     * @return
     */
    function cmd_updatepic() {
        if ($_POST['FORM']['pic_scrurl'] != "") {
            $pic_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id=" . $_POST['id'] . " LIMIT 1");
            delete_file(PICS_GAL_ROOT_ADMIN . $pic_obj['pic_name']);
            $filename = CMS_ROOT . 'images/gallery/' . $this->unique_filename(CMS_ROOT . 'images/gallery/', str_replace('.', '_', $this->get_domain_name_of_url($_POST['FORM']['pic_scrurl'])) .
                '.jpg');
            $command = sprintf('%s --width 1300 --height 1000 --no-stop-slow-scripts --enable-javascript --javascript-delay 1000 %s %s', 'wkhtmltoimage', escapeshellarg($_POST['FORM']['pic_scrurl']),
                $filename);
            $output = array();
            exec($command, $output);
            if ($_POST['FORM']['post_time_int'] != "") {
                $_POST['FORM']['post_time_int'] = strtotime($_POST['FORM']['post_time_int']);
            }
            else
                unset($_POST['FORM']['post_time_int']);
            $RET = $this->insert_to_db(basename($filename), $_POST['FORM'], $_POST['id']);
            $id = $RET['id'];
            $this->LOGCLASS->addLog('UPDATE', 'foto ' . basename($filename));
            $this->manupulate_pic($id, $RET['FORM']);
            update_table(TBL_CMS_GALPICS, 'id', $id, $_POST['FORM']);
            $this->ej('reloadquickfotos');
        }
        else {
            $this->save_image_content($_POST['FORM_CON'], $_POST['FORM_CON_ID']);
            $feedback_arr = $this->uploadPicAndSave($_FILES, $_POST['id'], $_POST['FORM']);
            if ($feedback_arr['msge'] == "") {
                if ($_POST['FORM']['post_time_int'] != "") {
                    $_POST['FORM']['post_time_int'] = strtotime($_POST['FORM']['post_time_int']);
                }
                else
                    unset($_POST['FORM']['post_time_int']);
                update_table(TBL_CMS_GALPICS, 'id', $feedback_arr['id'], $_POST['FORM']);
                $this->echo_json_fb('reloadquickfotos');
            }
            else {
                $this->msge($feedback_arr['msge']);
                $this->ej();
            }
        }
    }

    /**
     * gallery_class::cmd_insertpic()
     * 
     * @return
     */
    function cmd_insertpic() {
        if ($_POST['FORM']['pic_scrurl'] != "") {
            $filename = CMS_ROOT . 'images/gallery/' . $this->unique_filename(CMS_ROOT . 'images/gallery/', $_POST['FORM']['pic_scrurl'] . '.jpg');
            $command = sprintf('%s --width 1300 --height 1000 --no-stop-slow-scripts --enable-javascript --javascript-delay 1000 %s %s', 'wkhtmltoimage', escapeshellarg($_POST['FORM']['pic_scrurl']),
                $filename);
            $output = array();
            exec($command, $output);
            if ($_POST['FORM']['post_time_int'] != "") {
                $_POST['FORM']['post_time_int'] = strtotime($_POST['FORM']['post_time_int']);
            }
            else
                unset($_POST['FORM']['post_time_int']);
            $RET = $this->insert_to_db(basename($filename), $_POST['FORM'], $_POST['id']);
            $id = $RET['id'];
            $this->LOGCLASS->addLog('UPLOAD', 'foto ' . basename($filename));
            $this->manupulate_pic($id, $RET['FORM']);
            update_table(TBL_CMS_GALPICS, 'id', $id, $_POST['FORM']);
            $this->TCR->redirect('epage=' . $_POST['epage'] . '&section=edit&cmd=edit&id=' . $id);
        }
        else {
            $feedback_arr = $this->uploadPicAndSave($_FILES, $_POST['id'], $_POST['FORM']);
            if ($feedback_arr['msge'] == "") {
                if ($_POST['FORM']['post_time_int'] != "") {
                    $_POST['FORM']['post_time_int'] = strtotime($_POST['FORM']['post_time_int']);
                }
                else
                    unset($_POST['FORM']['post_time_int']);
                update_table(TBL_CMS_GALPICS, 'id', $feedback_arr['id'], $_POST['FORM']);
                $this->TCR->redirect('epage=' . $_POST['epage'] . '&section=edit&cmd=edit&id=' . $feedback_arr['id']);
            }
            else {
                $this->msge($feedback_arr['msge']);
            }
        }
    }

    /**
     * gallery_class::cmd_reloadquickfotos()
     * 
     * @return
     */
    function cmd_reloadquickfotos() {
        $galist = $this->loadGallery($_GET['gid'], 90, 90, true, 'crop', 0, 0, 0, 100);
        $this->smarty->assign('picquickjump', $galist['gallery']);
        $this->parse_to_smarty();
        kf::echo_template('gallery.editor.fotos');
    }

    /**
     * gallery_class::cmd_reload_foto()
     * 
     * @return
     */
    function cmd_reload_foto() {
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALPICS . " WHERE id='" . intval($_GET['id']) . "' LIMIT 1");
        $arr = array(
            'img_src' => kf::gen_thumbnail('/images/gallery/' . $FORM['pic_name'], 150, 100),
            'img_hover' => kf::gen_thumbnail('/images/gallery/' . $FORM['pic_name'], 600, 480),
            );
        echo json_encode($arr);
        $this->hard_exit();
    }

    /**
     * gallery_class::cmd_mu_save_sess()
     * 
     * @return
     */
    function cmd_mu_save_sess() {
        $FORM = $_POST['FORM'];
        $_SESSION['GAL']['FORM'] = $_POST['FORM'];
        if (!is_dir(UPL_ROOT))
            mkdir(UPL_ROOT, 0777);
        $dh = opendir(UPL_ROOT);
        while (false !== ($filename = readdir($dh))) {
            if ($filename != '.' && $filename != '..')
                unlink(UPL_ROOT . $filename);
        }
        $this->TCR->redirect('section=multiupload&cmd=multiupload&epage=' . $_GET['epage'] . '&gid=' . $FORM['group_id'] . '&section=mu_up');
        $this->hard_exit();
    }

    /**
     * gallery_class::cmd_rename_gallery_by_node()
     * 
     * @return
     */
    function cmd_rename_gallery_by_node() {
        $FORM = $_REQUEST['FORM'];
        list($tmp, $tid) = explode('-', $_GET['id']);
        update_table(TBL_CMS_GALGROUP, 'id', $tid, $FORM);
        echo json_encode(array('id' => $tid));
        $this->hard_exit();
    }

    /**
     * gallery_class::load_groups()
     * 
     * @param mixed $gid
     * @return
     */
    function load_groups($gid) {
        $GAL_OBJ = new gal_class;
        $nodes = new cms_tree_class();
        $nodes->db = $this->db;
        $nodes->label_column = 'groupname';
        $nodes->create_result_and_array("SELECT id, parent, groupname FROM " . TBL_CMS_GALGROUP . " ORDER BY parent,groupname", 0, 0, -1);
        $gid = (int)$gid;
        $this->GALADMIN['parentselect'] = $nodes->output_as_selectbox('FORM[parent]', '', 0, 0, 'MAIN_GROUP');
        $result = $this->db->query("SELECT G.*,G.id AS GID FROM " . TBL_CMS_GALGROUP . " G " . (($gid >= 0) ? " WHERE parent=" . $gid : '') .
            " ORDER BY G.approval DESC,G." . $this->gbl_config['gal_album_sort']);
        while ($row = $this->db->fetch_array_names($result)) {
            $k++;
            $row['g_createdate'] = date('d.m.Y', $row['g_createdate']);
            $row['templselect'] = build_html_selectbox('FORM[' . $row['id'] . '][tpl]', TBL_CMS_TEMPLATES, 'id', 'tpl_name',
                " WHERE modident='gallery' AND layout_group='1'", $row['tpl']);
            $row['url'] = $GAL_OBJ->genGalleryURL($row['id']);
            $row['implement'] = $GAL_OBJ->genGalleryImplement($row['id']);
            $row['icon_edit'] = kf::gen_edit_icon($row['id'], '', 'edit_group');
            $row['icon_del'] = kf::gen_del_icon($row['id'], true, 'deletegroup');
            $row['icon_approve'] = kf::gen_approve_icon($row['id'], $row['approval'], 'galaxapprove_item');
            $row['parentselect'] = $nodes->output_as_selectbox('parent[' . $row['id'] . ']', $row['id'], $row['parent'], '0', 'MAIN_GROUP');
            $row['index'] = $k;
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * gallery_class::cmd_load_group_table()
     * 
     * @return
     */
    function cmd_load_group_table() {
        $this->GALADMIN['grouptable'] = $this->load_groups($_GET['gid']);
        $this->parse_to_smarty();
        kf::echo_template('gallery.grouplist');
    }

    /**
     * gallery_class::cmd_load_groups()
     * 
     * @return
     */
    function cmd_load_groups() {
        $this->load_tree();
        $this->GALADMIN['grouptable'] = $this->load_groups($_GET['gid']);
        $LISTOBJ = array('gid' => $_GET['gid'], );
        $this->smarty->assign('LISTOBJ', $LISTOBJ);
    }

    /**
     * gallery_class::cmd_add_group_tree()
     * 
     * @return
     */
    function cmd_add_group_tree() {
        $_GET['FORM']['g_createdate'] = time();
        $id = insert_table(TBL_CMS_GALGROUP, $_GET['FORM']);
        echo json_encode(array('id' => $id));
        $this->hard_exit();
    }

    /**
     * gallery_class::cmd_add_gallery_group()
     * 
     * @return
     */
    function cmd_add_gallery_group() {
        foreach ($_POST['FORM'] as $key => $wert) {
            if (strlen($wert) == 0) {
                $this->msge('{LBL_PLEASEFILLOUT}');
                $this->TCR->tb();
                return;
            }
        }
        $_POST['FORM']['g_createdate'] = time();
        $id = insert_table(TBL_CMS_GALGROUP, $_POST['FORM']);
        $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($lang = $this->db->fetch_array_names($resultlang)) {
            $this->add_group_pageindex($_POST['FORM']['groupname'], $id, $lang['id']);
        }
        $this->msg("{LBLA_SAVED}");
        $this->ej('reload_gallery_table', 0);
    }

    /**
     * gallery_class::cmd_save_gallery_group()
     * 
     * @return
     */
    function cmd_save_gallery_group() {
        update_table(TBL_CMS_GALGROUP, 'id', $_POST['id'], $_POST['FORM']);
        $this->rebuild_page_index();
        $this->ej();
    }

    /**
     * gallery_class::cmd_set_title_img()
     * 
     * @return
     */
    function cmd_set_title_img() {
        $this->db->query("UPDATE " . TBL_CMS_GALGROUP . " SET picid=" . $_GET['picid'] . " WHERE id=" . $_GET['gid']);
        $this->hard_exit();
    }


    /**
     * gallery_class::cmd_edit_group()
     * 
     * @return
     */
    function cmd_edit_group() {
        global $LNGOBJ;
        $CMSGALLERY = new gal_class;
        $nodes = new cms_tree_class();
        $nodes->db = $this->db;
        $nodes->label_column = 'groupname';
        $nodes->create_result_and_array("SELECT id, parent, groupname FROM " . TBL_CMS_GALGROUP . " ORDER BY parent,groupname", 0, 0, -1);
        $this->GALADMIN['parentselect'] = $nodes->output_as_selectbox('FORM[parent]', '', 0, 0, 'MAIN_GROUP');
        $gallery_group = $this->db->query_first("SELECT *,G.id AS GID,P.id AS PICID FROM " . TBL_CMS_GALGROUP . " G 
                LEFT JOIN " . TBL_CMS_GALPICS . " P ON (P.id=G.picid)
                WHERE G.id=" . $_GET['id'] . " 
                LIMIT 1");
        $gallery_group['url'] = $CMSGALLERY->genGalleryURL($gallery_group['GID']);
        $gallery_group['implement'] = $CMSGALLERY->genGalleryImplement($gallery_group['GID']);
        $gallery_group['templselect'] = build_html_selectbox('FORM[tpl]', TBL_CMS_TEMPLATES, 'id', 'tpl_name', " WHERE modident='gallery' AND layout_group='1'", $gallery_group['tpl']);
        $GALOBJ = array(
            'gid' => $_GET['id'],
            'group' => $nodes->output_as_selectbox('FORM[parent]', $gallery_group['GID'], $gallery_group['parent'], 0, 'MAIN_GROUP'),
            'src' => (($gallery_group['pic_name'] != "") ? '<img src="' . kf::gen_thumbnail('/images/gallery/' . $gallery_group['pic_name'], 300, 200) . '" >' : ''),
            'gallery' => $gallery_group);

        $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_GLGRCON . " WHERE lang_id=" . (int)$_GET['uselang'] . " AND g_id='" . $_GET['id'] . "' LIMIT 1");
        $POBJ = array(
            'gid' => $_GET['id'],
            'uselang' => $_GET['uselang'],
            'conid' => intval($FORM_CON['id']),
            'fck' => create_html_editor('FORM_CON[g_content]', $FORM_CON['g_content'], 200, 'Basic2'),
            'FORM_CON' => $FORM_CON,
            'langselect' => $LNGOBJ->build_lang_select());
        $this->GALADMIN = array_merge($this->GALADMIN, $POBJ);
        $gals = $this->loadGallery($_GET['id']);
        $this->smarty->assign('galgroup', $gallery_group);
        $this->smarty->assign('GALOBJ', $GALOBJ);
        $this->GALADMIN['admingallery'] = $gals['gallery'];
    }

    /**
     * gallery_class::gen_image_link()
     * 
     * @param mixed $linkname
     * @return
     */
    function gen_image_link($linkname) {
        return '/' . $this->gblconfig->gal_path_image . '/' . $this->format_file_name($linkname) . '.html';
    }

    /**
     * gallery_class::gen_group_link()
     * 
     * @param mixed $linkname
     * @return
     */
    function gen_group_link($linkname) {
        return '/' . $this->gblconfig->gal_path . '/' . $this->format_file_name($linkname) . '.html';
    }

    /**
     * gallery_class::add_image_pageindex()
     * 
     * @param mixed $linkname
     * @param mixed $id
     * @param string $lngid
     * @return
     */
    function add_image_pageindex($linkname, $id, $lngid = '1') {
        $query = array('cmd' => 'showfoto', 'pid' => $id);
        $this->connect_to_pageindex($this->gen_image_link($linkname), $query, $id, 'gallery', $lngid);
    }

    /**
     * gallery_class::add_group_pageindex()
     * 
     * @param mixed $linkname
     * @param mixed $id
     * @param string $lngid
     * @return
     */
    function add_group_pageindex($linkname, $id, $lngid = '1') {
        $query = array('cmd' => 'load_group', 'gid' => $id);
        $this->connect_to_pageindex($this->gen_group_link($linkname), $query, $id, 'gallery', $lngid);
    }

    /**
     * gallery_class::rebuild_page_index()
     * 
     * @return
     */
    function rebuild_page_index() {
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='gallery'");
        $result = $this->db->query("SELECT *,id as MID FROM " . TBL_CMS_GALPICS);
        while ($row = $this->db->fetch_array($result)) {
            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_GALCON . " WHERE lang_id=" . $lang['id'] . " AND pic_id='" . $row['id'] . "' LIMIT 1");
                $FORM = array_merge($row, (array )$FORM_CON);
                $link = '/' . $this->gblconfig->gal_path_image . '/' . $this->format_file_name($FORM['pic_title']) . '.html';
                $query = array('cmd' => 'showfoto', 'pid' => $row['MID']);
                if ($FORM['pic_title'] != "")
                    $this->connect_to_pageindex($link, $query, $row['id'], 'gallery', $lang['id']);
            }
        }

        $result = $this->db->query("SELECT *,id as MID FROM " . TBL_CMS_GALGROUP);
        while ($row = $this->db->fetch_array($result)) {
            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_GLGRCON . " WHERE lang_id=" . $lang['id'] . " AND g_id='" . $row['id'] . "' LIMIT 1");
                $FORM = array_merge($row, (array )$FORM_CON);
                $FORM['g_title'] = ($FORM['g_title'] == "") ? $FORM['groupname'] : $FORM['g_title'];
                $link = '/' . $this->gblconfig->gal_path . '/' . $this->format_file_name($FORM['g_title']) . '.html';
                $query = array('cmd' => 'load_group', 'gid' => $row['MID']);
                if ($FORM['g_title'] != "")
                    $this->connect_to_pageindex($link, $query, $row['id'], 'gallery', $lang['id']);
            }
        }
    }

    /**
     * gallery_class::cmd_rebuild_perma()
     * 
     * @return
     */
    function cmd_rebuild_perma() {
        $this->rebuild_page_index();
        $this->ej();
    }
} //CLASS


?>