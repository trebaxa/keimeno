<?php

/**
 * @package    Keimeno
 * @author Harald Petrich
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 */


class template_class extends keimeno_class {

    var $CMSDATA = NULL;
    var $langid = 1;
    var $template = array();

    /**
     * template_class::template_class()
     * 
     * @param integer $langid
     * @param mixed $CMSDATA
     * @return
     */
    function __construct($langid = 1, $CMSDATA = array()) {
        parent::__construct();
        $this->DATA = $CMSDATA;
        $this->langid = (int)$langid;
    }

    /**
     * template_class::set_lang_id()
     * 
     * @param mixed $langid
     * @return
     */
    function set_lang_id(&$langid) {
        if ($langid == 0)
            $langid = 1;
        $this->langid = (int)$langid;
    }

    /**
     * template_class::list_opt()
     * 
     * @param mixed $row
     * @return
     */
    function list_opt($row) {
        if ($row['TT'] == 'T') {
            $label = '{LBL_CONTENTPAGE}';
        }
        if ($row['TT'] == 'B') {
            $label = 'Inlay';
        }
        if ($row['gbl_template'] == 1) {
            $label = '{LBLA_GBLTEMP}';
        }
        if ($row['TT'] == 'B') {
            $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=inlayadmin.inc&uselang=' . $row['lang_id'] . '', 'edit', 'id', 'run.php');
            $edit_link = 'run.php?epage=inlayadmin.inc&uselang=' . $row['lang_id'] . '&aktion=edit&id=' . $row['TID'];
        }
        if ($row['TT'] == 'T' && $row['gbl_template'] == 0) {
            $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=websitemanager.inc&uselang=' . $row['lang_id'] . '&tl=' . $row['tl'], 'edit', 'id', 'run.php');
            $edit_link = 'run.php?epage=websitemanager.inc&aktion=edit&uselang=' . $row['lang_id'] . '&tl=' . $row['tl'] . '&id=' . $row['TID'];
        }
        if ($row['TT'] == 'T' && $row['gbl_template'] == 1) {
            $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=gbltemplates.inc&uselang=' . $row['lang_id'] . '', 'edit', 'id', 'run.php');
            $edit_link = 'run.php?epage=gbltemplates.inc&aktion=edit&uselang=' . $row['lang_id'] . '&id=' . $row['TID'];
        }

        $row['edit_link'] = $edit_link;
        $row['edit_icon'] = $edit_icon;
        $row['label'] = $label;
        $row['thumb'] = kf::gen_thumbnail('/images/' . $row['bild'], 20, 20, 0);
        return $row;
    }

    /**
     * template_class::set_options()
     * 
     * @param mixed $row
     * @return
     */
    function set_options($row) {
        $row['icons']['edit'] = kf::gen_edit_icon($row['id'], '&uselang=' . (int)$_SESSION['employee']['lang_id_matrix'][0]);
        $row['icons']['approve'] = kf::gen_approve_icon($row['id'], $row['approval']);
        if ($row['admin'] == 0)
            $row['icons']['del'] = kf::gen_del_icon_reload($row['id'], 'a_delete', '{LBLA_CONFIRM}', '&starttree=' . (int)$_GET['starttree']);
        if ($row['admin'] == 0 && $row['c_type'] == 'B')
            $row['icons']['del'] = kf::gen_del_icon($row['id'], true, 'axdelinlay');
        if ($row['gbl_template'] == 0 && $row['c_type'] == 'T') {
            $url_label = ($row['t_htalinklabel'] == "") ? $row['linkname'] : $row['t_htalinklabel'];
            $tid = ($row['t_htalinklabel'] != "") ? 0 : $row['id'];
            $row['icons']['preview'] = kf::gen_eye_icon('http://www.' . FM_DOMAIN . gen_page_link($tid, $url_label, $_GET['uselang']), '_view', 'previewlink');
        }
        $row['smartytpl'] = htmlspecialchars('<% include file="' . $row['tpl_name'] . '.tpl" %>');
        $row['blocktpl'] = (($row['block_name'] != "") ? htmlspecialchars('<% include file="' . str_replace(array('{', '}'), '', $row['block_name']) . '.tpl" %>') : '');
        return $row;
    }

    /**
     * template_class::load_template()
     * 
     * @param mixed $id
     * @param integer $langid
     * @return
     */
    function load_template($id, $langid = 1) {
        $this->template = $this->db->query_first("SELECT T.*, TC.t_htalinklabel FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT .
            " TC WHERE T.id=TC.tid AND T.id='" . (int)$id . "' AND TC.lang_id=" . $langid . " LIMIT 1");
        $this->template = $this->set_options($this->template);
        $this->smarty->assign('TPLOBJ', $this->template);
    }


