<?php

/**
 * @package    inlay
 *
 * @copyright  Copyright (C) Trebaxa GmbH&Co.KG. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE
 * @version    1.0
 */


class inlay_class extends modules_class {

    var $TEMPL_OBJ = array();
    var $langid = array();
    var $CMSDATA = array();

    /**
     * inlay_class::__construct()
     * 
     * @return
     */
    function __construct() {
        parent::__construct();
        $this->TCR = new kcontrol_class($this);
        $this->INLAY = array();
    }

    /**
     * inlay_class::init()
     * 
     * @return
     */
    function init() {
        $this->TEMPL_OBJ = new template_class($this->langid, $this->DATA);
        $LNGOBJ = new language_class();
        $LNGOBJ->init_uselang();
        $this->smarty->assign('langselect', $LNGOBJ->build_lang_select_smarty($_REQUEST['uselang']));
    }

    /**
     * inlay_class::set_lang_id()
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
     * inlay_class::cmd_a_new()
     * 
     * @return
     */
    function cmd_a_new() {
        $id = $this->create_inlay($_POST['FORM']);
        $this->msg("{LBLA_SAVED}");
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&id=' . $id . '&cmd=edit');
        $this->hard_exit();
    }


    /**
     * inlay_class::cmd_ax_create_inlay()
     * 
     * @return
     */
    function cmd_ax_create_inlay() {
        $id = $this->create_inlay($_REQUEST['FORM']);
        echo json_encode(array('id' => $id));
        $this->hard_exit();
    }

    /**
     * inlay_class::cmd_edit()
     * 
     * @return
     */
    function cmd_edit() {
        $this->set_lang_id($this->TCR->REQUEST['uselang']);
        $this->load_inlay($this->TCR->REQUEST['id'], $this->langid);
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='T' AND gbl_template=0 ORDER BY description");
        while ($row = $this->db->fetch_array_names($result)) {
            $this->INLAY['hplinks'][] = array('link' => content_class::gen_url_template($row['id']), 'label' => $row['description']);
        }
    }

    /**
     * inlay_class::cmd_ax_edit()
     * 
     * @return
     */
    function cmd_ax_edit() {
        $this->cmd_edit();
        if ((int)$_GET['axcall'] == 1) {
            $this->parse_to_smarty();
            kf::echo_template('inlaymanager');
        }
    }

    /**
     * inlay_class::del_inlay()
     * 
     * @param mixed $id
     * @return
     */
    function del_inlay($id) {
        $id = (int)$id;
        $T_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id . " LIMIT 1");

