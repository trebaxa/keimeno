<?php

/**
 * @package    tcblog
 *
 * @copyright  Copyright (C) 2006 - 2017 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.4
 */


class tcblog_admin_class extends tcblog_master_class {

    protected $TCBLOG = array();

    /**
     * tcblog_admin_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        if (!isset($_SESSION['pingroup_id'])) {
            $_SESSION['pingroup_id'] = $this->get_init_group_id();
        }
        if (isset($_GET['gid']) && (int)$_GET['gid'] > 0) {
            $_SESSION['pingroup_id'] = (int)$_GET['gid'];
        }
    }

    /**
     * tcblog_admin_class::cmd_axdelete_item()
     * 
     * @return
     */
    function cmd_axdelete_item() {
        $this->del_blog_item($this->TCR->GET['ident']);
        $this->rebuild_page_index();
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_PIN . " SET approval='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }

    /**
     * tcblog_admin_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $pageindex = $this->db->query_first("SELECT * FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='tcblog' LIMIT 1");
        $this->TCBLOG['selected_group'] = $this->load_group($_SESSION['pingroup_id']);
        $this->TCBLOG['pageindex'] = $pageindex;
        $this->smarty->assign('TCBLOG', $this->TCBLOG);
    }

    /**
     * tcblog_admin_class::cmd_conf()
     * 
     * @return
     */
    function cmd_conf() {
        $CONFIG_OBJ = new config_class('tcblog');
        $this->TCBLOG['CONFIG'] = $CONFIG_OBJ->buildTable();
    }

    /**
     * tcblog_admin_class::del_blog_item()
     * 
     * @param mixed $id
     * @return
     */
    function del_blog_item($id) {
        $id = (int)$id;
        $this->remove_from_page_index('tcblog', $id, 'blog');
        $this->db->query("DELETE FROM " . TBL_CMS_PIN . " WHERE id=" . $id);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $id);
        while ($row = $this->db->fetch_array_names($result)) {
            $this->del_img($row['id']);
        }
        $this->db->query("DELETE FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $id);
    }

    /**
     * tcblog_admin_class::rebuild_page_index()
     * 
     * @return
     */
    function rebuild_page_index() {
        $k = 0;
        $this->db->query("DELETE FROM " . TBL_CMS_PAGEINDEX . " WHERE pi_modident ='tcblog'");
        $result = $this->db->query("SELECT P.*,P.id as MID,G.g_pageid FROM " . TBL_CMS_PIN . " P, " . TBL_CMS_PIN_GROUPS . " G WHERE P.group_id=G.id");
        while ($row = $this->db->fetch_array($result)) {
            $resultlang = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
            while ($lang = $this->db->fetch_array_names($resultlang)) {
                $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE lang_id=" . $lang['id'] . " AND nid='" . $row['MID'] . "' LIMIT 1");
                $FORM = array_merge($row, (array )$FORM_CON);
                $link = '/blog/' . date('Y', $FORM['inserttime']) . '/' . date('m', $FORM['inserttime']) . '/' . date('d', $FORM['inserttime']) . '/' . $this->format_file_name($FORM['title']) .
                    '.html';
                $query = array('cmd' => 'load_blog_item', 'id' => $row['MID']);
                if ($FORM['title'] != "") {
                    $this->connect_to_pageindex($link, $query, $row['MID'], 'tcblog', $FORM_CON['lang_id'], 0, $row['g_pageid']);
                    $k++;
                }
            }
        }
        return $k;
    }


    /**
     * tcblog_admin_class::add_pageindex()
     * 
     * @param mixed $FORM
     * @param mixed $id
     * @param string $lngid
     * @return
     */
    function add_pageindex($FORM, $id, $lngid = '1') {
        $link = '/blog/' . date('Y', $FORM['inserttime']) . '/' . date('m', $FORM['inserttime']) . '/' . date('d', $FORM['inserttime']) . '/' . $this->format_file_name($FORM['title']) .
            '.html';
        $query = array('cmd' => 'load_blog_item', 'id' => $id);
        $this->connect_to_pageindex($link, $query, $id, 'tcblog', $lngid);
    }

    /**
     * tcblog_admin_class::cmd_add_item()
     * 
     * @return
     */
    function cmd_add_item() {
        $FORM = $_POST['FORM'];
        $FORM_CON = $_POST['FORM_CON'];
        $FORM['ndate'] = ($FORM['ndate'] == "") ? date('Y-m-d') : format_date_to_sql_date($FORM['ndate']);
        $FORM['inserttime'] = strtotime($FORM['ndate']);
        $FORM['mid'] = $_SESSION['mitarbeiter'];
        $id = insert_table(TBL_CMS_PIN, $FORM);
        $FORM_CON['nid'] = $id;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " ORDER BY id");
        while ($row = $this->db->fetch_array_names($result)) {
            $FORM_CON['lang_id'] = $row['id'];
            insert_table(TBL_CMS_PIN_CONTENT, $FORM_CON);
            $this->add_pageindex(array_merge($FORM, $FORM_CON), $id, $row['id']);
        }
        $this->msg('{LBLA_SAVED}');
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?uselang=' . $this->gblconfig->std_lang_id . '&aktion=edit&id=' . $id . '&epage=' . $_POST['epage']);
        $this->hard_exit();
    }