    /**
     * template_class::load_template_list()
     * 
     * @param integer $gbltemp
     * @param string $c_type
     * @param string $topl_id
     * @param string $mod
     * @return
     */
    function load_template_list($gbltemp = 0, $c_type = 'T', $topl_id = '', $mod = '') {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " 
 WHERE " . (($gbltemp == 1) ? "gbl_template=1" : "gbl_template=0") . "  
 " . (($mod != "") ? " AND modident='" . $mod . "'" : " AND modident=''") . "  
 	AND c_type='" . $c_type . "' " . ($_GET['tl'] > 0 ? " AND tl=" . $_GET['tl'] . "" : "") . " 
 	ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->tab[$row['id']] = $this->set_options($row);
        }
        if (count($this->tab) > 0)
            $this->tab = sort_db_result($this->tab, 'description ', SORT_ASC, SORT_STRING);
        $this->smarty->assign('tpl_list', $this->tab);
    }

    /**
     * template_class::delete_template()
     * 
     * @param mixed $id
     * @return
     */
    function delete_template($id, $force = false) {
        $id = (int)$id;
        $T_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id . " LIMIT 1");

        if ($T_OBJ['admin'] == 1 && $force == false)
            die('ACCESS DINIED');
        if ($T_OBJ['c_type'] == "T") {
            if (get_data_count(TBL_CMS_TEMPLATES, 'id', "parent=" . $id) > 0) {
                return 1;
            }
            $this->LOGCLASS->addLog('DELETE', 'webpage ' . $T_OBJ['description']);
        }

        # Bilder entfernen
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . $id . "'");
        while ($row = $this->db->fetch_array_names($result)) {
            delete_file(CMS_ROOT . 'file_server/template/' . $row['theme_image']);
            delete_file(CMS_ROOT . 'file_server/template/' . $row['t_icon']);
        }

        $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE (tid='" . $id . "') OR (content='' AND linkname='')");
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_TAGS_REL . " WHERE tag_pid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_tid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPLATE_PRE . " WHERE tc_tid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_ADMIN_PAGEACCESS . " WHERE p_id=" . $id);

        # TOPLEVEL ZUORDNUNG ENTFERNEN
        $this->remove_page_from_all_toplevel($id);

        # PERMISSIONS ENTFERNEN
        $this->db->query("DELETE FROM " . TBL_CMS_PERMISSIONS . " WHERE perm_tid=" . $id);
        # DELETE .TPL FILES
        foreach ((array )$this->DATA->LANGS as $key => $lang) {
            $target_root = SMARTY_TEMPDIR . $this->DATA->LANGS[$key]['local'] . '/';
            $filename = $target_root . $T_OBJ['tpl_name'] . ".tpl";
            if (file_exists($filename))
                @unlink($filename);
        }
        return 0;
    }

    /**
     * template_class::remove_page_from_all_toplevel()
     * 
     * @param mixed $tid
     * @return
     */
    function remove_page_from_all_toplevel($tid) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $trees = explode(";", $row['trees']);
            if (in_array($tid, $trees)) {
                foreach ($trees as $key => $tmplid) {
                    if ($tmplid == $tid)
                        unset($trees[$key]);
                }
            }
            $trees = array_unique($trees);
            $TREE_OBJ = array('trees' => "");
            $TREE_OBJ['trees'] = implode(';', $trees);
            update_table(TBL_CMS_TOPLEVEL, 'id', $row['id'], $TREE_OBJ);
        }
    }

    /**
     * template_class::save_tpl_file()
     * 
     * @param mixed $T_OBJ
     * @return
     */
    function save_tpl_file($T_OBJ) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $LANGS[$row['id']] = $row;
        }
        $target_root = SMARTY_TEMPDIR . $LANGS[$T_OBJ['lang_id']]['local'] . '/';
        $filename = $target_root . $T_OBJ['tpl_name'] . ".tpl";
        if ($T_OBJ['c_type'] == 'B')
            $filename = $target_root . str_replace(array('{', '}'), '', $T_OBJ['block_name']) . ".tpl";

        if (!is_dir(SMARTY_TEMPDIR))
            @mkdir(SMARTY_TEMPDIR);
        if (!is_dir($target_root))
            @mkdir($target_root);
        if ($T_OBJ['c_type'] == 'B' || ($T_OBJ['admin'] == 0 && !empty($T_OBJ['modident'])) || $T_OBJ['gbl_template'] == 1) {
            if (file_exists($filename))
                @unlink($filename);
            file_put_contents($filename, $T_OBJ['content']);
        }
    }

    /**
     * template_class::replicateland()
     * 
     * @param mixed $id
     * @param mixed $langid
     * @return
     */
    function replicateland($id, $langid) {
        $T_OBJ = $this->db->query_first("SELECT *,T.id AS TID,TC.id AS TCID FROM " . TBL_CMS_TEMPLATES . " T
	LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . (int)$langid . " AND T.id=TC.tid)
	WHERE T.id=" . (int)$id . "
	");
        if ($T_OBJ['TCID'] > 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE id<>" . (int)$langid);
            while ($row = $this->db->fetch_array_names($result)) {
                $T_OBJ['lang_id'] = $row['id'];
                $NEWROW = array();
                foreach ($T_OBJ as $key => $wert)
                    $NEWROW[$key] = $this->db->real_escape_string($T_OBJ[$key]);
                #replace_db_table(TBL_CMS_TEMPCONTENT, $NEWROW);
                $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . $T_OBJ['lang_id'] . " AND tid=" . $T_OBJ['tid'] . "");
                $this->db->query("INSERT INTO " . TBL_CMS_TEMPCONTENT . " SET lang_id=" . $T_OBJ['lang_id'] . ",tid=" . $T_OBJ['tid'] . ",linkname='" . $this->db->
                    real_escape_string($T_OBJ['linkname']) . "',content='" . $this->db->real_escape_string($T_OBJ['content']) . "'");
                $this->save_tpl_file($T_OBJ);
            }
        }
    }

    /**
     * template_class::gen_unique_htalabel()
     * 
     * @param mixed $label
     * @param integer $parent
     * @param integer $tid
     * @param integer $lang_id
     * @return
     */
    function gen_unique_htalabel($label, $parent = 0, $tid = 0, $lang_id = 1) {
        $k = 0;
        $org_label = $label;
        while (get_data_count(TBL_CMS_TEMPCONTENT, "*", "lang_id=" . $lang_id . " AND t_htalinklabel='" . $label . "'" . (($tid > 0) ? " AND tid <> " . $tid : "
            ")) > 0) {
            if ($parent > 0) {
                $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . (int)$parent);
                $label = $this->db->real_escape_string($TPL['description']) . '_' . $org_label . (($k == 0) ? '' : '_' . $k);
            }
            else
                $label = $org_label . '_' . $k;
            $k++;
        }
        return $label;
    }

    /**
     * template_class::add_single_langcon()
     * 
     * @param mixed $langid
     * @param mixed $tid
     * @param mixed $description
     * @param integer $parent
     * @return
     */
    function add_single_langcon($langid, $tid, $description, $parent = 0) {
        $linkname = preg_replace("/[^0-9a-zA-Z_-]/", "", $this->format_file_name($description));
        $linkname = $this->gen_unique_htalabel($linkname, $parent, $tid);
        $FORMC = array(
            'tid' => $tid,
            'linkname' => $description,
            't_htalinklabel' => $linkname,
            'content' => '');
        $FORMC['lang_id'] = (int)$langid;
        $cont_id = insert_table(TBL_CMS_TEMPCONTENT, $FORMC);
        $W = new websites_class();
        $W->add_std_page($cont_id, $tid, '<h2>' . $description . '</h2>');
        unset($W);
        // TPL erzeugen fuer header bereich
        $T_OBJ = $this->db->query_first("SELECT *, T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON(TC.lang_id=" . $langid .
            " AND T.id=TC.tid) WHERE TC.id=" . $tid);
        $this->save_tpl_file($T_OBJ);
        return $cont_id;
    }

    function add_page_to_toplevel($tid, $toplevel_id = 1) {
        $TOPLEVEL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id=" . $toplevel_id);
        $tree_arr = explode(";", $TOPLEVEL['trees']);
        $tree_arr[] = $tid;
        $tree_arr = array_unique($tree_arr);
        $arr = array('trees' => implode(';', $tree_arr));
        update_table(TBL_CMS_TOPLEVEL, 'id', $toplevel_id, $arr);
    }

    /**
     * template_class::insert_webcontent()
     * 
     * @param mixed $FORM
     * @return
     */
    function insert_webcontent($FORM) {
        $last_id = insert_table(TBL_CMS_TEMPLATES, $FORM);
        $A_OBJ['block_name'] = strtoupper('{TMPL_T_' . $this->remove_white_space($this->only_alphanums($FORM['description'])) . '_' . $last_id . '}');
        # erste seite?
        if (get_data_count(TBL_CMS_TEMPLATES, '*', "is_startsite=1") == 0) {
            $A_OBJ['is_startsite'] = 1;
        }
        update_table(TBL_CMS_TEMPLATES, 'id', $last_id, $A_OBJ);
        $this->LOGCLASS->addLog('INSERT', 'webpage <a href="' . $_SERVER['PHP_SELF'] . ' ? aktion=edit & id=' . $last_id . '">' . $FORM['description'] . '</a>');

        // Setze alle Toplevel
        if ($FORM['parent'] == 0) {
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder, description");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['trees'] .= ($row['trees'] != "") ? ';' . $last_id : $last_id;
                $tree_arr = explode(";", $row['trees']);
                $tree_arr = array_unique($tree_arr);
                unset($row['trees']);
                foreach ($tree_arr as $tpl_id)
                    $row['trees'] .= ($row['trees'] != "") ? ';' . $tpl_id : $tpl_id;
                update_table(TBL_CMS_TOPLEVEL, 'id', $row['id'], $row);
            }
        }

        // Lege Uebersetzung fuer alle Sprachen an
        foreach ((array )$this->DATA->LANGSFE as $langid => $value) {
            $this->add_single_langcon($langid, $last_id, $FORM['description'], $FORM['parent']);
        }

        // Setze oeffentliche Gruppe
        $this->db->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $last_id . ", perm_group_id=1000");
        return $last_id;
    }

    /**
     * template_class::import_template()
     * 
     * @param mixed $id
     * @return
     */
    function import_template($id) {
        $id = (int)$id;
        include_once (CMS_ROOT . 'admin/inc/update.class.php');
        $upt_obj = new upt_class();
        $upt_obj->db_zugriff = $this->db;
        $upt_obj->import_single_template($id);
        unset($upt_obj);
    }

    /**
     * template_class::fill_content()
     * 
     * @param mixed $row
     * @return
     */
    function fill_content($row) {
        if ($row['content'] == '' && (int)$row['TCID'] == 0) {
            $FC = $this->db->query_first("SELECT TC.* FROM " . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT .
                " TC ON(TC.lang_id=1 AND T.id=TC.tid) WHERE T.id=" . $row['TID'] . "");
            $FC['lang_id'] = $row['lang_id'];
            unset($FC['TCID']);
            unset($FC['id']);
            $FC = $this->real_escape($FC);
            $id = insert_table(TBL_CMS_TEMPCONTENT, $FC);
            $row = $FC;
        }
        return $row;
    }

    /**
     * template_class::rewrite_all_smarty_tpl()
     * 
     * @return
     */
    function rewrite_all_smarty_tpl() {
        global $CMSDATA;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE 1");
        while ($row = $this->db->fetch_array_names($result)) {
            $LANGS[$row['id']] = $row;
        }

        # SMARTY Templates anlegen
        $TEMPL_OBJ = new template_class(1, $CMSDATA);
        foreach ($LANGS as $id => $rowl) {
            $this->delete_dir_with_subdirs(SMARTY_TEMPDIR . $LANGS[$rowl['id']]['local']);
        }

        sleep(1);
        foreach ($LANGS as $id => $rowl) {
            $result = $this->db->query("SELECT *, T.id AS TID, TC.id AS TCID FROM " . TBL_CMS_TEMPLATES . " T 
            LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON(TC.lang_id=" . $rowl['id'] . " AND T.id=TC.tid) WHERE 1");
            while ($row = $this->db->fetch_array_names($result)) {
                $row['lang_id'] = $rowl['id'];
                $row = $this->fill_content($row);
                $TEMPL_OBJ->save_tpl_file($row);
            }

        }
    }

    /**
     * template_class::build_lang_select()
     * 
     * @return
     */
    function build_lang_select() {
        global $LNGOBJ;
        $LNGOBJ->init_uselang();
        $ulang_obj = $this->db->query_first("SELECT * FROM " . TBL_CMS_LANG . " WHERE id=" . (int)$_GET['uselang']);
        $ulang_obj['flag'] = kf::gen_thumbnail('/images/' . $ulang_obj['bild'], 30, 0, 0);
        $_SESSION['CNT_TABBEDLANG'] = $LNGOBJ->build_lang_select();
        $this->smarty->assign('ulang_obj', $ulang_obj);
    }


}

?>