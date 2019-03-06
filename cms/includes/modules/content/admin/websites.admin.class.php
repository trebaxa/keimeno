<?php

/**
 * @package    content
 *
 * @copyright  Copyright (C) 2006 - 2016 Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class websites_class extends keimeno_class {

    var $TEMPL_OBJ = array();
    var $langid = array();
    var $CMSDATA = array();
    var $WEBSITE = array();

    /**
     * websites_class::__construct()
     * 
     * @param mixed $CMSDATA
     * @param integer $langid
     * @return
     */
    function __construct($CMSDATA = array(), $langid = 1) {
        global $GRAPHIC_FUNC;
        parent::__construct();
        $this->TEMPL_OBJ = new template_class($langid, $CMSDATA);
        $this->langid = $langid;
        $this->DATA = $CMSDATA;
        $this->TEMPL_OBJ->set_lang_id($this->langid);
        $this->GRAPHIC_FUNC = $GRAPHIC_FUNC;
        if (!isset($_SESSION['toplevel'])) {
            $_SESSION['toplevel'] = 1;
        }

        if (!isset($_GET['tmsid']))
            $_GET['tmsid'] = md5('{LBL_NOTOPLEVEL}');
        if (isset($_REQUEST['toplevel']) && $_REQUEST['toplevel'] > 0) {
            $_SESSION['toplevel'] = (int)$_REQUEST['toplevel'];
        }
        else {
            $_GET['toplevel'] = $_SESSION['toplevel'];
        }

        $this->TCR = new kcontrol_class($this);
    }

    /**
     * websites_class::cmd_setallperm()
     * 
     * @return
     */
    public function cmd_setallperm() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='T'");
        while ($row = $this->db->fetch_array_names($result)) {
            $kdb->query("DELETE FROM " . TBL_CMS_PERMISSIONS . " WHERE perm_tid=" . $row['id'] . " AND perm_group_id=1000");
            $kdb->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $row['id'] . ", perm_group_id=1000");
        }
        $this->msg("{LBLA_SAVED}");
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }

    /**
     * websites_class::cmd_rootact()
     * 
     * @return
     */
    public function cmd_rootact() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE parent=0");
        $TOP = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id=1");
        $trees = realexplode($TOP['trees']);
        while ($row = $this->db->fetch_array_names($result)) {
            $trees[] = $row['id'];
        }
        $trees = array_unique($trees);
        foreach ($trees as $tree) {
            $ts .= (($ts != "") ? ';' : '') . $tree;
        }
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . "");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->db->query("UPDATE " . TBL_CMS_TOPLEVEL . " SET trees='" . $ts . "' WHERE id=" . $row['id']);
        }
        $this->msg("{LBLA_SAVED}");
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage']);
        exit;
    }

    /**
     * websites_class::cmd_a_delete()
     * 
     * @return
     */
    public function cmd_a_delete() {
        if ($_GET['id'] > 0) {
            $res = $this->TEMPL_OBJ->delete_template($_GET['id']);
            if ($res == 1) {
                $this->msge("{LBLA_NOT_DELETED} {LBL_HASSUBCONTENT}");
                HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&starttree=' . $_GET['starttree']);
                exit;
            }
            else
                if ($res == 0) {
                    $this->msg("{LBLA_DELETED}");
                    HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&starttree=' . $_GET['starttree']);
                    exit;
                }
        }
    }

    /**
     * websites_class::set_lang_id()
     * 
     * @param mixed $langid
     * @return
     */
    function set_lang_id($langid) {
        if ($langid == 0)
            $langid = 1;
        $this->langid = (int)$langid;
        $this->TEMPL_OBJ->set_lang_id($this->langid);
    }

    /**
     * websites_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->WEBSITE['seltopl'] = $_SESSION['toplevel'];
        $this->WEBSITE['langid'] = $this->langid;
        $this->smarty->assign('WEBSITE', $this->WEBSITE);
    }

    /**
     * websites_class::approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET approval='" . (int)$value . "' WHERE id=" . (int)$id . " LIMIT 1");
        $this->breadcrumb_update((int)$id);
    }

    /**
     * websites_class::cmd_themepicdelete()
     * 
     * @return
     */
    function cmd_themepicdelete() {
        $this->delete_theme_image($_GET['id'], $_GET['uselang']);
        $this->msg("{LBL_DELETED}");
        $this->hard_exit();
    }

    /**
     * websites_class::delete_theme_image()
     * 
     * @param mixed $id
     * @param mixed $langid
     * @return
     */
    function delete_theme_image($id, $langid) {
        $THEME = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . (int)$langid . " AND tid=" . (int)$id . " LIMIT 1");
        delete_file(CMS_ROOT . 'file_data/themeimg/' . $THEME['theme_image']);
        clean_cache_like('theme');
        $arr['theme_image'] = '';
        update_table(TBL_CMS_TEMPCONTENT, 'id', $THEME['id'], $arr);
    }

    /**
     * websites_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $parts = explode('-', $this->TCR->GET['id']);
        $id = $parts[1];
        $this->approve($this->TCR->GET['value'], $id);
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_delete_webpage_by_node()
     * 
     * @return
     */
    function cmd_delete_webpage_by_node() {
        $this->TEMPL_OBJ->delete_template($_GET['id']);
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_rename_webpage_by_node()
     * 
     * @return
     */
    function cmd_rename_webpage_by_node() {
        $FORM = $_REQUEST['FORM'];
        list($tmp, $tid) = explode('-', $_GET['id']);
        update_table(TBL_CMS_TEMPLATES, 'id', $tid, $FORM);
        echo json_encode(array('id' => $tid));
        $this->hard_exit();
    }

    /**
     * websites_class::delete_icon_image()
     * 
     * @param mixed $id
     * @param mixed $langid
     * @return
     */
    function delete_icon_image($id, $langid) {
        $THEME = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . (int)$langid . " AND tid=" . (int)$id . " LIMIT 1");
        if (delete_file(CMS_ROOT . 'file_data/menu/' . $THEME['t_icon'])) {
            $THEME['t_icon'] = '';
            update_table(TBL_CMS_TEMPCONTENT, 'id', $THEME['id'], $THEME);
        }
    }

    /**
     * websites_class::cmd_delete_icon_image()
     * 
     * @return
     */
    function cmd_delete_icon_image() {
        $this->delete_icon_image($_GET['ident'], $_GET['uselang']);
        $this->msg("{LBL_DELETED}");
        $this->ej('reload_website_settings');
    }

    /**
     * websites_class::build_website_tree()
     * 
     * @param mixed $menue_arr
     * @param mixed $allowed_treeids
     * @param bool $printoutall
     * @param mixed $top_arrays
     * @param mixed $forbidden_page_ids
     * @return
     */
    function build_website_tree(&$menue_arr, $allowed_treeids, $printoutall = false, $top_arrays = array(), $forbidden_page_ids = array()) {
        foreach ($menue_arr as $key => $child) {
            $menue_arr[$key]['printout'] = false;
            $has_no_toplevel_connection = (!in_array($child['id'], $top_arrays['all_used_pids_in_toplevel']) && $_SESSION['toplevel'] == 0 && $child['parent'] == 0);
            if (count($allowed_treeids) > 0 && $child['parent'] == 0 && in_array($child['id'], $allowed_treeids)) {
                $menue_arr[$key]['printout'] = true;
            }
            if (count($allowed_treeids) > 0 && $child['parent'] > 0) {
                $menue_arr[$key]['printout'] = true;
            }
            if ($has_no_toplevel_connection == true) {
                $menue_arr[$key]['printout'] = true;
            }
            if (count($forbidden_page_ids) > 0 && in_array($child['id'], $forbidden_page_ids)) {
                $menue_arr[$key]['printout'] = false;
            }
            if ($printoutall === true) {
                $menue_arr[$key]['printout'] = true;
            }
            if ($menue_arr[$key]['printout'] == true) {
                $menue_arr[$key]['haschildren'] = is_array($child['children']) && count($child['children']) > 0;
                if (is_array($child['children']))
                    $menue_arr[$key]['children'] = $this->build_website_tree($child['children'], $allowed_treeids, $printoutall, $top_arrays, $forbidden_page_ids);
            }
            else {
                unset($menue_arr[$key]);
            }
        }
        return $menue_arr;
    }

    /**
     * websites_class::cmd_load_website_tree()
     * 
     * @return
     */
    function cmd_load_website_tree() {
        $topl = (int)$_SESSION['toplevel'];
        $startree = (int)$startree;

        # load forbidden page ids
        $pageaccess = new pageaccess_class();
        $forbidden_page_ids = $pageaccess->load_page_access();

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $topl_treeids[$row['id']] = explode(";", $row['trees']);
        }
        if ($topl > 0)
            $allowed_treeids = $topl_treeids[$topl];
        else
            $allowed_treeids = array();

        $TREE = new nestedArrClass();
        $TREE->db = $this->db;
        $TREE->label_id = 'id';
        $TREE->create_result_and_array("SELECT id, parent, description, approval FROM " . TBL_CMS_TEMPLATES . "  WHERE gbl_template=0 AND c_type='T' 
        ORDER BY parent,morder", 0, 0, -1);
        $top_arrays = $this->buildTopLevelFilter();
        $this->WEBSITE['websitetree'] = $this->build_website_tree($TREE->menu_array, $allowed_treeids, false, $top_arrays, $forbidden_page_ids);
        $this->parse_to_smarty();
        kf::echo_template('website.tree');
    }

    /**
     * websites_class::cmd_load_pages()
     * 
     * @return
     */
    function cmd_load_pages() {
        global $LNGOBJ;
        $this->initmanager();
        $this->parse_to_smarty();
        kf::echo_template('websitemanager');
    }


    /**
     * websites_class::load_frameworks()
     * 
     * @return
     */
    function load_frameworks() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE is_framework=1");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->WEBSITE['frameworks'][] = $row;
        }
    }

    /**
     * websites_class::cmd_reload_theme_image()
     * 
     * @return
     */
    function cmd_reload_theme_image() {
        $arr = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id='" . (int)$_GET['content_id'] . "'LIMIT 1");
        $arr['theme_image'] .= '?a=' . gen_sid(8);
        echo json_encode($arr);
        $this->hard_exit();
    }


    /**
     * websites_class::load_website()
     * 
     * @param mixed $id
     * @return
     */
    function load_website($id) {
        $this->TEMPL_OBJ->load_template((int)$id, $this->langid);
        $this->TEMPL_OBJ->template['formcontent'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . (int)$id . "' AND lang_id='" . $this->
            langid . "' LIMIT 1");
        if ($this->TEMPL_OBJ->template['formcontent']['theme_image'] != "")
            $this->TEMPL_OBJ->template['formcontent']['theme_image'] .= '?a=' . gen_sid(8);
        $this->TEMPL_OBJ->template['no_fck'] = (strstr($this->TEMPL_OBJ->template['formcontent']['content'], '<%') && strstr($this->TEMPL_OBJ->template['formcontent']['content'],
            '%>')) ? 1 : $this->TEMPL_OBJ->template['no_fck'];
        $this->TEMPL_OBJ->template['oeditor'] = (($this->TEMPL_OBJ->template['no_fck'] == 0) ? create_html_editor('FORM[content]', $this->TEMPL_OBJ->template['formcontent']['content'],
            900, 'Full', 0, false, 'templcontent') : '<textarea class="form-control" class="se-html" rows="60" style="width:100%;" name="FORM[content]">' . trim(htmlspecialchars
            ($this->TEMPL_OBJ->template['formcontent']['content'])) . '</textarea>');
        #  $this->TEMPL_OBJ->template['icons']['import'] = ($this->TEMPL_OBJ->template['admin'] == 1) ? genImportImgTagConfirmAJAX($this->TEMPL_OBJ->template['id']) : '';
        $this->TEMPL_OBJ->template['urltpl'] = content_class::gen_url_template($id);
        $lang = new language_class();
        $this->TEMPL_OBJ->template['langfe'] = $lang->build_lang_select_smarty($this->langid);
        $this->smarty->assign('TPLOBJ', $this->TEMPL_OBJ->template);

        $this->load_contentmatrix($this->TEMPL_OBJ->template['formcontent']['id']);
        $this->load_frameworks();

        $this->WEBSITE['templselect'] = build_html_selectbox('FORM[t_themegalid]', TBL_CMS_TEMPLATES, 'id', 'tpl_name', " WHERE modident='gallery' AND layout_group='1'",
            $this->TEMPL_OBJ->template['formcontent']['t_themegalid']);
        $this->WEBSITE['nivo_effect'] = explode(',',
            'sliceDown,sliceDownLeft,sliceUp,sliceUpLeft,sliceUpDown,sliceUpDownLeft,fold,fade,random,slideInRight,slideInLeft,boxRandom,boxRain,boxRainReverse,boxRainGrow,boxRainGrowReverse');
        $this->WEBSITE['croppositions'] = explode(',', 'NorthWest,North,NorthEast,West,Center,East,SouthWest,South,SouthEast');

    }

    /**
     * websites_class::cmd_reload_website_settings()
     * 
     * @return
     */
    function cmd_reload_website_settings() {
        $this->load_website($_GET['id']);
        echo json_encode($this->TEMPL_OBJ->template['formcontent']);
        $this->hard_exit();
    }


    /**
     * websites_class::cmd_importtpl()
     * 
     * @return
     */
    function cmd_importtpl() {
        $tid = (int)$_GET['id'];
        $list = $this->curl_get_data(GETFILECMS . '?lang_id=1&tid=' . $tid . '&cmd=get_template_content_matrix');
        $cont_arr = json_decode($list);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_tid=" . $tid);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $tid);
        while ($row = $this->db->fetch_array_names($result)) {
            foreach ($cont_arr as $key => $c) {
                $FORM = array(
                    'tm_cid' => $row['id'],
                    'tm_tid' => $tid,
                    'tm_content' => $c,
                    'tm_type' => 'C',
                    'tm_order' => 0);
                $FORM = $this->real_escape($FORM);
                insert_table(TBL_CMS_TEMPMATRIX, $FORM);
            }
        }
        $this->hard_exit();
    }

    /**
     * websites_class::load_websites()
     * 
     * @param mixed $toplevel
     * @param mixed $starttree
     * @return
     */
    function load_websites($toplevel, $starttree) {
        $starttree = (int)$starttree;
        $toplevel = (int)$toplevel;
        $top_arrays = $this->buildTopLevelFilter();

        if ($toplevel > 0 && is_array($top_arrays['tl_treeids'][$toplevel])) {
            foreach ($top_arrays['tl_treeids'][$toplevel] as $treeid) {
                if ($treeid > 0)
                    $sql_addon .= ($sql_addon != "") ? " OR id=" . $treeid : "id=" . $treeid;
            }
            $sql_addon = ($sql_addon != "") ? $sql_addon = ' AND (' . $sql_addon . ')' : "";
        }


        $sql = "SELECT T.*
		FROM " . TBL_CMS_TEMPLATES . " T
		WHERE T.gbl_template=0 AND T.c_type='T' AND T.parent=" . (int)$starttree . "
		" . (($starttree == 0 && $toplevel > 0) ? $sql_addon : '') . "
		ORDER BY T.morder";
        $result = $this->db->query($sql);
        while ($row = $this->db->fetch_array_names($result)) {
            $has_no_toplevel_connection = (!in_array($row['id'], $top_arrays['all_used_pids_in_toplevel']) && $toplevel == 0 && $row['parent'] == 0);
            if ($has_no_toplevel_connection || ($toplevel > 0 && $sql_addon != "")) {
                $row['childcount'] = get_data_count(TBL_CMS_TEMPLATES, 'id', "parent=" . $row['id']);
                $row['icons'][] = kf::gen_edit_icon($row['id'], '&tl=' . $row['tl']);
                $row['icons'][] = kf::gen_approve_icon($row['id'], $row['approval']);
                $row['icons'][] = ($row['admin'] == 0) ? kf::gen_del_icon_reload($row['id'], 'a_delete', '{LBLA_CONFIRM}', '&starttree=' . $starttree) : '';
                #  $row['icons'][] = ($row['admin'] == 1) ? genImportImgTagConfirmAJAX($row['id']) : '';
                $row['morder'] *= 1;
                $websites[] = $row;
            }
        }
        $WLIST = array('websites' => $websites, 'toplevel_links' => $top_arrays['m_arr']);
        $this->smarty->assign('WLIST', $WLIST);
    }

    /**
     * websites_class::load_tree_pages()
     * 
     * @param mixed $topl
     * @param mixed $starttree
     * @return
     */
    function load_tree_pages($topl, $starttree) {
        $this->nodes = new cms_tree_class();
        $this->nodes->db = $this->db;
        $this->load_websites($topl, $starttree);
    }

    /**
     * websites_class::cmd_loadtreepages()
     * 
     * @return
     */
    function cmd_loadtreepages() {
        $this->load_tree_pages($_GET['toplevel'], $_GET['starttree']);
        $this->parse_to_smarty();
        kf::echo_template('website.pagetable');
    }

    /**
     * websites_class::buildTopLevelFilter()
     * 
     * @return
     */
    function buildTopLevelFilter() {
        // TOP_LEVEL FILTER
        $all_used_pids_in_toplevel = array();
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $m_arr[] = $row;
            $tl_treeids[$row['id']] = explode(";", $row['trees']);
            $all_used_pids_in_toplevel = array_merge($all_used_pids_in_toplevel, $tl_treeids[$row['id']]);
        }
        $m_arr[] = array(
            'description' => '{LBL_NORELATION}',
            'id' => 0,
            'approval' => 1);
        $this->WEBSITE['toplevel_tabs'] = $m_arr;
        return array(
            'all_used_pids_in_toplevel' => $all_used_pids_in_toplevel,
            'm_arr' => $m_arr,
            'tl_treeids' => $tl_treeids);
    }

    /**
     * websites_class::cmd_set_perm_to_public()
     * 
     * @return
     */
    function cmd_set_perm_to_public() {
        if (count($this->TCR->POST['pageids']) > 0) {
            foreach ($this->TCR->POST['pageids'] as $key => $id) {
                $this->set_permission_for_groups($id, array(1000));
            }
            $this->TCR->add_msg('{LBLA_SAVED}');
        }
        else {
            $this->TCR->add_msge('{LBL_NOSELECTED}');
        }
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * websites_class::set_permission_for_groups()
     * 
     * @param mixed $id
     * @param mixed $CUSTGROUP
     * @return
     */
    function set_permission_for_groups($id, $CUSTGROUP) {
        $id = (int)$id;
        $this->db->query("DELETE FROM " . TBL_CMS_PERMISSIONS . " WHERE perm_tid=" . $id);
        if (is_array($CUSTGROUP)) {
            foreach ($CUSTGROUP as $key => $group_id) {
                $this->db->query("INSERT INTO " . TBL_CMS_PERMISSIONS . " SET perm_tid=" . $id . ", perm_group_id=" . (int)$group_id);
            }
        }
    }

    /**
     * websites_class::cmd_save_redirect()
     * 
     * @return
     */
    function cmd_save_redirect() {
        update_table(TBL_CMS_TEMPLATES, 'id', $_POST['tid'], $_POST['FORM_TEMPLATE']);
        $this->ej();
    }


    /**
     * websites_class::cmd_genkeys()
     * 
     * @return
     */
    function cmd_genkeys() {
        $LANGSFE = $this->dao->load_frontend_languages();
        $FORM = $this->dao->load_template_content((int)$_REQUEST['conid']);
        $C = new content_class();
        $arr = $C->build_content($_REQUEST['conid'], 1, $_REQUEST['uselang'], $FORM['tid']);
        $this->smarty->addTemplateDir(CMS_ROOT . 'smarty/templates/' . $LANGSFE[$_REQUEST['uselang']]['local']);
        $html = main_class::compile_frontend($arr['content'], $_REQUEST['uselang']);
        kf::output(kf::gen_meta_keywords($html, 0, ',', $_REQUEST['uselang']));
    }

    /**
     * websites_class::cmd_genmeta()
     * 
     * @return
     */
    function cmd_genmeta() {
        $LANGSFE = $this->dao->load_frontend_languages();
        $FORM = $this->dao->load_template_content((int)$_REQUEST['conid']);
        $C = new content_class();
        $arr = $C->build_content($_REQUEST['conid'], 1, $_REQUEST['uselang'], $FORM['tid']);
        $this->smarty->addTemplateDir(CMS_ROOT . 'smarty/templates/' . $LANGSFE[$_REQUEST['uselang']]['local']);
        $html = main_class::compile_frontend($arr['content'], $_REQUEST['uselang']);
        $html = kf::gen_plain_text_content($html, $_REQUEST['lang_id']);
        $html = trim(substr($html, 0, (int)$this->gblconfig->metadesc_count));
        kf::output($html);
    }

    /**
     * websites_class::cmd_genmetatitle()
     * 
     * @return
     */
    function cmd_genmetatitle() {
        $FORM = $this->dao->load_template_content((int)$_REQUEST['conid']);
        kf::output($this->gbl_config['opt_site_title'] . ' ' . $FORM['linkname']);
    }

    /**
     * websites_class::cmd_save_meta()
     * 
     * @return
     */
    function cmd_save_meta() {
        global $MODULE;
        $FORM = (array )$_POST['FORM'];
        $FORM['meta_title'] = strip_tags($FORM['meta_title']);
        $FORM['meta_keywords'] = strip_tags(pure_translation($FORM['meta_keywords'], $_POST['uselang']));
        $FORM['meta_desc'] = strip_tags(pure_translation($FORM['meta_desc'], $_POST['uselang']));

        // Metas erzwingen
        if ($FORM['content'] != "") {
            $FORM['meta_keywords'] = ($FORM['meta_keywords'] != "") ? kf::format_meta($FORM['meta_keywords']) : '';
            $FORM['meta_desc'] = ($FORM['meta_desc'] != "") ? kf::format_meta($FORM['meta_desc']) : '';
        }
        $FORM['meta_desc'] = str_replace('\"', '', $FORM['meta_desc']);
        $FORM['meta_title'] = str_replace('"', '', $FORM['meta_title']);
        $FORM['meta_keywords'] = implode(',', keimeno_class::trim_array(explode(',', $FORM['meta_keywords'])));
        $FORM['meta_keywords'] = str_replace('"', '', $FORM['meta_keywords']);
        $this->dao->update_tpl_content($FORM, (int)$_POST['id']);
        $this->ej();
    }


    /**
     * websites_class::cmd_add_website()
     * 
     * @return
     */
    function cmd_add_website() {
        $id = $this->TEMPL_OBJ->insert_webcontent($_POST['FORM']);
        $breadcrumb_root_pageid = $this->get_root_element($id);
        $this->breadcrumb_update($breadcrumb_root_pageid);
        $_SESSION['last_parent'] = $_POST['FORM']['parent'];
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&id=' . $id . '&aktion=edit');
        $this->msg('{LBLA_SAVED}');
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_add_website_tree()
     * 
     * @return
     */
    function cmd_add_website_tree() {
        $id = $this->TEMPL_OBJ->insert_webcontent($_GET['FORM']);
        $breadcrumb_root_pageid = $this->get_root_element($id);
        $this->breadcrumb_update($breadcrumb_root_pageid);
        $_SESSION['last_parent'] = $_GET['FORM']['parent'];
        echo json_encode(array('id' => $id));
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_load_titletpl()
     * 
     * @return
     */
    function cmd_load_titletpl() {
        $this->load_website($_GET['tid']);
        $this->parse_to_smarty();
        kf::echo_template('website.edit.title');
    }

    /**
     * websites_class::save_pre_compiled_content()
     * 
     * @return void
     */
    function save_pre_compiled_content($conid) {
        try {
            error_reporting(0);
            $conid = (int)$conid;
            $LANGSFE = $this->dao->load_frontend_languages();
            $TC = $this->dao->load_template_content($conid);
            $C = new content_class();
            $arr = $C->build_content($conid, 1, $TC['lang_id'], $TC['tid']);
            $this->smarty->addTemplateDir(CMS_ROOT . 'smarty/templates/' . $LANGSFE[$TC['lang_id']]['local']);
            $arr['content'] = translate_language($arr['content'], $TC['lang_id']);
            $arr = $this->real_escape($arr);
            $FORM = array('t_precontent' => $arr['content']);
            $this->dao->update_tpl_content($FORM, (int)$conid);
        }
        catch (Exception $e) {
            #  echo 'Exception abgefangen: ', $e->getMessage(), "\n";
            $row = $this->real_escape($row);
            $LOG->addLog('FAILURE', 'Smarty compile error. TID: ' . $row['tid'] . '|' . $row['linkname']);
        }
    }

    /**
     * websites_class::cmd_save_content()
     * 
     * @return
     */
    function cmd_save_content() {
        $FORM = $_POST['FORM'];
        $FORM_TEMPLATE = $_POST['FORM_TEMPLATE'];
        if ($FORM['t_htalinklabel'] == "" || $FORM['linkname'] == "" || $FORM_TEMPLATE['description'] == "") {
            $this->msge('{LA_PFLICHFELDERBITTEAUSF}');
        }
        $FORM['t_htalinklabel'] = preg_replace("/[^0-9a-zA-Z_-]/", "", $this->format_file_name($FORM['t_htalinklabel']));
        if ($FORM['t_htalinklabel'] != "") {
            $FORM['t_htalinklabel'] = $this->TEMPL_OBJ->gen_unique_htalabel($FORM['t_htalinklabel'], $_POST['parent'], $_POST['tid'], $FORM['lang_id']);
        }


        if ($this->has_errors()) {
            $this->ej();
        }
        else {
            update_table(TBL_CMS_TEMPLATES, 'id', $_POST['tid'], $FORM_TEMPLATE);
            // Content anlegen
            if ($_POST['id'] > 0) {
                $this->dao->update_tpl_content($FORM, (int)$_POST['id']);
            }
            else {
                $FORM['tid'] = $_POST['tid'];
                insert_table(TBL_CMS_TEMPCONTENT, $FORM);
            }
            $this->breadcrumb_update($_POST['tid']);
        }
        $this->save_pre_compiled_content($_POST['id']);
        $this->ej('load_title_tpl');
    }

    /**
     * websites_class::create_default_dirs()
     * 
     * @return
     */
    function create_default_dirs() {
        $dir = CMS_ROOT . 'file_data/';
        $arr = array(
            $dir . 'template',
            $dir . 'template/css',
            $dir . 'template/scss',
            $dir . 'template/js',
            $dir . 'template/img',
            $dir . 'template/fonts',
            $dir . 'themeimg');
        foreach ($arr as $dirset) {
            if (!is_dir($dirset))
                mkdir($dirset, 0775);
        }
    }


    /**
     * websites_class::cmd_save_theme()
     * 
     * @return
     */
    function cmd_save_theme() {
        $this->create_default_dirs();
        $FORM = (array )$_POST['FORM']; // Content anlegen
        if ($_POST['content_id'] == 0) {
            $FORM['tid'] = $_POST['tid'];
            $FORM['lang_id'] = $_POST['uselang'];
            $content_id = insert_table(TBL_CMS_TEMPCONTENT, $FORM);
        }
        else {
            $content_id = $_POST['content_id'];
            $this->dao->update_tpl_content($FORM, (int)$content_id);
        }

        // UPLOAD THEME
        if (isset($_FILES['datei']) && $_FILES['datei']['name'] != "") {
            $ext_file = strtolower(strrchr($_FILES['datei']['name'], '.'));
            if (!validate_upload_file($_FILES['datei'], true)) {
                keimeno_class::msg($_SESSION['upload_msge']);
                $this->ej();
            }

            $target_file = CMS_ROOT . 'file_data/themeimg/theme_image_cp_' . (int)$_POST['uselang'] . '_' . (int)$_POST['tid'] . $ext_file;
            #delete_file($target_file);
            $this->delete_theme_image($_POST['tid'], $_POST['uselang']);
            if (move_uploaded_file($_FILES['datei']['tmp_name'], $target_file) === true) {
                chmod($target_file, 0755);
                $THEME = array('theme_image' => basename($target_file));
                $this->dao->update_tpl_content($THEME, (int)$content_id);
                graphic_class::resize_picture_imageick('../file_data/themeimg/' . basename($target_file), '../file_data/themeimg/' . basename($target_file), 2100, 2000);
            }
        }
        keimeno_class::msg('{LBLA_SAVED}');
        $this->ej('reload_theme_image', $content_id);
    }

    /**
     * websites_class::get_root_element()
     * 
     * @param mixed $tid
     * @return
     */
    function get_root_element($tid) {
        $T = $this->dao->load_template($tid);
        while ($T['parent'] > 0) {
            $T = $this->dao->load_template($T['parent']);
        }
        return (int)$T['id'];
    }


    /**
     * websites_class::cmd_ax_sort_tree()
     * 
     * @return
     */
    function cmd_ax_sort_tree() {
        $next_node_id = (int)$_GET['next_node_id'];
        $prev_node_id = (int)$_GET['prev_node_id'];
        $parent = (int)$_GET['parent'];
        $next_node = $this->dao->load_template($next_node_id);
        $prev_node = $this->dao->load_template($prev_node_id);

        if ($next_node['id'] > 0) {
            $morder = $next_node['morder'] - 1;
        }
        else
            if ($prev_node['id'] > 0) {
                $morder = $prev_node['morder'] + 1;
            }
            else {
                $morder = 0;
            }

            update_table(TBL_CMS_TEMPLATES, 'id', $_GET['tid'], array('parent' => $parent, 'morder' => $morder));

        # resave order
        $topl_id = (int)$_SESSION['toplevel'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $topl_treeids[$row['id']] = explode(";", $row['trees']);
        }
        if ($topl_id > 0)
            $allowed_treeids = $topl_treeids[$topl_id];
        else
            $allowed_treeids = array();

        $k = 0;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE parent=" . $parent . " ORDER BY morder");
        while ($row = $this->db->fetch_array_names($result)) {
            if (in_array($row['id'], $allowed_treeids) || $parent > 0) {
                $k += 10;
                $this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET morder=" . $k . " WHERE id=" . $row['id']);
            }
        }

        if ($parent == 0) {
            $TOPLEVEL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TOPLEVEL . " WHERE id=1");
            $treeids = explode(";", $TOPLEVEL['trees']);
            $treeids[] = $_GET['tid'];
            $treeids = array_unique($treeids);
            update_table(TBL_CMS_TOPLEVEL, 'id', 1, array('trees' => implode(';', $treeids)));
        }

        #rebuild breadcrumb
        $this->breadcrumb_update($parent);
        # $this->breadcrumb_update($thisnode['parent']);

        $this->hard_exit();
    }


    /**
     * websites_class::cmd_save_website()
     * 
     * @return
     */
    function cmd_save_website_settings() {
        global $MODULE;
        $FORM = $_POST['FORM'];
        $FORM_TEMPLATE = (array )$_POST['FORM_TEMPLATE'];
        $FORM_TEMPLATE['show_rss_link'] = (int)$FORM_TEMPLATE['show_rss_link'];
        $FORM_TEMPLATE['tagable'] = (int)$FORM_TEMPLATE['tagable'];
        $FORM_TEMPLATE['xml_sitemap'] = (int)$FORM_TEMPLATE['xml_sitemap'];

        // Content anlegen
        if ($_POST['content_id'] == 0) {
            $FORM['tid'] = $_POST['tid'];
            $FORM['lang_id'] = $_POST['uselang'];
            $content_id = insert_table(TBL_CMS_TEMPCONTENT, $FORM);
        }
        else
            $content_id = $_POST['content_id'];
        if ($FORM_TEMPLATE['is_startsite'] == 1) {
            $this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET is_startsite=0 WHERE 1");
        }

        #   if ($FORM_TEMPLATE['parent'] > 0) {
        $T = $this->dao->load_template($_POST['tid']);
        #     if ($FORM_TEMPLATE['parent'] != $T['parent']) {
        $breadcrumb_pre_tid = $this->get_root_element($T['id']);
        #    }
        #  }

        // UPLOAD ICON
        if ($_FILES['dateiicon']['name'] != "") {
            $ext_file = strtolower(strrchr($_FILES['dateiicon']['name'], '.'));
            if (!validate_upload_file($_FILES['dateiicon'], true)) {
                keimeno_class::msg($_SESSION['upload_msge']);
                HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&starttree=' . $_POST['starttree'] . '&uselang=' . $_POST['uselang'] . '&toplevel=' .
                    $_POST['toplevel'] . '&tmsid=' . $_POST['tmsid'] . '&aktion=edit&id=' . $_POST['tid']);
                exit;
            }
            if (!is_dir(CMS_ROOT . 'file_data/menu'))
                mkdir(CMS_ROOT . 'file_data/menu', 0755);
            $target_file = CMS_ROOT . 'file_data/menu/icon_menu_' . (int)$_POST['uselang'] . '_' . (int)$_POST['tid'] . $ext_file;
            delete_file($target_file);
            if (move_uploaded_file($_FILES['dateiicon']['tmp_name'], $target_file) === true) {
                chmod($target_file, 0755);
                $ICON = array();
                $ICON['t_icon'] = basename($target_file);
                update_table(TBL_CMS_TEMPCONTENT, 'id', $content_id, $ICON);
            }
        }

        # save
        $this->dao->update_template($FORM_TEMPLATE, $_POST['tid']);
        $this->dao->update_tpl_content($FORM, $content_id);

        $T_OBJ = $this->db->query_first("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $_POST['uselang'] . " AND T.id=TC.tid)
		WHERE T.id=" . $_POST['tid'] . "
		");
        $this->TEMPL_OBJ->save_tpl_file($T_OBJ);

        # Modul verlinkung zu PHP
        $MODUPD = array();
        if ($FORM_TEMPLATE['module_id'] != "") {
            $MODUPD['php'] = $MODULE[$FORM_TEMPLATE['module_id']]['php'];
            $this->dao->update_template($MODUPD, $_POST['tid']);
        }

        # TOPLEVEL ZUORDNUNG
        if (!is_array($_POST['TLACTIVE']) || $_POST['FORM_TEMPLATE']['parent'] > 0) {
            $_POST['TLACTIVE'] = array();
        }
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $trees = explode(";", $row['trees']);
            if (in_array($row['id'], $_POST['TLACTIVE']))
                $trees[] = $_POST['tid'];
            else {
                foreach ($trees as $key => $tmplid) {
                    if ($tmplid == $_POST['tid'])
                        unset($trees[$key]);
                }
            }
            $trees = array_unique($trees);
            $TREE_OBJ['trees'] = "";
            foreach ($trees as $tid => $tree_id) {
                $TREE_OBJ['trees'] .= ($TREE_OBJ['trees'] != "" ? ";" : "") . $tree_id;
            }
            update_table(TBL_CMS_TOPLEVEL, 'id', $row['id'], $TREE_OBJ);
        }

        # Permissions setzen
        $this->set_permission_for_groups($_POST['tid'], $_POST['CUSTGROUP']);
        $this->LOGCLASS->addLog('MODIFY', 'webpage <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&aktion=edit&id=' . $_POST['tid'] . '">' . $FORM['linkname'] .
            '</a>');
        # Bread Crumb
        if ($breadcrumb_pre_tid > 0)
            $this->breadcrumb_update($breadcrumb_pre_tid);
        $this->breadcrumb_update($_POST['tid']);
        if ($_POST['doreload'] == 1) {
            self::msg('{LBLA_SAVED}');
            HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&starttree=' . $_POST['starttree'] . '&uselang=' . $_POST['uselang'] . '&toplevel=' .
                $_POST['toplevel'] . '&tmsid=' . $_POST['tmsid'] . '&aktion=edit&id=' . $_POST['tid'] . '&tabid=' . (int)$_POST['tabid']);
        }
        if ($_POST['is_json_form'] == 1) {
            $this->ej('reload_website_settings');
        }
        $this->hard_exit();
    }

    /**
     * websites_class::breadcrumb_update()
     * 
     * @param mixed $tid
     * @return
     */
    function breadcrumb_update($tid) {
        $tid = (int)$tid;
        $childs = $this->build_flatarr_of_children($tid);
        $this->dao->update_template(array('tid_childs' => serialize($childs['id_array'])), $tid);
        $result = $this->db->query("SELECT C.id, TC.id AS TCID, TC.lang_id FROM " . TBL_CMS_TEMPLATES . " C, " . TBL_CMS_TEMPCONTENT . " TC 
        WHERE TC.tid=C.id" . (($childs['sql_cid_filter'] != "") ? " AND " . $childs['sql_cid_filter'] : ""));
        while ($row = $this->db->fetch_array_names($result)) {
            $bread_crumb = array();
            $breadcrumb_str = "";
            $this->create_path($row['id'], $row['lang_id'], $bread_crumb);
            $bread_crumb = $this->real_escape(array_reverse($bread_crumb, true));
            foreach ($bread_crumb as $bread) {
                $breadcrumb_str .= '/' . trim($bread['label']);
            }
            $this->dao->update_tpl_content(array('t_breadcrumb_arr' => serialize($bread_crumb), 't_breadcrumb' => $breadcrumb_str), $row['TCID']);
        }
    }

    /**
     * websites_class::create_path()
     * 
     * @param mixed $tid
     * @param mixed $langid
     * @param mixed $bread_crumb
     * @return
     */
    function create_path($tid, $langid, &$bread_crumb) {
        $C = $this->db->query_first("SELECT linkname,parent,t_htalinklabel,C.id,C.approval FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " C 
        WHERE C.id=TC.tid AND TC.tid=" . $tid . " AND lang_id=" . $langid);
        $url_label = ($C['t_htalinklabel'] == "") ? $C['linkname'] : $C['t_htalinklabel'];
        $tid = ($C['t_htalinklabel'] != "") ? 0 : $tid;
        #  if ($C['approval'] == 1) {
        $bread_crumb[] = array(
            'label' => $C['linkname'],
            'parent' => $C['parent'],
            'link' => gen_page_link($tid, $url_label, $langid),
            'id' => $C['id'],
            'approved' => $C['approval']);
        # }
        if ($C['parent'] > 0) {
            $this->create_path($C['parent'], $langid, $bread_crumb);
        }
    }

    /**
     * websites_class::build_flatarr_of_children()
     * 
     * @param mixed $tid
     * @return
     */
    function build_flatarr_of_children($tid) {
        $tid = (int)$tid;
        $menutree = new nestedArrClass();
        $menutree->db = $this->db;
        $menutree->init(array(
            'label_column' => 'description',
            'label_parent' => 'parent',
            'label_id' => 'id',
            'sign' => '|_'));
        $menutree->create_result_and_array("SELECT * FROM " . TBL_CMS_TEMPLATES . " ORDER BY parent,description", $tid, 0, -1);
        $menutree->build_flat_obj_arr($menutree->menu_array, $child_ids);
        if (is_array($child_ids))
            foreach ($child_ids as $child)
                $ids[] = $child['id'];
        $ids[] = $tid;
        $ids = array_unique($ids);
        $cids_str = implode(',', $ids);
        if ($cids_str[0] == ',')
            $cids_str = substr(1, $cids_str);
        if (substr($cids_str, -1) == ',')
            $cids_str = substr($cids_str, 0, -1);
        if ($cids_str != "")
            $sql_cid_filter = " C.id IN (" . $cids_str . ") ";
        unset($menutree);
        return array(
            'cat_ids' => $child_ids,
            'sql_cid_filter' => $sql_cid_filter,
            'id_array' => (array )$ids);
    }

    /**
     * websites_class::reload_contentm()
     * 
     * @param mixed $tm_cid
     * @return
     */
    function reload_contentm($tm_cid) {
        $this->initmanager();
        $this->load_contentmatrix($tm_cid);
        $this->parse_to_smarty();
        kf::echo_template('website.addcontent');
    }

    /**
     * websites_class::cmd_reload_cont_table()
     * 
     * @return
     */
    function cmd_reload_cont_table() {
        $this->reload_contentm($_GET['id']);
    }

    /**
     * websites_class::resaved_sorted()
     * 
     * @param mixed $tm_cid
     * @param mixed $tm_pos
     * @return
     */
    function resaved_sorted($tm_cid, $tm_pos) {
        $k = 0;
        $result = $this->db->query("SELECT * FROM  " . TBL_CMS_TEMPMATRIX . "  
                WHERE tm_cid=" . (int)$tm_cid . " AND tm_pos=" . (int)$tm_pos . "
                ORDER BY tm_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $k += 10;
            $upt = array('tm_order' => $k);
            update_table(TBL_CMS_TEMPMATRIX, 'id', $row['id'], $upt);
        }
    }

    /**
     * websites_class::cmd_sort_content_table()
     * 
     * @return void
     */
    function cmd_sort_cm_table() {
        $ids = (string )$_GET['ids'];
        $ids = explode(',', $ids);
        $k = 0;
        foreach ($ids as $id) {
            $k += 10;
            dao_class::update_table(TBL_CMS_TEMPMATRIX, array('tm_order' => $k), array('id' => $id));
        }
        $this->ej();
    }

    /**
     * websites_class::cmd_axshow_editor()
     * 
     * @return
     */
    function cmd_axshow_editor() {
        $id = (int)$_GET['id'];
        $tm_type = $_GET['tm_type'];
        $tm_plugid = $_GET['tm_plugid'];
        $tm_modident = $_GET['tm_modident'];
        $tm_pos = $_GET['tm_pos'];
        $tm_cid = $_GET['tm_cid'];
        $this->gen_js_boxen();

        if ($tm_cid == 0) {
            $tm_cid = $this->TEMPL_OBJ->add_single_langcon($_GET['langid'], $_GET['tid'], $description);
        }


        if ($id > 0) {
            $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $id);
            $tm_type = $R['tm_type'];
            $tm_plugid = $R['tm_plugid'];
            $R['tm_plugform'] = unserialize($R['tm_plugform']);
        }
        else {
            $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id=" . $tm_cid);
            $arr = array(
                'tm_pos' => $tm_pos,
                'tm_type' => $tm_type,
                'tm_tid' => $C['tid'],
                'tm_cid' => $tm_cid,
                'tm_plugid' => $tm_plugid,
                'tm_modident' => $tm_modident);
            $id = insert_table(TBL_CMS_TEMPMATRIX, $arr);
            # sorting
            $after = (int)$_GET['after'];
            $this->resaved_sorted($tm_cid, $tm_pos);
            if ($after == 0) { # ON TOP
                $upt = array('tm_order' => 1);
                update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $upt);
                $this->resaved_sorted($tm_cid, $tm_pos);
            }
            else { # SAVE AFTER ELEMENT
                $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $after);
                $next = $C['tm_order'] + 1;
                $upt = array('tm_order' => $next);
                update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $upt);
                $this->resaved_sorted($tm_cid, $tm_pos);
            }

            $R = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $id);
        }
        if ($tm_type == 'W') {
            $this->WEBSITE['fck'] = create_html_editor('FORM[tm_content]', $R['tm_content'], 450, 'Full', 0, false, 'templcontent');
        }
        if ($tm_type == 'P') {
            $this->WEBSITE['PLUGIN'] = $this->load_content_plugins_by_mod($tm_plugid, $id);
        }
        if ($tm_type == 'S') {
            $this->WEBSITE['SYSTPL'] = $this->load_systpl();
        }
        $R['urltpl'] = content_class::gen_url_template($R['tm_tid']);
        $R['tm_plugform'] = (array )$R['tm_plugform'];
        $this->WEBSITE['node'] = $R;
        $this->parse_to_smarty();
        kf::echo_template('website.axeditor');
    }

    /**
     * websites_class::convert_pasted_image()
     * 
     * @param mixed $html
     * @return
     */
    function convert_pasted_image($html) {
        if ($this->gbl_config['gra_scrsave'] == 0)
            return $html;
        if (!is_dir(CMS_ROOT . 'file_server/posts/'))
            mkdir(CMS_ROOT . 'file_server/posts/', 0775);
        # try to get h1, h2 , h3 for filename
        for ($i = 1; $i <= 6; $i++) {
            preg_match_all('=<h' . $i . '>(.*)</h' . $i . '>=siU', $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $tag) {
                $img_title = strip_tags($tag);
            }
            if ($img_title != "")
                break;
        }
        if ($img_title == "") {
            $img_title = md5(gen_sid(8));
        }

        if (strstr($html, 'data:image/png;base64,')) {
            preg_match_all('=data:image/png;base64,(.*)"=siU', $html, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $image_bin) {
                $rep = array("data:image/png;base64,", '\"');
                $base64_data = (str_replace($rep, "", $image_bin));
                $filename = CMS_ROOT . 'file_server/posts/' . $this->unique_filename(CMS_ROOT . 'file_server/posts/', $img_title . '.png');
                file_put_contents($filename, base64_decode($base64_data));
                $html = str_replace('data:image/png;base64,' . $base64_data, 'file_server/posts/' . basename($filename), $html);
            }
        }
        return $html;
    }

    /**
     * websites_class::cmd_add_content()
     * 
     * @return
     */
    function cmd_add_content() {
        $FORM = (array )$_POST['FORM'];
        $PLUGFORM = self::arr_stripslashes((array )$_POST['PLUGFORM']);
        $FORM['tm_plugform'] = serialize($PLUGFORM);
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id=" . $FORM['tm_cid']);
        $FORM['tm_tid'] = $C['tid'];
        $tm_cid = $FORM['tm_cid'];


        # transform path
        $FORM['tm_content'] = str_replace(array(
            'http://' . FM_DOMAIN . PATH_CMS,
            'http://www.' . FM_DOMAIN . PATH_CMS,
            'https://' . FM_DOMAIN . PATH_CMS,
            'https://www.' . FM_DOMAIN . PATH_CMS), '/', $FORM['tm_content']);
        # convert copy and paste images
        $FORM['tm_content'] = $this->convert_pasted_image($FORM['tm_content']);

        # if system template
        if ($_POST['PLUGFORM']['systplid'] > 0) {
            $T = $this->dao->load_template($_POST['PLUGFORM']['systplid']);
            $FORM['tm_content'] = '<%include file="' . $T['tpl_name'] . '.tpl"%>';
            $FORM['tm_pluginfo'] = $this->db->real_escape_string($T['description']);
        }

        # fix wysing editor url link format
        if (strstr($FORM['tm_content'], '/{URL_TPL_')) {
            $FORM['tm_content'] = str_replace('/{URL_TPL_', '{URL_TPL_', $FORM['tm_content']);
        }

        $id = (int)$_POST['id'];
        update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $FORM);

        #exec plugin save
        if ($FORM['tm_plugid'] != "") {
            try {
                $plugin = $this->simple_load_plugin($FORM['tm_plugid']);
                $upt = array('tm_plugname' => strval($plugin->name));
                update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $upt);
                if (isset($plugin->save)) {
                    $class_name = strval($plugin->save->attributes()->classname);
                    $function = strval($plugin->save->attributes()->function);
                    $tmp_class = new $class_name();
                    if (method_exists($tmp_class, $function)) {
                        $tmp_class->$function(array(
                            'id' => $id,
                            'FORM' => $_POST['PLUGFORM'],
                            'POSTFORM' => $_POST['FORM']));
                    }
                }
            }
            catch (kException $e) {
                #die($e->get_error_message());
                self::msge($e->get_error_message());
                $this->ej('content_saved_successful');
            }
        }

        # save pre compiled version
        $this->save_compiled_version($tm_cid);
        $this->save_pre_compiled_content($tm_cid);
        $this->ej('content_saved_successful');
        # $this->hard_exit();
    }

    /**
     * websites_class::save_compiled_version()
     * 
     * @param mixed $tm_cid
     * @return
     */
    function save_compiled_version($tm_cid) {
        $PAGE = $this->db->query_first("SELECT T.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " IC  WHERE T.id=IC.tm_tid AND IC.tm_cid=" . (int)$tm_cid .
            " LIMIT 1");
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE id=" . $tm_cid);
        $hotspots = $this->get_all_hotspots($C['lang_id'], $PAGE['use_framework']);
        $CONT_FE = new content_class();
        foreach ($hotspots as $key => $spot) {
            # build content
            $this->db->query("DELETE FROM " . TBL_CMS_TEMPLATE_PRE . " WHERE tc_cid=" . $tm_cid . " AND tc_pos=" . $spot['number']);
            $compiled_content = $CONT_FE->build_content($tm_cid, $spot['number'], $C['lang_id'], $C['tid']);

            # fix wysing editor url link format
            if (strstr($compiled_content['content'], '/{URL_TPL_')) {
                $compiled_content['content'] = str_replace('/{URL_TPL_', '{URL_TPL_', $compiled_content['content']);
            }

            # save
            $arr = array(
                'tc_cid' => $tm_cid,
                'tc_tid' => $C['tid'],
                'tc_pos' => $spot['number'],
                'tc_interpreter' => serialize($CONT_FE->exec_interpreter),
                'tc_globaltpl' => ($compiled_content['content'] == true) ? 1 : 0,
                'tc_content' => $compiled_content['content']);
            insert_table(TBL_CMS_TEMPLATE_PRE, $this->real_escape($arr));
        }
        $this->allocate_memory($CONT_FE);
    }

    /**
     * websites_class::cmd_axdelcon()
     * 
     * @return
     */
    function cmd_axdelcon() {
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . (int)$this->TCR->GET['id']);
        $this->msg('{LBLA_DELETED}');
        $this->ej();
    }

    /**
     * websites_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave() {
        $cats = (array )$_POST['CATS'];
        if (is_array($cats) && count($cats) > 1) {
            $cats = $this->sort_multi_array($cats, 'morder', SORT_ASC, SORT_NUMERIC);
            $k = 0;
            foreach ($cats as $key => $row) {
                $k += 10;
                $id = $row['id'];
                unset($row['id']);
                $row['morder'] = ($k < 0) ? 10 : $k;
                update_table(TBL_CMS_TEMPLATES, 'id', $id, $row);
            }
        }
        $this->load_tree_pages($_POST['toplevel'], $_POST['starttree']);
        $this->parse_to_smarty();
        kf::echo_template('website.pagetable');
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_moveup()
     * 
     * @return
     */
    function cmd_moveup() {
        $id = (int)$_GET['id'];
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $id);
        $next = $C['tm_order'] -= 11;
        $upt = array('tm_order' => $next);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $upt);
        $this->resaved_sorted($C['tm_cid'], $C['tm_pos']);
        $this->reload_contentm($C['tm_cid']);
    }

    /**
     * websites_class::cmd_movedown()
     * 
     * @return
     */
    function cmd_movedown() {
        $id = (int)$_GET['id'];
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . $id);
        $next = $C['tm_order'] += 11;
        $upt = array('tm_order' => $next);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $id, $upt);
        $this->resaved_sorted($C['tm_cid'], $C['tm_pos']);
        $this->reload_contentm($C['tm_cid']);
    }

    /**
     * websites_class::load_pages_by_topl_and_st()
     * 
     * @param mixed $toplevel
     * @param mixed $starttree
     * @return
     */
    function load_pages_by_topl_and_st($toplevel, $starttree) {
        $top_arrays = $this->buildTopLevelFilter();
        if ($toplevel > 0 && is_array($top_arrays['tl_treeids'][$toplevel])) {
            foreach ($top_arrays['tl_treeids'][$toplevel] as $treeid) {
                if ($treeid > 0)
                    $sql_addon .= ($sql_addon != "") ? " OR id=" . $treeid : "id=" . $treeid;
            }
            $sql_addon = ($sql_addon != "") ? $sql_addon = ' AND (' . $sql_addon . ')' : "";
        }
        $result = $this->db->query("SELECT T.*
		FROM " . TBL_CMS_TEMPLATES . " T
		WHERE T.gbl_template=0 AND T.c_type='T' AND T.parent=" . (int)$starttree . "
		" . (($starttree == 0 && $toplevel > 0) ? $sql_addon : '') . "
		ORDER BY T.morder");
        while ($row = $this->db->fetch_array_names($result)) {
            $pages[] = $row;
        }
        return (array )$pages;
    }

    /**
     * websites_class::resaved_sorted_pages()
     * 
     * @param mixed $toplevel
     * @param mixed $starttree
     * @return
     */
    function resaved_sorted_pages($toplevel, $starttree) {
        $k = 0;
        $pages = $this->load_pages_by_topl_and_st($toplevel, $starttree);
        foreach ($pages as $page) {
            $k += 10;
            $upt = array('morder' => $k);
            update_table(TBL_CMS_TEMPLATES, 'id', $page['id'], $upt);
        }
    }

    /**
     * websites_class::cmd_pmoveup()
     * 
     * @return
     */
    function cmd_pmoveup() {
        $id = (int)$_GET['id'];
        $st = (int)$_GET['starttree'];
        $toplevel = (int)$_GET['toplevel'];
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id);
        $next = $C['morder'] -= 11;
        $upt = array('morder' => $next);
        update_table(TBL_CMS_TEMPLATES, 'id', $id, $upt);
        $this->resaved_sorted_pages($toplevel, $st);
        $this->load_tree_pages($toplevel, $st);
        $this->parse_to_smarty();
        kf::echo_template('website.pagetable');
    }

    /**
     * websites_class::cmd_pmovedown()
     * 
     * @return
     */
    function cmd_pmovedown() {
        $id = (int)$_GET['id'];
        $st = (int)$_GET['starttree'];
        $toplevel = (int)$_GET['toplevel'];
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id);
        $next = $C['morder'] += 11;
        $upt = array('morder' => $next);
        update_table(TBL_CMS_TEMPLATES, 'id', $id, $upt);
        $this->resaved_sorted_pages($toplevel, $st);
        $this->load_tree_pages($toplevel, $st);
        $this->parse_to_smarty();
        kf::echo_template('website.pagetable');
    }

    /**
     * websites_class::add_plugin()
     * 
     * @return void
     */
    function add_plugin($plugin, $module) {
        $key = strtoupper((string )$plugin->id);
        $this->WEBSITE['plugins'][$key] = array(
            'id' => (string )$plugin->id,
            'plug_name' => (string )$plugin->name,
            'tm_type' => 'P',
            'modident' => (string )$module->settings->id);
    }

    /**
     * websites_class::load_plugins()
     * 
     * @return
     */
    function load_plugins() {
        $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
        foreach ($xml_modules->modules->children() as $module) {
            if (isset($module->contentplugins)) {
                $childs = (array )$module->contentplugins->children();
                foreach ($childs as $plugin) {
                    if (!is_array($plugin)) {
                        $this->add_plugin($plugin, $module);
                    }
                    else {
                        foreach ($plugin as $plugin_child) {
                            $this->add_plugin($plugin_child, $module);
                        }
                    }
                }
            }
        }

        $this->WEBSITE['first_plugs']['FLXT_INLAY'] = $this->WEBSITE['plugins']['FLXT_INLAY'];
        unset($this->WEBSITE['plugins']['FLXT_INLAY']);

        $this->WEBSITE['first_plugs']['WYSIWYG'] = array(
            'id' => '',
            'tm_type' => 'W',
            'plug_name' => 'Text mit Bilder (WYSIWYG Editor)',
            'modident' => '');
        $this->WEBSITE['first_plugs']['SCRIPTCODE'] = array(
            'id' => '',
            'tm_type' => 'C',
            'plug_name' => 'HTML/Script Code',
            'modident' => '');
        $this->WEBSITE['first_plugs']['SYSTEMTPL'] = array(
            'id' => '',
            'tm_type' => 'S',
            'plug_name' => 'System Template',
            'modident' => '');


        ksort($this->WEBSITE['plugins']);
    }

    /**
     * websites_class::simple_load_plugin()
     * 
     * @param mixed $plugin_id
     * @return
     */
    function simple_load_plugin($plugin_id) {
        try {
            $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            foreach ($xml_modules->modules->children() as $module) {
                if (isset($module->contentplugins)) {
                    foreach ($module->contentplugins->children() as $plugin) {
                        if ($plugin->id == $plugin_id) {
                            return (object)$plugin;
                        }
                    }
                }
            }
        }
        catch (kException $e) {
            die($e->get_error_message());
        }
    }

    /**
     * websites_class::load_systpl()
     * 
     * @return
     */
    function load_systpl() {
        global $MODULE;
        #   echoarr($MODULE);
        $result = $this->db->query("SELECT T.*
		FROM " . TBL_CMS_TEMPLATES . " T
		WHERE T.gbl_template=1 AND T.tpl_name!='' AND is_framework=0
		ORDER BY T.modident,T.description");
        while ($row = $this->db->fetch_array_names($result)) {
            if (isset($MODULE[$row['modident']]['id']) || $row['modident'] == "") {
                $row['modident'] = ($row['modident'] == "") ? 'System' : $row['modident'];
                $SYS['pages'][$row['modident']]['description'] = ($row['modident'] != "System") ? ucfirst($MODULE[$row['modident']]['module_name']) : 'System';
                $SYS['pages'][$row['modident']]['children'][] = $row;
            }
        }
        #   echoarr($SYS);
        return $SYS;
    }

    /**
     * websites_class::load_content_plugins_by_mod()
     * 
     * @param mixed $plugin_id
     * @param mixed $tpl_id
     * @return
     */
    function load_content_plugins_by_mod($plugin_id, $tpl_id) {
        try {
            $xml_modules = simplexml_load_file(MODULE_ROOT . 'config_all_modules.xml');
            foreach ($xml_modules->modules->children() as $module) {
                if (isset($module->contentplugins)) {
                    foreach ($module->contentplugins->children() as $plugin) {
                        if ($plugin->id == $plugin_id) {
                            $plugin->tpl .= '.tpl';
                            if (isset($plugin->lists)) {
                                foreach ($plugin->lists->children() as $dropdown) {
                                    $class_name = strval($dropdown->attributes()->classname);
                                    $function = strval($dropdown->attributes()->function);
                                    $tmp_class = new $class_name();
                                    if (method_exists($tmp_class, $function)) {
                                        $params = array(
                                            'idname' => strval($dropdown->attributes()->idname),
                                            'label' => strval($dropdown->attributes()->label),
                                            'b64_form_fields' => explode(',', strval($plugin->b64_form_fields)),
                                            'tpl_id' => $tpl_id,
                                            'url_tpl' => content_class::gen_url_template($tpl_id),
                                            'admintpl' => strval($plugin->tpl));
                                        $PLUGIN['result'][strval($dropdown->attributes()->name)] = $tmp_class->$function($params);
                                        $PLUGIN['params'] = $params;
                                        $ADMIN_LINK = $this->db->query_first("SELECT * FROM " . TBL_CMS_MENU . " WHERE mod_ident='" . $module->settings->id . "' LIMIT 1");
                                        $PLUGIN['admin_link'] = $ADMIN_LINK['php'];
                                    }
                                    $this->allocate_memory($tmp_class);
                                }

                            }

                            if (isset($plugin->stdoptions)) {
                                $class_name = strval($plugin->stdoptions->attributes()->classname);
                                $function = strval($plugin->stdoptions->attributes()->function);
                                $tmp_class = new $class_name();
                                if (method_exists($tmp_class, $function)) {
                                    $PLUGIN['data'] = $tmp_class->$function();
                                    $PLUGIN['pluginobj'] = (object)$plugin;
                                }
                                $this->allocate_memory($tmp_class);
                            }
                            $PLUGIN['pluginobj'] = (object)$plugin;
                            return (array )$PLUGIN;
                        }
                    }
                }
            }
        }
        catch (kException $e) {
            die($e->get_error_message());
        }
    }


    /**
     * websites_class::add_std_page()
     * 
     * @param mixed $tm_cid
     * @param mixed $tm_tid
     * @param string $tm_content
     * @return
     */
    function add_std_page($tm_cid, $tm_tid, $tm_content = "") {
        $FORM = array(
            'tm_cid' => $tm_cid,
            'tm_tid' => $tm_tid,
            'tm_content' => $tm_content,
            'tm_type' => 'W',
            'tm_order' => 10,
            );
        $id = insert_table(TBL_CMS_TEMPMATRIX, $FORM);
    }

    /**
     * websites_class::replicatelang()
     * 
     * @param mixed $tid
     * @param mixed $langid
     * @return
     */
    function replicatelang($tid, $langid) {
        $T_OBJ = $this->db->query_first("SELECT *,T.id AS TID,TC.id AS TMCID FROM " . TBL_CMS_TEMPLATES . " T
	       LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . (int)$langid . " AND T.id=TC.tid)
	       WHERE T.id=" . (int)$tid . "
        ");
        $to_delete_content_matrix_ids = $replicate_matrix = array();
        if ($T_OBJ['TMCID'] > 0) {
            $ORGS = array();
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_cid=" . $T_OBJ['TMCID']);
            while ($row = $this->db->fetch_array_names($result)) {
                $ORGS[] = $row;
            }

            $result = $this->db->query("SELECT id FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_cid<>" . $T_OBJ['TMCID'] . " AND tm_tid=" . $T_OBJ['TID']);
            while ($row = $this->db->fetch_array_names($result)) {
                $to_delete_content_matrix_ids[] = $row['id'];
            }

            $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_cid<>" . $T_OBJ['TMCID'] . " AND tm_tid=" . $T_OBJ['TID']);
            $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE id<>" . (int)$langid);
            while ($row = $this->db->fetch_array_names($result)) {
                $T_OBJ['lang_id'] = $row['id'];
                $T_OBJ = $this->real_escape($T_OBJ);
                $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . $T_OBJ['lang_id'] . " AND tid=" . $T_OBJ['TID'] . "");
                $arr = $this->load_tpl_content($tid, $langid);
                $arr = $this->real_escape($arr);
                $arr['lang_id'] = $row['id'];
                $tm_cid_old = $val['id'];
                unset($arr['id']);
                $tm_cid = insert_table(TBL_CMS_TEMPCONTENT, $arr);
                foreach ($ORGS as $key => $val) {
                    $val['tm_cid'] = $tm_cid;
                    $val = $this->real_escape($val);
                    $content_matrix_id_old = $val['id'];
                    unset($val['id']);
                    $content_matrix_id = insert_table(TBL_CMS_TEMPMATRIX, $val);
                    $upt = array('tm_content' => '{TMPL_FLXTPL_' . $content_matrix_id . '}');
                    update_table(TBL_CMS_TEMPMATRIX, 'id', $content_matrix_id, $upt);
                    $replicate_matrix[] = array(
                        'cid_to' => $tm_cid,
                        'cid_from' => $tm_cid_old,
                        'content_matrix_id_from' => $content_matrix_id_old,
                        'content_matrix_id_to' => $content_matrix_id);
                }
                #   $this->save_tpl_file($T_OBJ);
            }
        }
        $params = exec_evt('OnReplicateContent', array(
            'tid' => $tid,
            'langid' => $langid,
            'template' => $T_OBJ,
            'to_delete_content_matrix_ids' => $to_delete_content_matrix_ids,
            'replicate_matrix' => $replicate_matrix), $this);
    }

    /**
     * websites_class::load_tpl_content()
     * 
     * @param mixed $tid
     * @param mixed $langid
     * @return
     */
    function load_tpl_content($tid, $langid) {
        return $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . (int)$langid . " AND tid=" . (int)$tid);
    }

    /**
     * websites_class::cmd_replicatelang()
     * 
     * @return
     */
    function cmd_replicatelang() {
        $this->replicatelang($_GET['id'], $_GET['uselang']);
        $this->ej();
    }

    /**
     * websites_class::cmd_replicatealllang()
     * 
     * @return
     */
    function cmd_replicatealllang() {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE gbl_template=0 AND c_type='T'");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->replicatelang($row['id'], $_POST['langid']);
        }
        $this->hard_exit();
    }

    /**
     * websites_class::cmd_searchreplace()
     * 
     * @return
     */
    function cmd_searchreplace() {
        $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_content = REPLACE(tm_content, '" . $_POST['FORM']['word'] . "', '" . $_POST['FORM']['rword'] . "')");
        $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content = REPLACE(content, '" . $_POST['FORM']['word'] . "', '" . $_POST['FORM']['rword'] . "')");
        $T = new template_class();
        $T->rewrite_all_smarty_tpl();
        $this->hard_exit();
    }


    /**
     * websites_class::cmd_search()
     * 
     * @return
     */
    function cmd_search() {
        $k = 0;
        $FORM = (array )$_POST['FORM'];
        if ($FORM['word'] != "") {
            if ($_GET['show_active'] == 1) {
                $result = $this->db->query("SELECT C.*, T.description,T.id AS TID,T.c_type AS TT,L.bild, T.gbl_template,T.approval
        FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_LANG . " L , " . TBL_CMS_TEMPMATRIX . " M
        WHERE L.id=C.lang_id AND C.tid=T.id AND M.tm_tid=T.id AND T.gbl_template=0 AND T.approval=1 AND T.gbl_template=0  AND T.c_type='T'     
        GROUP BY T.id 
        ORDER BY T.description");
            }
            else {
                $result = $this->db->query("SELECT C.*, T.description,T.id AS TID,T.c_type AS TT,L.bild, T.gbl_template,T.approval
        FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C, " . TBL_CMS_LANG . " L , " . TBL_CMS_TEMPMATRIX . " M
        WHERE L.id=C.lang_id AND C.tid=T.id AND M.tm_tid=T.id AND T.gbl_template=0 AND 
        (C.content LIKE '%" . $FORM['word'] . "%' 
            OR M.tm_content LIKE '%" . $FORM['word'] . "%'
           )
        GROUP BY T.id 
        ORDER BY T.description");
            }

            while ($row = $this->db->fetch_array_names($result)) {
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
                    $row['app_icon'] = kf::gen_approve_icon($row['TID'], $row['approval']);
                }
                if ($row['TT'] == 'T' && $row['gbl_template'] == 0) {
                    $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=websitemanager.inc&uselang=' . $row['lang_id'] . '&tl=' . $row['tl'], 'edit', 'id', 'run.php');
                    $edit_link = 'run.php?epage=websitemanager.inc&aktion=edit&uselang=' . $row['lang_id'] . '&tl=' . $row['tl'] . '&id=' . $row['TID'];
                    $row['app_icon'] = kf::gen_approve_icon($row['TID'], $row['approval']);
                }
                if ($row['TT'] == 'T' && $row['gbl_template'] == 1) {
                    $edit_icon = kf::gen_edit_icon($row['TID'], '&epage=gbltemplates.inc&uselang=' . $row['lang_id'] . '', 'edit', 'id', 'run.php');
                    $edit_link = 'run.php?epage=gbltemplates.inc&aktion=edit&uselang=' . $row['lang_id'] . '&id=' . $row['TID'];
                }


                $word_arr = array();
                if (isset($FORM['word']))
                    $word_arr = explode($FORM['word'], $row['content'] . $row['tm_content']);
                $row['word_arr'] = $word_arr;
                $row['edit_link'] = $edit_link;
                $row['edit_icon'] = $edit_icon;
                $row['label'] = $label;
                $row['foundcount'] = count($word_arr);
                $row[thumb] = kf::gen_thumbnail('/images/' . $row['bild'], 20, 20);
                $res_tab[] = $row;
            }
            $searchresult = array('res_tab' => $res_tab, 'totalfound' => count($res_tab));
        }
        $this->smarty->assign('searchresult', $searchresult);
        $this->parse_to_smarty();
        kf::echo_template('website.searchres');
    }

    /**
     * websites_class::cmd_writealltpls()
     * 
     * @return
     */
    function cmd_writealltpls() {
        $T = new template_class();
        $T->rewrite_all_smarty_tpl();
        $this->msg('{LBLA_SAVED}');
        $this->TCR->set_just_turn_back(true);
    }

    /**
     * websites_class::gen_js_boxen()
     * 
     * @return
     */
    function gen_js_boxen() {
        /*  $result = $this->db->query("SELECT D.*,SUM(L.hits) AS TOTAL FROM " . TBL_CMS_DOWNCENTER . " D LEFT JOIN " . TBL_CMS_DC_LOG .
        " L ON (D.id = L.dcid) GROUP BY D.file ORDER BY D.file");
        while ($row = $this->db->fetch_array_names($result)) {
        $dbox .= '<option value="' . gen_download_url($row['id']) . '">' . $row['file'] . '</option>';
        }
        if ($dbox != "")
        $js_boxen .= '<div style="float:left;margin-left:10px;"><h3>Download Center</h3><select class="form-control" class="jsboxclick" size="6">' . $dbox .
        '</select><br><input type="text" class="form-control" value="" name="dbox_text" id="dbox_text"></div>';
        */
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='T' AND gbl_template=0 ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            #  $urlbox .= '<option value="' . content_class::gen_url_template($row['id']) . '">' . $row['description'] . '</option>';
            $url_arr[] = array('lvalue' => $row['description'], 'lkey' => content_class::gen_url_template($row['id']));
        }
        # if ($urlbox != "")
        #     $js_boxen .= '<div style="float:left;margin-left:10px;"><h3>Links</h3><select class="form-control" class="jsboxclick" size="6">' . $urlbox .
        #         '</select><br><input type="text" class="form-control" value="" name="urlbox_text" id="urlbox_text"></div>';
        # $this->WEBSITE['js_boxen'] = $js_boxen;
        $this->WEBSITE['boxes']['websitelinks'] = (array )$url_arr;
    }

    /**
     * websites_class::initmanager()
     * 
     * @return
     */
    function initmanager() {
        global $LNGOBJ;
        $this->nodes = new cms_tree_class();
        $this->nodes->db = $this->db;
        $this->nodes->create_result_and_array("SELECT id, parent, description,approval FROM " . TBL_CMS_TEMPLATES .
            " WHERE c_type='T' AND gbl_template=0 ORDER BY parent,morder", 0, 0, -1);
        $this->load_plugins();
        $this->gen_js_boxen();
        $this->load_websites($_SESSION['toplevel'], $_REQUEST['starttree']);
        $this->WEBSITE['IFORM'] = array('parent_select' => $this->nodes->output_as_selectbox('FORM[parent]', '', $_SESSION['last_parent'], 0, 'ROOT'));
        $this->WEBSITE['langselect'] = $LNGOBJ->build_lang_select_smarty($_GET['uselang']);
        unset($_SESSION['last_parent']);
    }

    /**
     * websites_class::get_all_hotspots()
     * 
     * @param mixed $langid
     * @param mixed $framework_id
     * @return
     */
    function get_all_hotspots($langid, $framework_id) {
        $framework_id = ($framework_id == 0) ? 1 : (int)$framework_id;
        $TPL = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $framework_id);
        $TPL['gui_frame'] = ($TPL['gui_frame'] == 0) ? 1 : (int)$TPL['gui_frame'];
        $FW = $this->db->query_first("SELECT * FROM " . TBL_CMS_FRAMEWORKS . " WHERE fw_number=" . $TPL['gui_frame']);
        foreach ($FW as $key => $value) {
            $arr[$value] = $key;
        }
        $gbl_template = get_template($framework_id, $langid);
        $order = array(
            'fw_header',
            'fw_left',
            'fw_main',
            'fw_right',
            'fw_footer');
        foreach ($order as $value)
            $num_arr[$value] = array();
        if (strstr($gbl_template, '{TMPL_SPOT_')) {
            preg_match_all("={TMPL_SPOT_(.*)}=siU", $gbl_template, $tpl_tag);
            foreach ($tpl_tag[0] as $key => $spot) {
                $tmp = explode('_', $spot);
                $spot_num = (int)$tmp[count($tmp) - 1];
                $num_arr[$arr[$spot_num]] = array('number' => $spot_num, 'fw_pos' => $arr[$spot_num]);
            }
        }
        foreach ($num_arr as $key => $value) {
            if (count($value) == 0)
                unset($num_arr[$key]);
        }
        $this->WEBSITE['hotspots'] = (array )$num_arr;
        return (array )$num_arr;
    }

    /**
     * websites_class::build_parent_content()
     * 
     * @param mixed $tid
     * @param mixed $tm_pos
     * @param mixed $arr
     * @param mixed $TMID
     * @return
     */
    function build_parent_content($tid, $tm_pos, &$arr, $TMID) {
        $result = $this->db->query("SELECT T.*,IC.*, IC.id AS TMID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " IC ," . TBL_CMS_TEMPCONTENT . " TC
                WHERE TC.id=IC.tm_cid AND T.id=TC.tid AND T.id=IC.tm_tid AND T.id=" . (int)$tid . " AND TC.lang_id=" . $this->langid . " AND IC.tm_pos=" . (int)
            $tm_pos . "
                ORDER BY tm_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $row['heredity'] = true;
            $row['TMID'] = $TMID;
            if ($row['tm_type'] != 'H') {
                $arr[] = $row;
            }
            else {
                if ($row['tm_parent'] > 0 && count($arr) == 0) {
                    $this->build_parent_content($row['tm_parent'], $tm_pos, $arr, TMID);
                }
            }
        }
    }

    /**
     * websites_class::cmd_approvematrixitem()
     * 
     * @return
     */
    function cmd_approvematrixitem() {
        list($tmp, $id) = explode('-', $this->TCR->GET['id']);
        $this->db->query("UPDATE " . TBL_CMS_TEMPMATRIX . " SET tm_approved='" . (int)$_GET['value'] . "' WHERE id='" . (int)$id . "' LIMIT 1");
        $this->hard_exit();
    }


    /**
     * websites_class::load_contentmatrix()
     * 
     * @param mixed $tm_cid
     * @return
     */
    function load_contentmatrix($tm_cid) {
        $PAGE = $this->db->query_first("SELECT T.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " IC  WHERE T.id=IC.tid AND IC.id=" . (int)$tm_cid .
            " LIMIT 1");
        $hotspots = $this->get_all_hotspots($this->langid, $PAGE['use_framework']);
        foreach ($hotspots as $key => $spot) {
            $this->WEBSITE['content_table']['hotspot-' . $spot['fw_pos']] = array();
        }
        $result = $this->db->query("SELECT *, IC.id AS TMID,T.id AS TID 
                FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " IC 
                WHERE T.id=IC.tm_tid AND IC.tm_cid=" . (int)$tm_cid . " 
                ORDER BY tm_pos,tm_order");
        while ($row = $this->db->fetch_array_names($result)) {
            $TID = $row['TID'];
            $row['icons'][] = kf::gen_approve_icon($row['TMID'], $row['tm_approved'], 'approvematrixitem');
            if ($row['tm_type'] != 'H') {
                $row['heredity'] = false;
                $this->WEBSITE['content_table']['hotspot-' . $row['tm_pos']][] = $row;
                if ($this->WEBSITE['content_parent'][$row['tm_pos']] != true)
                    $this->WEBSITE['content_parent'][$row['tm_pos']] = false;
            }
            else {
                $this->WEBSITE['content_parent'][$row['tm_pos']] = true;
                $arr = array();
                $this->build_parent_content($row['tm_parent'], $row['tm_pos'], $arr, $row['TMID']);
                $this->WEBSITE['content_table']['hotspot-' . $row['tm_pos']] = array_merge((array )$this->WEBSITE['content_table']['hotspot-' . $row['tm_pos']], $arr);
            }
        }
        $this->WEBSITE['TMCID'] = $tm_cid;
        $this->WEBSITE['TMTID'] = $TID;
    }

    /**
     * websites_class::cmd_setheredity()
     * 
     * @return
     */
    function cmd_setheredity() {
        if ($_GET['status'] == 1) {
            $T_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T WHERE TC.tid=T.id AND  TC.id=" . $_GET['tm_cid']);
            if ($T_OBJ['parent'] == 0) {
                $T_OBJ['parent'] = 1;
            }
            $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_type='H' AND tm_pos=" . $_GET['tm_pos'] . " AND tm_cid=" . $_GET['tm_cid']);
            $FORM = array(
                'tm_tid' => $T_OBJ['tid'],
                'tm_cid' => $_GET['tm_cid'],
                'tm_type' => 'H',
                'tm_order' => '0',
                'tm_pos' => $_GET['tm_pos'],
                'tm_parent' => $T_OBJ['parent']);
            insert_table(TBL_CMS_TEMPMATRIX, $FORM);
            $this->resaved_sorted($FORM['tm_cid'], $FORM['tm_pos']);
        }
        else {
            $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_type='H' AND tm_pos=" . $_GET['tm_pos'] . " AND tm_cid=" . $_GET['tm_cid']);
        }
        $this->reload_contentm($_GET['tm_cid']);
    }


    /**
     * websites_class::cmd_startseo()
     * 
     * @return
     */
    function cmd_startseo() {
        $FORM = $this->db->query_first("SELECT TC.* FROM " . TBL_CMS_TEMPCONTENT . " TC, " . TBL_CMS_TEMPLATES . " T WHERE TC.tid=T.id AND TC.id='" . $_REQUEST['conid'] .
            "' LIMIT 1");

        $url = self::get_domain_url() . 'index.php?page=' . $FORM['tid'];
        $start = $this->get_micro_time();
        $data = $this->curl_get_data($url);
        $ende = $this->get_micro_time();
        $SICRAWLER = new htmlcrawl_class();
        $SICRAWLER->inhalt = $data;
        $SICRAWLER->gen_meta_keywords_se($meta_keywords);
        $SICRAWLER->extract_meta_tags();
        $SICRAWLER->get_bonus_tags_count();
        $SEO = $SICRAWLER->SEO;
        $SEO = array_merge($SEO, array(
            'wordscores' => $SICRAWLER->wordscores,
            'metas' => $SICRAWLER->metas,
            'loadtime' => number_format($ende - $start, 4, ",", ".")));
        $this->smarty->assign('SEO', $SEO);
        kf::echo_template('website.seo.result');
    }

    /**
     * websites_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        global $GRAPHIC_FUNC;
        $pageid = (int)$_GET['id'];

        # validate acces to page by admin group
        $page_access = new pageaccess_class();
        if (!$page_access->page_access_valid($pageid, $_SESSION['admin_obj']['GROUPID'])) {
            if ($_REQUEST['axcall'] != 1) {
                header("location:welcome.html");
                exit();
            }
            else {
                kf::echo_template('no_permissions.admin');
            }
        }

        $this->initmanager();
        $this->load_website((int)$pageid);
        $this->TEMPL_OBJ->build_lang_select();

        $result = $this->db->query("SELECT * FROM " . TBL_CMS_CUSTGROUPS . " ORDER BY groupname");
        while ($row = $this->db->fetch_array_names($result)) {
            $perm_checkoxes .= '<div class="checkbox"><label><input type="checkbox" ' . ((get_data_count(TBL_CMS_PERMISSIONS, 'perm_tid', "perm_tid=" . (int)$pageid .
                " AND perm_group_id=" . $row['id']) > 0) ? 'checked' : '') . ' name="CUSTGROUP[' . $row['id'] . ']" value="' . $row['id'] . '"> ' . (($row['id'] == 1000) ?
                '<b>' : '') . $row['groupname'] . (($row['id'] == 1000) ? '</b>' : '') . '</label></div>';
        }
        $this->WEBSITE['permboxes'] = $perm_checkoxes;


        #ermittle markierte Topl-Level
        $topl_found = false;
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TOPLEVEL . " ORDER BY morder,description");
        while ($row = $this->db->fetch_array_names($result)) {
            $toplevel_checkoxes .= '<div class="checkbox"><label><input type="checkbox" ' . ((in_array($pageid, explode(';', $row['trees']))) ? 'checked' : '') . ' name="TLACTIVE[' . $row['id'] .
                ']" value="' . $row['id'] . '"> ' . $row['description'] . '</label></div>';
            if (in_array($pageid, explode(';', $row['trees'])))
                $topl_found = true;
        }
        if ($this->TEMPL_OBJ->template['parent'] > 0) {
            $toplevel_checkoxes = "";
            $topl_found = true;
        }
        $this->WEBSITE['toplevel_box'] = $toplevel_checkoxes;

        if ($this->TEMPL_OBJ->template['approval'] == 0)
            $info_box .= '- {LBL_NOGREENHOOK}<br>';
        if ($this->TEMPL_OBJ->template['formcontent']['linkname'] == "")
            $info_box .= '- {LBLA_TITLE} {LBL_NOTFILEDOUT}<br>';
        if ($topl_found == false)
            $info_box .= '- {LBL_NOTOPLEVELCHECKED}<br>';
        $theme_image = "";
        if ($this->TEMPL_OBJ->template['formcontent']['theme_image'] != "") {
            $theme_image = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../file_data/themeimg/' . $this->TEMPL_OBJ->template['formcontent']['theme_image'], 100,
                30, 'admin/' . CACHE, true, 'resize');
            $theme_image_big = PATH_CMS . 'admin/' . CACHE . $GRAPHIC_FUNC->makeThumb('../file_data/themeimg/' . $this->TEMPL_OBJ->template['formcontent']['theme_image'], $this->
                TEMPL_OBJ->template['formcontent']['t_tiwidth'], $this->TEMPL_OBJ->template['formcontent']['t_tiheight'], 'admin/' . CACHE, true, 'crop', '', '', $this->
                TEMPL_OBJ->template['formcontent']['t_ticroppos']);
        }


        $webopt = array('{LBL_MODULE_TYP}' => kf::build_module_select($this->TEMPL_OBJ->template['module_id']));


        if ($this->TEMPL_OBJ->template['gbl_template'] == 0) {
            $webopt['{LBL_TREEPOSITION}'] = $this->nodes->output_as_selectbox('FORM_TEMPLATE[parent]', $this->TEMPL_OBJ->template['id'], $this->TEMPL_OBJ->template['parent'],
                '0', 'ROOT');
        }

        $this->nodes->CreateNestedArray($this->nodes->data, $this->nodes->menue_arr, 0, 0, -1);
        $node = $this->nodes->getOneNode($this->TEMPL_OBJ->template['id'], $this->nodes->menue_arr);

        $WSOBJ = array(
            'info_box' => $info_box,
            'js_boxen' => $js_boxen,
            'theme_image' => $theme_image,
            'theme_image_big' => $theme_image_big,
            'webopt' => $webopt,
            'selectedmodul' => $_SESSION['modul_selected'],
            'node' => $node,
            'quickjump' => $this->nodes->output_as_selectbox_jumb('id', 0, $this->TEMPL_OBJ->template['id'], 0, '', '&aktion=edit&epage=' . $_GET['epage'], ''),
            'entrypoint' => ((is_array($node['children']) == true) ? $this->nodes->output_as_selectbox_only_childs('FORM_TEMPLATE[first_page]', $this->TEMPL_OBJ->template['id'],
                $this->TEMPL_OBJ->template['first_page'], '0', '-NONE-') : '<input type="hidden" name="FORM_TEMPLATE[first_page]" value="0">-NONE-'),

            );
        $params = array('tplid' => (int)$pageid, 'template' => $this->TEMPL_OBJ->template);
        $params = exec_evt('OnPageEdit', $params, $this);
        $this->smarty->assign('WSOBJ', $WSOBJ);
    }

    /**
     * websites_class::cmd_page_axedit()
     * 
     * @return
     */
    function cmd_page_axedit() {
        # $this->initmanager();
        $this->cmd_edit();
        $this->parse_to_smarty();
        kf::echo_template('website.editor');
    }


}