    /**
     * tcblog_admin_class::cmd_save_item()
     * 
     * @return
     */
    function cmd_save_item() {
        $FORM = $_POST['FORM'];
        $FORM_CON = $_POST['FORM_CON'];
        $conid = (int)$_POST['conid'];
        $FORM['ndate'] = ($FORM['ndate'] == "") ? date('Y-m-d') : format_date_to_sql_date($FORM['ndate']);
        $FORM['tags'] = implode(',', $this->trim_array(explode(',', $FORM['tags'])));
        $FORM['inserttime'] = strtotime($FORM['ndate']);
        update_table(TBL_CMS_PIN, 'id', $_POST['id'], $FORM);
        if ($conid > 0) {
            update_table(TBL_CMS_PIN_CONTENT, 'id', $conid, $FORM_CON);
        }
        else {
            $FORM_CON['nid'] = $_POST['id'];
            $conid = insert_table(TBL_CMS_PIN_CONTENT, $FORM_CON);
        }

        # Image Upload
        if ($_FILES['datei']['name'] != "") {
            if (!validate_upload_file($_FILES['datei'])) {
                $this->msge($_SESSION['upload_msge']);
                $this->ej();
            }
            $this->del_img($conid);
            $new_file_name = $this->format_file_name($_FILES['datei']['name']);
            if (!is_dir(CMS_ROOT . BLOG_IMG_PATH))
                mkdir(CMS_ROOT . BLOG_IMG_PATH, 0775);
            $new_file_name = $this->unique_filename(CMS_ROOT . BLOG_IMG_PATH, $new_file_name);
            move_uploaded_file($_FILES['datei']['tmp_name'], CMS_ROOT . BLOG_IMG_PATH . $new_file_name);
            chmod(CMS_ROOT . BLOG_IMG_PATH . $new_file_name, 0755);
            $FINFO = array();
            $FINFO['b_image'] = $new_file_name;
            update_table(TBL_CMS_PIN_CONTENT, 'id', $conid, $FINFO);
        }
        $this->add_pageindex(array_merge($FORM, $FORM_CON), $_POST['id'], $FORM_CON['lang_id']);
        $this->rebuild_page_index();
        $this->ej('reload_item');
    }

