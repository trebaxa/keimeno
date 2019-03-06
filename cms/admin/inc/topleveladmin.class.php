<?php

/**
 * @package    Keimeno
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */




class topleveladmin_class extends keimeno_class {


    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);

    }

    function cmd_topaxapprove_item() {
        $this->db->query("UPDATE " . TBL_CMS_TOPLEVEL . " SET approval='" . $this->TCR->GET['value'] . "' WHERE id=" . (int)$this->TCR->GET['ident'] . " LIMIT 1");
        $this->ej();
    }

    function cmd_delete_topl_icon() {
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLCON . " WHERE lang_id=" . (int)$_GET['lang_id'] . " AND tid=" . (int)$_GET['id']);
        delete_file(CMS_ROOT . 'file_data/themeimg/' . $T['tpl_icon']);
        $this->db->query("UPDATE " . TBL_CMS_TPLCON . " SET tpl_icon='' WHERE lang_id=" . (int)$_GET['lang_id'] . " AND tid=" . (int)$_GET['id'] . " LIMIT 1");
        $this->hard_exit();
    }

    function cmd_delete_theme_image() {
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLCON . " WHERE lang_id=" . (int)$_GET['lang_id'] . " AND tid=" . (int)$_GET['id']);
        delete_file(CMS_ROOT . 'file_data/themeimg/' . $T['theme_image']);
        $this->db->query("UPDATE " . TBL_CMS_TPLCON . " SET theme_image='' WHERE lang_id=" . (int)$_GET['lang_id'] . " AND tid=" . (int)$_GET['id'] . " LIMIT 1");
        $this->hard_exit();
    }

    function cmd_save_tpltable() {
        $FORM = (array )$_POST['FORM'];
        $FORM = $this->sort_multi_array($FORM, 'morder', SORT_ASC, SORT_NUMERIC);
        $this->db->query("UPDATE " . TBL_CMS_TOPLEVEL . " SET show_parent_level=0 WHERE 1");
        foreach ($FORM as $key => $row) {
            $k += 10;
            $row['morder'] = $k;
            $id = $row['id'];
            unset($row['id']);
            update_table(TBL_CMS_TOPLEVEL, 'id', $id, $row);
        }
        $this->echo_json_fb();
    }

    function parse_to_smarty() {
        $this->smarty->assign('TOPLMAN', $this->TOPLMAN);
    }


    function cmd_show_all() {
        $nodes = new cms_tree_class();
        $nodes->db = $this->db;
        $nodes->create_result_and_array("SELECT id, parent, description FROM " . TBL_CMS_TEMPLATES . " ORDER BY parent,morder", 0, 0, -1);
        $temp_class_obj = new content_class();
        $result = $this->db->query("SELECT T.*,TC.theme_image FROM " . TBL_CMS_TOPLEVEL . " T
		LEFT JOIN " . TBL_CMS_TPLCON . " TC ON (TC.tid=T.id AND TC.theme_image!='')
		GROUP BY T.id
		ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['icons'][] = kf::gen_edit_icon($row['id']);
            if ($row['admin'] == 0) {
                $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval'], 'topaxapprove_item');
                $row['icons'][] = kf::gen_del_icon($row['id'], true, 'deltoplevel');
                #kf::gen_del_icon_reload($row['id'], 'deltoplevel');
            }

            // ERLAUBE EINSTIEGSPUNKTE ERMITTELN
            $temp_class_obj->gen_entry_point_list($list, $nodes, $row);
            foreach ($list as $key => $value) {
                $row['entrypoints'][] = '<option ' . (($row['first_page'] == $value['id']) ? 'selected' : '') . ' value="' . (int)$value['id'] . '">' . $value['label'] .
                    '</option>';
            }

            $row['theme_image'] = ($row['theme_image'] != "") ? '/file_data/themeimg/' . $row['theme_image'] : '/images/opt_no_pic.jpg';
            $row[thumb] =  kf::gen_thumbnail($row['theme_image'], 100, 30) . '?a=' . rand(0, 10000);
            $this->TOPLMAN['topltable'][] = $row;
        }
        self::allocate_memory($nodes);
        self::allocate_memory($temp_class_obj);
    }

    function cmd_ax_show_all() {
        $this->cmd_show_all();
        $this->parse_to_smarty();
        kf::echo_template('toplevel.editor');
    }

    function cmd_rename_topl() {
        $FORM = $_REQUEST['FORM'];
        list($tmp, $tid) = explode('-', $_GET['id']);
        update_table(TBL_CMS_TOPLEVEL, 'id', $tid, $FORM);
        ECHO json_encode(array('id' => $tid));
        $this->hard_exit();
    }


    function cmd_save_topl() {
        $id = (int)$_POST['id'];
        if ($_POST['id'] == 0) {
            $_POST['FORM_CON']['level_name'] = stripslashes(strip_tags($_POST['FORM_CON']['level_name']));
            $_POST['FORM_CON']['level_subtitle'] = $_POST['FORM_CON']['level_subtitle'];
            $id = $_POST['FORM_CON']['tid'] = insert_table(TBL_CMS_TOPLEVEL, $_POST['FORM']);
            $_POST['FORM_CON']['lang_id'] = $this->gbl_config['std_lang_id'];
            $conid = insert_table(TBL_CMS_TPLCON, $_POST['FORM_CON']);
            $this->LOGCLASS->addLog('INSERT', 'toplevel <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=edit&id=' . $this->db->insert_id() .
                '">' . $_POST['FORM']['description'] . '</a>');

        }
        else {
            $this->LOGCLASS->addLog('MODIFY', 'toplevel <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&aktion=edit&id=' . $_POST['id'] . '">' . $_POST['FORM']['description'] .
                '</a>');
            $_POST['FORM_CON']['level_name'] = (strip_tags($_POST['FORM_CON']['level_name']));
            $_POST['FORM_CON']['level_subtitle'] = $_POST['FORM_CON']['level_subtitle'];
            update_table(TBL_CMS_TOPLEVEL, 'id', $_POST['id'], $_POST['FORM']);
            if ($_POST['conid'] > 0) {
                update_table(TBL_CMS_TPLCON, 'id', $_POST['conid'], $_POST['FORM_CON']);
                $conid = $_POST['conid'];
            }
            else {
                $conid = insert_table(TBL_CMS_TPLCON, $_POST['FORM_CON']);
            }
        }

        if ($_FILES['datei']['name'] != "") {
            $ext_file = strtolower(strrchr($_FILES['datei']['name'], '.'));
            if (!validate_upload_file($_FILES['datei'], TRUE)) {
                self::msge($_SESSION['upload_msge']);
                $this->ej();
            }
            $target_file = CMS_ROOT . 'file_data/themeimg/theme_image_' . (int)$_POST['FORM_CON']['lang_id'] . '_' . (int)$id . $ext_file;
            delete_file($target_file);
            if (move_uploaded_file($_FILES['datei']['tmp_name'], $target_file) === TRUE) {
                chmod($target_file, 0755);
                $THEME = ARRAY();
                $THEME['theme_image'] = basename($target_file);
                update_table(TBL_CMS_TPLCON, 'id', $conid, $THEME);
                clean_cache_like($THEME['theme_image']);
            }
        }


        if ($_FILES['icon_datei']['name'] != "") {
            $ext_file = strtolower(strrchr($_FILES['icon_datei']['name'], '.'));
            if (!validate_upload_file($_FILES['icon_datei'], TRUE)) {
                self::msge($_SESSION['upload_msge']);
                $this->ej();
            }
            $target_file = CMS_ROOT . 'file_data/themeimg/topl_icon_' . (int)$_POST['FORM_CON']['lang_id'] . '_' . (int)$id . $ext_file;
            delete_file($target_file);
            if (move_uploaded_file($_FILES['icon_datei']['tmp_name'], $target_file) === TRUE) {
                chmod($target_file, 0755);
                $THEME = ARRAY();
                $THEME['tpl_icon'] = basename($target_file);
                update_table(TBL_CMS_TPLCON, 'id', $conid, $THEME);
                clean_cache_like($THEME['tpl_icon']);
            }
        }
        self::msg("{LBLA_SAVED}");
        $this->ej('set_topl_ids', $id . ',' . $conid);
    }

    function cmd_create_toplevel() {
        list($tmp, $modid) = explode('-', $_GET['id']);
        $FORM = $_GET['FORM'];
        $FORM_CON['level_name'] = stripslashes(strip_tags($FORM['description']));
        $FORM_CON['level_subtitle'] = "";
        $id = $FORM_CON['tid'] = insert_table(TBL_CMS_TOPLEVEL, $FORM);
        $FORM_CON['lang_id'] = $this->gbl_config['std_lang_id'];
        $conid = insert_table(TBL_CMS_TPLCON, $FORM_CON);
        $this->LOGCLASS->addLog('INSERT', 'toplevel <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=edit&id=' . $this->db->insert_id() . '">' .
            $FORM['description'] . '</a>');
        ECHO json_encode(array('id' => $id));
        $this->hard_exit();
    }

    function cmd_edit() {
        global $LNGOBJ;
        $GRAPHIC_FUNC = new graphic_class();
        $LNGOBJ->init_uselang();
        $FORM_CON = $this->db->query_first("SELECT * FROM " . TBL_CMS_TPLCON . " WHERE lang_id=" . (int)$_GET['uselang'] . " AND tid='" . (int)$_GET['id'] . "' LIMIT 1");
        $FORM = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id='" . (int)$_GET['id'] . "' LIMIT 1");
        $FORM['build_lang_select'] = $LNGOBJ->build_lang_select();

        if ($FORM_CON['theme_image'] != "") {
            $FORM_CON['theme_imagethumb'] = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../file_data/themeimg/' . $FORM_CON['theme_image'], 300, 300, 'admin/' .
                CACHE, TRUE, 'resize');
        }
        if ($FORM_CON['tpl_icon'] != "") {
            $FORM_CON['tpl_iconthumb'] = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../file_data/themeimg/' . $FORM_CON['tpl_icon'], 90, 90, 'admin/' . CACHE, TRUE,
                'resize');
        }
        $FORM['con'] = $FORM_CON;
        $this->smarty->assign('toplobj', $FORM);
        return $FORM;
    }

    function cmd_ax_topl_edit() {
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('toplevel.editor');
    }

    function cmd_reloadimgs() {
        $TOPL = $this->cmd_edit();
        echo json_encode(array('theme' => strval($TOPL['con']['theme_imagethumb']), 'icon' => strval($TOPL['con']['tpl_iconthumb'])));
        $this->hard_exit();
    }

    function cmd_deltoplevel() {
        $id = (int)$_GET['ident'];
        $tmp_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id='" . $id . "' LIMIT 1");
        if (get_data_count(TBL_CMS_TEMPLATES, "id", "tl=" . $id) > 0) {
            self::msge('{ERR_NOTDELETED_INUSE}');
            $this->ej();
        }
        else {
            $this->db->query("DELETE FROM " . TBL_CMS_TOPLEVEL . " WHERE id='" . $id . "' LIMIT 1");
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TPLCON . " WHERE tid='" . $id . "'");
            while ($row = $this->db->fetch_array_names($result)) {
                $target_file = CMS_ROOT . 'file_data/themeimg/' . $row['tpl_icon'];
                delete_file($target_file);
            }
            $this->db->query("DELETE FROM " . TBL_CMS_TPLCON . " WHERE tid='" . $id . "' OR tid=0");
            $this->LOGCLASS->addLog('DELETE', 'toplevel ' . $tmp_obj['description']);
            if ($id == $_SESSION['toplevel'])
                $_SESSION['toplevel'] = 1;
            self::msg('{LBLA_UPDATE_DONE}');
            $this->ej();
        }
    }


    function cmd_load_toplevel_tree() {
        $result = $this->db->query("SELECT T.*,TC.theme_image FROM " . TBL_CMS_TOPLEVEL . " T
		LEFT JOIN " . TBL_CMS_TPLCON . " TC ON (TC.tid=T.id AND TC.theme_image!='')
		GROUP BY T.id
		ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->TOPLMAN['topleveltree'][] = $row;
        }
        #   $this->TOPLMAN['topleveltree'] = $this->sort_multi_array($this->TOPLMAN['topleveltree'], 'description', SORT_ASC, SORT_STRING);
        $this->parse_to_smarty();
        kf::echo_template('toplevel.tree');
    }
}

?>