        // removing joker from templates
        $result = $this->db->query("SELECT C.* FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C WHERE C.tid=T.id");
        while ($row = $this->db->fetch_array_names($result)) {
            $html = $row['content'];
            if (strstr($html, $T_OBJ['block_name'])) {
                $html = str_replace($T_OBJ['block_name'], "", $html);
                $html = str_replace('<% include file="' . str_replace(array('{', '}'), '', $T_OBJ['block_name']) . '.tpl" %>', "", $html);

                $this->db->query("UPDATE " . TBL_CMS_TEMPCONTENT . " SET content='" . $this->db->real_escape_string($html) . "' WHERE id='" . $row['id'] . "' LIMIT 1");
            }
        }
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid=" . $id);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPLATES . " WHERE id=" . $id . " LIMIT 1");
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_refid=" . $id);
        $this->LOGCLASS->addLog('DELETE', 'inlay ' . $T_OBJ['description']);
    }

    /**
     * inlay_class::cmd_replicate_lang()
     * 
     * @return
     */
    function cmd_replicate_lang() {
        $langid = (int)$_POST['langid'];
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_LANG . " WHERE id<>" . $langid);
        while ($row = $this->db->fetch_array_names($result)) {
            $result2 = $this->db->query("SELECT *, C.id AS CONID, T.id AS TID 
             FROM " . TBL_CMS_TEMPLATES . " T LEFT JOIN " . TBL_CMS_TEMPCONTENT . " C ON (T.id=C.tid AND C.lang_id=" . $row['id'] . ")
             WHERE T.c_type='B' ORDER BY T.id");
            while ($TOUPDATE = $this->db->fetch_array_names($result2)) {
                $ORGTEMPALTE = $this->db->query_first("SELECT C.* 
             FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C
             WHERE T.id=C.tid AND T.c_type='B' AND C.lang_id='" . $langid . "' AND T.id=" . $TOUPDATE['TID'] . " LIMIT 1");
                $UPDATE = $ORGTEMPALTE;
                $UPDATE['lang_id'] = $TOUPDATE['lang_id'];
                unset($UPDATE['id']);
                foreach ($UPDATE as $key => $value)
                    $UPDATE[$key] = $this->db->real_escape_string($value);

                if ($TOUPDATE['CONID'] > 0) {
                    update_table(TBL_CMS_TEMPCONTENT, 'id', $TOUPDATE['CONID'], $UPDATE);
                }
                else {
                    insert_table(TBL_CMS_TEMPCONTENT, $UPDATE);
                }
            }
        }
        include_once (CMS_ROOT . 'admin/inc/update.class.php');
        $upt_obj = new upt_class();
        $upt_obj->rewriteSmartyTPL();
        $this->hard_exit();
    }

    /**
     * inlay_class::create_inlay()
     * 
     * @param mixed $FORM
     * @return
     */
    function create_inlay($FORM) {
        $FORM['description'] = strip_tags($FORM['description']);
        $FORM['description'] = 'node';
        $last_id = insert_table(TBL_CMS_TEMPLATES, $FORM);
        $A_OBJ['block_name'] = strtolower('{inlay_' . $this->format_file_name($FORM['description']) . '_' . $last_id . '}');
        $A_OBJ['c_type'] = 'B';
        update_table(TBL_CMS_TEMPLATES, 'id', $last_id, $A_OBJ);
        // Lege Uebersetzung fuer alle Sprachen an
        $FORMC = array();
        $FORMC['linkname'] = $FORM['description'];
        $FORMC['tid'] = $last_id;
        foreach ($this->DATA->LANGS as $langid => $value) {
            $FORMC['lang_id'] = (int)$langid;
            insert_table(TBL_CMS_TEMPCONTENT, $FORMC);
            $T_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $FORMC['lang_id'] . " AND T.id=TC.tid)
		WHERE T.id=" . $last_id . "
		");
            $this->TEMPL_OBJ->save_tpl_file($T_OBJ);
        }
        $this->LOGCLASS->addLog('INSERT', 'inlay <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_REQUEST['epage'] . '&cmd=edit&id=' . $last_id . '">' . $FORM['description'] .
            '</a>');
        return $last_id;
    }


    /**
     * inlay_class::load_inlays()
     * 
     * @return
     */
    function load_inlays() {
        $this->TEMPL_OBJ->load_template_list(0, 'B');
        $this->inlay_list = $this->TEMPL_OBJ->tab;
        $this->smarty->assign('inlay_list', $this->inlay_list);
    }

    /**
     * inlay_class::load_inlay()
     * 
     * @param mixed $id
     * @param mixed $langid
     * @return
     */
    function load_inlay($id, $langid) {
        $this->TEMPL_OBJ->load_template((int)$id);
        $this->TEMPL_OBJ->template['formcontent'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . (int)$id . "' AND lang_id='" . $langid .
            "' LIMIT 1");
        $this->TEMPL_OBJ->template['no_fck'] = (strstr($this->TEMPL_OBJ->template['formcontent']['content'], '<%') && strstr($this->TEMPL_OBJ->template['formcontent']['content'],
            '%>')) ? 1 : $this->TEMPL_OBJ->template['no_fck'];

        $this->TEMPL_OBJ->template['oeditor'] = (($this->TEMPL_OBJ->template['no_fck'] == 0) ? create_html_editor('FORMCON[content]', $this->TEMPL_OBJ->template['formcontent']['content'],
            900) : '<textarea class="form-control" id="templcontent" class="se-html" rows="60" style="width:100%;" name="FORMCON[content]">' . trim(htmlspecialchars($this->
            TEMPL_OBJ->template['formcontent']['content'])) . '</textarea>');
        $this->smarty->assign('TPLOBJ', $this->TEMPL_OBJ->template);
        return $this->TEMPL_OBJ->template;
    }

    /**
     * inlay_class::load_inlay_fe()
     * 
     * @param mixed $id
     * @param mixed $langid
     * @return
     */
    function load_inlay_fe($id, $langid) {
        $tpl = $this->db->query_first("SELECT T.*, TC.t_htalinklabel FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " TC WHERE T.id=TC.tid AND T.id='" . (int)
            $id . "' AND TC.lang_id=" . $langid . " LIMIT 1");

        $tpl['formcontent'] = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . (int)$id . "' AND lang_id='" . $langid . "' LIMIT 1");
        return $tpl;
    }

    /**
     * inlay_class::cmd_save_inlay()
     * 
     * @return
     */
    function cmd_save_inlay() {
        $FORM = $this->TCR->POST['FORM_TEMPLATE'];
        $FORMCONTENT = $this->TCR->POST['FORMCON'];
        $tempalte_id = (int)$this->TCR->POST['tid'];
        $FORM['approval'] = (int)$FORM['approval'];
        $FORM['description'] = strip_tags($FORM['description']);
        update_table(TBL_CMS_TEMPLATES, 'id', (int)$tempalte_id, $FORM);

        $FORMCONTENT['content'] = str_ireplace('<% $PATH_CMS %>', PATH_CMS, $FORMCONTENT['content']);
        $FORMCONTENT['content'] = str_ireplace('<%$PATH_CMS%>', PATH_CMS, $FORMCONTENT['content']);
        $C = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " 
        WHERE tid=" . $tempalte_id . " 
        AND lang_id=" . $FORMCONTENT['lang_id']);
        if ($C['id'] > 0) {
            update_table(TBL_CMS_TEMPCONTENT, 'id', $C['id'], $FORMCONTENT);
        }
        else {
            $FORMCONTENT['tid'] = $tempalte_id;
            insert_table(TBL_CMS_TEMPCONTENT, $FORMCONTENT);
        }

        $T_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPLATES . " T
		LEFT JOIN " . TBL_CMS_TEMPCONTENT . " TC ON (TC.lang_id=" . $FORMCONTENT['lang_id'] . " AND T.id=TC.tid)
		WHERE T.id=" . $tempalte_id . "
		");
        $this->TEMPL_OBJ->save_tpl_file($T_OBJ);
        $this->LOGCLASS->addLog('MODIFY', 'inlay <a href="' . $_SERVER['PHP_SELF'] . '?epage=' . $_POST['epage'] . '&uselang=' . $FORMCONTENT['lang_id'] .
            '&cmd=edit&id=' . $tempalte_id . '">' . $FORM['description'] . '</a>');
        $this->hard_exit();
    }

    /**
     * inlay_class::approve()
     * 
     * @param mixed $value
     * @param mixed $id
     * @return
     */
    function approve($value, $id) {
        $this->db->query("UPDATE " . TBL_CMS_TEMPLATES . " SET approval=" . (int)$value . " WHERE id=" . (int)$id . " LIMIT 1");
    }

    /**
     * inlay_class::cmd_axapprove_item()
     * 
     * @return
     */
    function cmd_axapprove_item() {
        $this->approve($this->TCR->GET['value'], $_GET['ident']);
        $this->hard_exit();
    }

    /**
     * inlay_class::msave()
     * 
     * @param mixed $FORM
     * @return
     */
    function msave($FORM) {
        if (is_array($FORM) && count($FORM) > 0) {
            foreach ($FORM as $id => $FA) {
                $FA['description'] = strip_tags($FA['description']);
                update_table(TBL_CMS_TEMPLATES, 'id', (int)$id, $FA);
            }
        }
    }

    /**
     * inlay_class::import_template()
     * 
     * @param mixed $id
     * @return
     */
    function import_template($id) {
        $this->TEMPL_OBJ->import_template($id);
    }


    #*********************************
    # FILL IN INLAYS
    #*********************************
    /**
     * inlay_class::fillin_inlays()
     * 
     * @param mixed $html_str
     * @return
     */
    function fillin_inlays($html_str) {
        $smarty_v = array();
        $result = $this->db->query("SELECT *,T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T
	LEFT JOIN " . TBL_CMS_TEMPCONTENT . " C ON (C.tid=T.id AND C.lang_id='" . $this->langid . "')
	WHERE T.c_type='B'");

        while ($row = $this->db->fetch_array_names($result)) {
            if ($row['content'] == '') {
                $TC_OBJ = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPCONTENT . " WHERE tid='" . $row['TID'] . "' AND use_all_lang=1");
                $row['content'] = $TC_OBJ['content'];
            }
            if ($row['approval'] == 1) {
                $html_str = fill_temp($row['block_name'], '<% include file="' . str_replace(array('{', '}'), '', $row['block_name']) . '.tpl" %>', $html_str);
                $smarty_v[str_replace(array('{', '}'), '', $row['block_name'])] = $row['content'];
            }
            else {
                $html_str = fill_temp($row['block_name'], "", $html_str);
                $html_str = str_replace('<% include file="' . str_replace(array('{', '}'), '', $row['block_name']) . '.tpl" %>', '', $html_str);
                $smarty_v[str_replace(array('{', '}'), '', $row['block_name'])] = "";
            }
        }
        $this->smarty->assign('INLAYS', $smarty_v);
        return $html_str;
    }

    /**
     * inlay_class::delete_lang_content()
     * 
     * @param mixed $params
     * @return
     */
    function delete_lang_content($params) {
        $id = (int)$params['id'];
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPCONTENT . " WHERE lang_id=" . (int)$id . " AND lang_id>1");
        return $params;
    }

    /**
     * inlay_class::parse_to_smarty()
     * 
     * @return
     */
    function parse_to_smarty() {
        $this->INLAY['langid'] = $this->langid;
        $this->smarty->assign('INLAY', $this->INLAY);
    }

    /**
     * inlay_class::inlay_connect_init()
     * 
     * @return
     */
    function inlay_connect_init() {
        $this->NESTED_ARR->init(array(
            'label_column' => 'description',
            'label_parent' => 'parent',
            'label_id' => 'id',
            'bread_break_sym' => '/'));
        $this->NESTED_ARR->create_result_and_array("SELECT id, parent, description,approval FROM " . TBL_CMS_TEMPLATES .
            " WHERE c_type='T' AND gbl_template=0 ORDER BY description", 0, 0, -1);
        $this->INLAY['website_tree'] = $this->NESTED_ARR->output_as_selectbox();
        #   $this->load_conn_table($this->TCR->REQUEST['id']);
    }

    /**
     * inlay_class::cmd_add_position()
     * 
     * @return
     */
    function cmd_add_position() {
        include_once (CMS_ROOT . 'admin/inc/websites.class.php');
        $FORM = $this->TCR->POST['FORM'];
        $INLAY = $this->db->query_first("SELECT TH.description,T.lang_id,T.content, T.id AS TMCID, TH.id AS TID 
        FROM " . TBL_CMS_TEMPCONTENT . " T, " . TBL_CMS_TEMPLATES . " TH 
        WHERE TH.id=T.tid  AND TH.id=" . $FORM['inlay_id']);

        $T = $this->db->query_first("SELECT T.description,C.*,C.id AS TMCID, T.id AS TID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C 
            WHERE C.lang_id=" . $INLAY['lang_id'] . " 
            AND C.tid=T.id 
            AND T.id=" . $FORM['tid']);
        $FORM = array(
            'tm_cid' => $T['TMCID'],
            'tm_tid' => $T['TID'],
            'tm_content' => '',
            'tm_type' => 'P',
            'tm_order' => 0,
            'tm_plugname' => 'Inlay',
            'tm_plugid' => 'html_inlay',
            'tm_pluginfo' => $INLAY['description'],
            'tm_refid' => $FORM['inlay_id']);
        $FORM = $this->real_escape($FORM);
        if ($FORM['i_pos'] == 2) { // unten
            $FORM['tm_order'] = 100000;
        }
        if ($FORM['tm_cid'] > 0) {
            $id = insert_table(TBL_CMS_TEMPMATRIX, $FORM);
            $W = new websites_class(array());
            $W->resaved_sorted($T['TMCID']);
        }

        kf::simple_output('<div class="bg-success">{LBLA_SAVED}</div>');
    }

    /**
     * inlay_class::cmd_reload_conn_table()
     * 
     * @return
     */
    function cmd_reload_conn_table() {
        $this->load_conn_table($this->TCR->REQUEST['id']);
        $this->parse_to_smarty();
        kf::simple_output('<% include file="inlay.conntable.tpl" %><script>set_del_func();</script>');
    }

    /**
     * inlay_class::load_conn_table()
     * 
     * @param mixed $id
     * @return
     */
    function load_conn_table($id) {
        $id = (int)$id;
        if ($id > 0) {
            $result = $this->db->query("SELECT *, IC.id AS MID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPMATRIX . " IC 
                WHERE T.id=IC.tm_tid AND IC.tm_refid=" . $id);
            while ($row = $this->db->fetch_array_names($result)) {
                $row['icons'][] = kf::gen_del_icon_ajax($row['MID'], false, 'axdel_conn');
                $this->INLAY['i_connections'][] = $row;
            }
        }
    }

    /**
     * inlay_class::cmd_axdel_conn()
     * 
     * @return
     */
    function cmd_axdel_conn() {
        $parts = explode('-', $this->TCR->GET['id']);
        $T = $this->db->query_first("SELECT * FROM " . TBL_CMS_TEMPMATRIX . " WHERE id=" . (int)$parts[1]);
        $this->db->query("DELETE FROM " . TBL_CMS_TEMPMATRIX . " WHERE tm_tid=" . $T['tm_tid'] . " AND tm_refid=" . (int)$T['tm_refid']);
        $this->hard_exit();
    }

    /**
     * inlay_class::load_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function load_homepage_integration($params) {
        $result = $this->db->query("SELECT * FROM " . TBL_CMS_TEMPLATES . " WHERE c_type='B' ORDER BY description");
        while ($row = $this->db->fetch_array($result)) {
            $row['LABEL'] = $row[$params['label']];
            $row['ID'] = $row[$params['idname']];
            $list[] = $row;
        }
        return (array )$list;
    }

    /**
     * inlay_class::save_homepage_integration()
     * 
     * @param mixed $params
     * @return
     */
    function save_homepage_integration($params) {
        $cont_matrix_id = (int)$params['id'];
        $templateid = (int)$params['FORM']['templateid'];
        $CM = $this->db->query_first("SELECT * FROM  " . TBL_CMS_TEMPMATRIX . " M, " . TBL_CMS_TEMPCONTENT . " C WHERE C.id=M.tm_cid AND M.id=" . $cont_matrix_id);
        $T = $this->db->query_first("SELECT T.description,C.*,C.id AS TMCID FROM " . TBL_CMS_TEMPLATES . " T, " . TBL_CMS_TEMPCONTENT . " C 
            WHERE C.lang_id=" . $CM['lang_id'] . " 
            AND C.tid=T.id 
            AND T.id=" . $templateid);
        $upt = array(
            'tm_content' => '',
            'tm_pluginfo' => $T['description'],
            'tm_refid' => $T['TMCID']);
        update_table(TBL_CMS_TEMPMATRIX, 'id', $cont_matrix_id, $this->real_escape($upt));
    }

    /**
     * inlay_class::cmd_load_inlay_tree()
     * 
     * @return
     */
    function cmd_load_inlay_tree() {
        $this->load_inlays();
        $this->parse_to_smarty();
        kf::echo_template('inlay.tree');
    }

    /**
     * inlay_class::cmd_rename_inlay()
     * 
     * @return
     */
    function cmd_rename_inlay() {
        $FORM = $_REQUEST['FORM'];
        update_table(TBL_CMS_TEMPLATES, 'id', $_GET['id'], $FORM);
        echo json_encode(array('id' => $tid));
        $this->hard_exit();
    }

    /**
     * inlay_class::cmd_ax_show_all()
     * 
     * @return
     */
    function cmd_ax_show_all() {
        $this->load_inlays();
        $this->parse_to_smarty();
        kf::echo_template('inlaymanager');
    }

    /**
     * inlay_class::cmd_axdelinlay()
     * 
     * @return
     */
    function cmd_axdelinlay() {
        $this->del_inlay($_GET['ident']);
        $this->msg("{LBL_DELETED}");
        $this->ej();
    }


    /**
     * inlay_class::cmd_a_import()
     * 
     * @return
     */
    function cmd_a_import() {
        $this->import_template($_GET['id']);
        $this->msg("{LBL_TEMPLATE} " . $_GET['id'] . " {LBL_IMPORTED}");
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&aktion=' . $_GET['oaktion'] . '&id=' . $_GET['id']);
        exit;
    }

    /**
     * inlay_class::cmd_a_msave()
     * 
     * @return
     */
    function cmd_a_msave() {
        $this->msave($_POST['FORM']);
        exit;
    }


    /**
     * inlay_class::cmd_replicateland()
     * 
     * @return
     */
    function cmd_replicateland() {
        $this->TEMPL_OBJ->replicateland($_GET['id'], $_GET['uselang']);
        $this->msg("{LBLA_SAVED}");
        HEADER('location:' . $_SERVER['PHP_SELF'] . '?epage=' . $_GET['epage'] . '&cmd=edit&id=' . $_GET['id'] . '&uselang=' . $_GET['uselang']);
        exit;
    }
}