    function cmd_rebuildpageindex() {
        $k = $this->rebuild_page_index();
        self::msg($k . ' Links aktualisiert.');
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_save_blog_foto()
     * 
     * @return
     */
    function cmd_save_blog_foto() {
        $id = (int)$_POST['id'];
        // Foto
        if ($_FILES["datei"]["name"] != "") {
            if (!is_dir(CMS_ROOT . 'file_data/tcblog'))
                mkdir(CMS_ROOT . 'file_data/tcblog', 0775);
            if (!is_dir(CMS_ROOT . 'file_data/tcblog/fotos'))
                mkdir(CMS_ROOT . 'file_data/tcblog/fotos', 0775);
            if (!validate_upload_file($_FILES['datei'])) {
                $this->msge('Bilddatei ungültig.');
            }
            $filename = $this->unique_filename(CMS_ROOT . 'file_data/tcblog/fotos/', $id . '_' . date('H_i') . $this->format_file_name($_FILES["datei"]["name"]));
            $target = CMS_ROOT . 'file_data/tcblog/fotos/' . $filename;
            if (move_uploaded_file($_FILES["datei"]["tmp_name"], $target)) {
                chmod($target, 0755);
                $N_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . "  WHERE id=" . $id);
                $fotos = unserialize($N_OBJ['b_fotos']);
                $fotos[md5($target)] = array('foto' => basename($target), 'id' => md5($target));
                $arr = array('b_fotos' => serialize($fotos));
                update_table(TBL_CMS_PIN_CONTENT, 'id', $id, $arr);
            }
            else {
                $this->msge('Bilddatei ungültig.');
            }
        }
        $this->ej('reload_blog_fotos');
    }

    /**
     * tcblog_admin_class::cmd_reload_blog_fotos()
     * 
     * @return
     */
    function cmd_reload_blog_fotos() {
        $id = (int)$_GET['id'];
        $N_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . "  WHERE id=" . $id);
        $fotos = unserialize($N_OBJ['b_fotos']);
        foreach ((array )$fotos as $key => $row) {
            if (file_exists(CMS_ROOT . '/file_data/tcblog/fotos/' . $row['foto'])) {
                $row[thumb] = kf::gen_thumbnail('/file_data/tcblog/fotos/' . $row['foto'], 160, 90, 'crop', false);
                $this->TCBLOG['fotos'][] = $row;
            }
            else {
                unset($fotos[$key]);
                $arr = array('b_fotos' => serialize($fotos));
                update_table(TBL_CMS_PIN_CONTENT, 'id', $id, $arr);
            }
        }
        $this->parse_to_smarty();
        kf::echo_template('tcblog.fotos');
    }

    /**
     * tcblog_admin_class::cmd_delfoto()
     * 
     * @return
     */
    function cmd_delfoto() {
        $N_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . "  WHERE id=" . (int)$_GET['id']);
        $fotos = unserialize($N_OBJ['b_fotos']);
        foreach ((array )$fotos as $key => $row) {
            if ($key == $_GET['fotoid']) {
                if (file_exists(CMS_ROOT . 'file_data/tcblog/fotos/' . $row['foto']))
                    @unlink(CMS_ROOT . 'file_data/tcblog/fotos/' . $row['foto']);
                unset($fotos[$key]);
                $arr = array('b_fotos' => serialize($fotos));
                update_table(TBL_CMS_PIN_CONTENT, 'id', $_GET['id'], $arr);
                break;
            }
        }
        $this->hard_exit();
    }

    /**
     * tcblog_admin_class::del_img()
     * 
     * @param mixed $id
     * @return
     */
    function del_img($id) {
        $N_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . "  WHERE id=" . $id);
        @unlink(CMS_ROOT . BLOG_IMG_PATH . $N_OBJ['b_image']);
        $this->db->query("UPDATE " . TBL_CMS_PIN_CONTENT . " SET b_image='' WHERE id=" . $id);
    }

    /**
     * tcblog_admin_class::cmd_del_img()
     * 
     * @return
     */
    function cmd_del_img() {
        $this->del_img((int)$_GET['id']);
        $this->hard_exit();
    }

    /**
     * tcblog_admin_class::cmd_load_item_json()
     * 
     * @return
     */
    function cmd_load_item_json() {
        $N_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . "  WHERE id=" . (int)$_GET['id']);
        echo json_encode($N_OBJ);
        $this->hard_exit();
    }

    /**
     * tcblog_admin_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $LNGOBJ;
        $N_OBJ = $this->db->query_first("SELECT *,NL.id AS NLID,NL.approval AS NA FROM " . TBL_CMS_PIN . " NL LEFT JOIN " . TBL_CMS_ADMINS .
            " K ON (K.id=NL.mid) WHERE NL.id=" . (int)$_GET['id']);
        $N_OBJ['icon_approve'] = kf::gen_approve_icon($N_OBJ['NLID'], $N_OBJ['NA']);
        $this->TCBLOG['FORM_CON'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE lang_id=" . $_GET['uselang'] . " AND nid='" . $_GET['id'] .
            "' LIMIT 1");
        $N_OBJ['ndate'] = my_date('d.m.Y', $N_OBJ['ndate']);
        $N_OBJ['comments'] = $this->load_comments($N_OBJ['NLID']);
        foreach ($N_OBJ['comments'] as $key => $row) {
            $N_OBJ['comments'][$key]['icons'][] = kf::gen_del_icon($row['id'], true, 'del_blog_comment');
            $N_OBJ['comments'][$key]['icons'][] = kf::gen_approve_icon($row['id'], $row['c_approved'], 'approve_blog_comment');
        }
        $this->TCBLOG['blogitem'] = $N_OBJ;
        $this->TCBLOG['blogitem']['langselect'] = $LNGOBJ->build_lang_select();
        $this->TCBLOG['blogitem']['editor'] = create_html_editor('FORM_CON[content]', $this->TCBLOG['FORM_CON']['content'], 500);
        $this->TCBLOG['blog_group_id'] = $_SESSION['pingroup_id'];
    }

    /**
     * tcblog_admin_class::cmd_del_blog_comment()
     * 
     * @return
     */
    function cmd_del_blog_comment() {
        $this->db->query("DELETE FROM " . TBL_CMS_PIN_COMMENTS . " WHERE id=" . $_GET['ident']);
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_approve_blog_comment()
     * 
     * @return
     */
    function cmd_approve_blog_comment() {
        update_Table(TBL_CMS_PIN_COMMENTS, 'id', $_GET['ident'], array('c_approved' => $_GET['value']));
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_load_items()
     * 
     * @return
     */
    function cmd_load_items() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_GROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->TCBLOG['groupsselect'][] = $row;
        }
        $this->load_items();
    }


    /**
     * tcblog_admin_class::load_items()
     * 
     * @return
     */
    function load_items() {
        $this->TCBLOG['pin_items'] = array();
        if ($_SESSION['pingroup_id'] > 0) {
            $result = $this->db->query("SELECT NL.id AS NLID,K.*,NL.*,NC.*,NG.*,NG.id AS NGID,NL.approval AS NA
						FROM " . TBL_CMS_PIN . " NL
						INNER JOIN " . TBL_CMS_PIN_GROUPS . " NG ON (NG.id=NL.group_id AND NL.group_id=" . (int)$_SESSION['pingroup_id'] . ")
						LEFT JOIN " . TBL_CMS_PIN_CONTENT . " NC ON (NL.id=NC.nid AND NC.lang_id=" . (int)$_SESSION['alang_id'] . ")
						LEFT JOIN " . TBL_CMS_ADMINS . " K ON (K.id=NL.mid)
						WHERE 1 
						GROUP BY NL.id ORDER BY inserttime DESC");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['icon_del'] = kf::gen_del_icon($row['NLID'], true, 'axdelete_item');
                $row['icon_edit'] = kf::gen_edit_icon($row['NLID'], '&uselang=' . $this->gblconfig->std_lang_id);
                $row['icon_approve'] = kf::gen_approve_icon($row['NLID'], $row['NA']);
                $row['date'] = (($row['ndate'] != '0000-00-00') ? my_date('d.m.Y', $row['ndate']) : '');
                if ($row['title'] == "") {
                    $TITLE = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_CONTENT . " WHERE nid=" . $row['NLID'] . " AND title<>''");
                    $row['title'] = $TITLE['title'];
                }
                $this->TCBLOG['pin_items'][] = $row;
            }
        }
    }

    /**
     * tcblog_admin_class::cmd_load_groups()
     * 
     * @return
     */
    function cmd_load_groups() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_GROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id'], '&section=edit_group', 'edit_group');
            $row['icons'][] = kf::gen_del_icon($row['id'], true, 'del_group');
            $this->TCBLOG['groups'][] = $row;
        }
    }

    /**
     * tcblog_admin_class::cmd_save_groups()
     * 
     * @return
     */
    function cmd_save_groups() {
        foreach ((array )$_POST['FORM'] as $key => $row) {
            update_table(TBL_CMS_PIN_GROUPS, 'id', $key, $row);
        }
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_edit_group()
     * 
     * @return
     */
    function cmd_edit_group() {
        global $LNGOBJ;
        $this->TCBLOG['group'] = $this->db->query_first("SELECT *
							FROM " . TBL_CMS_PIN_GROUPS . "
							WHERE id=" . $_GET['id'] . "
							ORDER BY groupname");
        $this->TCBLOG['langselect'] = $LNGOBJ->build_lang_select();
        $this->TCBLOG['groupcon'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_PIN_GCON . " WHERE lang_id=" . $_GET['uselang'] . " AND g_id='" . $_GET['id'] .
            "' LIMIT 1");
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->TCBLOG['permchecks'] .= '<input type="checkbox" ' . ((get_data_count(TBL_CMS_PIN_PERM, 'perm_did', "perm_did=" . $_GET['id'] . " AND perm_group_id=" . $row['id']) >
                0) ? 'checked' : '') . ' name="CUSTGROUP[' . $row['id'] . ']" value="' . $row['id'] . '"> ' . $row['groupname'] . '<br>';
        }
    }

    /**
     * tcblog_admin_class::cmd_setallperm()
     * 
     * @return
     */
    function cmd_setallperm() {
        # Permissions setzen
        $this->db->query("DELETE FROM " . TBL_CMS_PIN_PERM . " WHERE perm_did=" . $_POST['tid']);
        if (is_array($_POST['CUSTGROUP'])) {
            foreach ($_POST['CUSTGROUP'] as $key => $group_id) {
                $this->db->query("INSERT INTO " . TBL_CMS_PIN_PERM . " SET perm_did=" . $_POST['tid'] . ", perm_group_id=" . $group_id);
            }
        }
        $FORM_CON = $_POST['FORM_CON'];
        if ($_POST['conid'] > 0) {
            update_table(TBL_CMS_PIN_GCON, 'id', $_POST['conid'], $FORM_CON);
        }
        else {
            $FORM_CON['g_id'] = $_POST['tid'];
            insert_table(TBL_CMS_PIN_GCON, $FORM_CON);
        }
        $this->ej();
    }

    /**
     * tcblog_admin_class::cmd_add_blog()
     * 
     * @return
     */
    function cmd_add_blog() {
        $id = insert_table(TBL_CMS_PIN_GROUPS, $_POST['FORM']);
        $this->db->query("INSERT INTO " . TBL_CMS_PIN_PERM . " SET perm_did=" . $id . ", perm_group_id=1000");
        $this->TCR->tb();
    }

    /**
     * tcblog_admin_class::cmd_del_group()
     * 
     * @return
     */
    function cmd_del_group() {
        if (get_data_count(TBL_CMS_PIN, 'id', "group_id=" . $_GET['ident']) == 0) {
            $this->db->query("DELETE FROM " . TBL_CMS_PIN_GROUPS . " WHERE id>1 AND id=" . $_GET['ident']);
            $this->db->query("DELETE FROM " . TBL_CMS_PIN_PERM . " WHERE perm_did=" . $_GET['ident']);
            $this->db->query("DELETE FROM " . TBL_CMS_PIN_GCON . " WHERE g_id=" . $_GET['ident']);
            $this->msg('{LBL_DELETED}');
            $this->ej();
        }
        else {
            $this->msge('{LBLA_NOT_DELETED} {LBL_HASSUBCONTENT}');
            $this->ej();
        }
    }

    /**
     * tcblog_admin_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        return $this->load_templates_for_plugin_by_modident('tcblog', $params);
    }

    /**
     * tcblog_admin_class::load_themes_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_themes_integration($params) {
        $list = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_PIN_GROUPS . " WHERE 1 ORDER BY groupname");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * tcblog_admin_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_TCBLOG_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * tcblog_admin_class::save_homepage_integration_latest_post()
     * 
     * @param mixed $params
     * @return void
     */
    function save_homepage_integration_latest_post($params) {
        $cont_matrix_id = (int)$params['id'];
        $id = $params['FORM']['tplid'];
        $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$id);
        $upt = array('tm_content' => '{TMPL_TCBLOGLATEST_' . $cont_matrix_id . '}', 'tm_pluginfo' => $R['description']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

}
